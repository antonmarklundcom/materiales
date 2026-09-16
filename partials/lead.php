<?php
/**
 * partials/lead.php — lógica del pipeline de leads (plan §3). SIN salida y SIN efectos HTTP:
 * todo lo que hace es transformar datos, hablar con VenderCRM y escribir el log. El shell
 * HTTP (validar, redirigir) vive en cotizar/enviar.php; el formulario en partials/form.php.
 *
 * Separado así a propósito: tools/smoke.php ejercita estas funciones directamente en CI
 * (teléfono, consentimiento, idempotencia, log) sin levantar un servidor.
 *
 * CONTRATO DEL PAYLOAD — es fundacional (plan §4.4). Cambiarlo después del lanzamiento
 * rompe la correlación de leads en el CRM: cualquier cambio es una parada, no una decisión
 * de fase. Lo que NUNCA se manda: pipeline, stage, owner, tag — el ruteo se configura en el
 * registro del sitio dentro de VenderCRM (skill vendercrm-lead-capture).
 */

declare(strict_types=1);

/** Segundos mínimos entre el render del formulario y el submit (trampa de tiempo, plan §3). */
const LEAD_MIN_SECONDS = 3;

/** Ventana máxima de validez del sello del formulario: 12 h. Más viejo = página fósil. */
const LEAD_STAMP_TTL = 43200;

/** Ventana entre envíos de una misma IP. */
const LEAD_IP_WINDOW_SECONDS = 60;

/**
 * config/vendercrm.php si existe. Nunca lanza: sin config el handler degrada a leads.log y
 * el visitante igual llega a /gracias/ (plan §4.5).
 */
function lead_config(): array
{
    static $config = null;
    if ($config === null) {
        $file = CONFIG_DIR . '/vendercrm.php';
        $loaded = is_file($file) ? require $file : [];
        $config = [
            'url'         => rtrim((string) ($loaded['url'] ?? ''), '/'),
            'api_key'     => (string) ($loaded['api_key'] ?? ''),
            'timeout'     => (int) ($loaded['timeout'] ?? 10),
            'form_secret' => (string) ($loaded['form_secret'] ?? ''),
        ];
    }
    return $config;
}

/** true si hay CRM al que postear. false ⇒ modo sólo-log, documentado en config.sample.php. */
function lead_crm_configured(): bool
{
    $config = lead_config();
    return $config['url'] !== '' && $config['api_key'] !== '';
}

/**
 * Normaliza un teléfono paraguayo a E.164, o null si no es plausible.
 *
 * Acepta lo que la gente escribe de verdad: "0981 123 456", "+595 981 123456",
 * "(021) 123-456", "595981123456". Deliberadamente permisivo: rechazar un lead real cuesta
 * plata, y el filtro fuerte es el repaso manual de cada lead en el CRM (plan §8.1).
 */
function lead_normalize_phone(string $raw): ?string
{
    $digits = preg_replace('/\D+/', '', $raw) ?? '';
    if ($digits === '') {
        return null;
    }

    // A "nacional": sin prefijo país (595) ni el 0 de larga distancia.
    if (str_starts_with($digits, '595')) {
        $national = substr($digits, 3);
    } elseif (str_starts_with($digits, '0')) {
        $national = ltrim($digits, '0');
    } else {
        $national = $digits;
    }

    $length = strlen($national);
    if ($length < 7 || $length > 11) {
        return null;
    }
    // Ningún número paraguayo empieza en 0 ni en 1 una vez quitado el prefijo.
    if (!preg_match('/^[2-9]/', $national)) {
        return null;
    }
    // Celulares: 9 + 8 dígitos. Un "9…" de otro largo es un tipeo, no una línea fija.
    if ($national[0] === '9' && $length !== 9) {
        return null;
    }

    return '+595' . $national;
}

/**
 * Clave de idempotencia (plan §3): mismo teléfono dentro de la misma hora UTC = mismo envío.
 * Colapsa el doble clic y el reintento por timeout, pero deja consultar de nuevo mañana.
 * Se hashea el teléfono NORMALIZADO para que el formato tipeado no genere claves distintas.
 *
 * Firmado con HMAC y el mismo secreto de lead_form_secret() (nunca hash() a secas): sin
 * secreto, la clave era el hash público de "teléfono + hora", adivinable por cualquiera que
 * conociera el número de otra persona — alcanzaba para chocar a propósito un pedido futuro
 * ajeno contra uno inventado, y para recuperar el teléfono probando el espacio de numeración
 * paraguayo. Con HMAC, sólo quien tiene el secreto puede calcular o invertir la clave.
 */
function lead_idempotency_key(string $phoneE164, ?int $now = null): string
{
    return hash_hmac('sha256', $phoneE164 . '|' . gmdate('Y-m-d-H', $now ?? time()), lead_form_secret());
}

/**
 * Secreto persistido y generado en el primer uso, para cuando no hay 'form_secret' explícito
 * en config/vendercrm.php. Evita dos problemas del esquema anterior (derivar el secreto de
 * 'api_key' o, en su ausencia, de site('base_url'), un valor público que aparece en cada URL
 * del sitio):
 *   1. Con el CRM sin configurar, cualquiera podía calcular el secreto y falsificar el sello.
 *   2. El día que se cargaba config/vendercrm.php, el secreto cambiaba de golpe y todo
 *      formulario ya renderizado (pestañas abiertas, back-button, una posible caché de
 *      página) quedaba con un sello que ya no valida — el visitante ve "Listo, recibimos tu
 *      pedido" pero el lead nunca se guarda.
 * Con este archivo, el secreto por defecto es aleatorio desde el primer request y no depende
 * de si el CRM ya está configurado, así que no cambia solo. Vive en storage/ (protegido por
 * su .htaccess y por el bloqueo de la raíz) y nunca se commitea (storage/ está en
 * .gitignore).
 */
function lead_form_secret_auto(): string
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $path = STORAGE_DIR . '/.form-secret';
    $existing = @file_get_contents($path);
    if (is_string($existing) && strlen(trim($existing)) >= 32) {
        return $cached = trim($existing);
    }

    try {
        $fresh = bin2hex(random_bytes(32));
    } catch (Throwable $e) {
        // Entorno sin CSPRNG disponible: extremadamente improbable en PHP 8, pero no hay que
        // tirar una excepción por esto — degradar es mejor que romper el formulario entero.
        return $cached = hash('sha256', uniqid('materiales-form-fallback', true));
    }

    if (!is_dir(STORAGE_DIR)) {
        @mkdir(STORAGE_DIR, 0770, true);
    }
    // O_EXCL vía 'x': si dos requests concurrentes generan el archivo a la vez, sólo el
    // primero gana y el segundo lee lo que ya quedó escrito, evitando dos secretos distintos.
    $handle = @fopen($path, 'x');
    if ($handle !== false) {
        fwrite($handle, $fresh);
        fclose($handle);
        @chmod($path, 0600);
        return $cached = $fresh;
    }

    // Alguien más lo creó primero entre el file_get_contents() y el fopen('x') de arriba.
    $existing = @file_get_contents($path);
    if (is_string($existing) && strlen(trim($existing)) >= 32) {
        return $cached = trim($existing);
    }
    return $cached = $fresh;
}

/** Secreto para firmar el sello del formulario. Ver lead_form_stamp(). */
function lead_form_secret(): string
{
    $config = lead_config();
    if ($config['form_secret'] !== '') {
        return hash('sha256', 'materiales-form|' . $config['form_secret']);
    }
    return hash('sha256', 'materiales-form|' . lead_form_secret_auto());
}

/**
 * Sello de render del formulario: timestamp + HMAC. Un bot que postea directo al handler no
 * puede fabricar un sello válido, y uno que reusa el sello de la página sigue chocando con
 * el mínimo de LEAD_MIN_SECONDS.
 */
function lead_form_stamp(?int $now = null): array
{
    $ts = (string) ($now ?? time());
    return ['ts' => $ts, 'sig' => hash_hmac('sha256', $ts, lead_form_secret())];
}

/**
 * Valida el sello. Devuelve '' si está bien, o el motivo del descarte: 'sello' (falta o está
 * falsificado o vencido) y 'rapido' (submit en menos de LEAD_MIN_SECONDS).
 * Ambos motivos se tratan como bot: se registra y no se manda nada al CRM.
 */
function lead_form_stamp_reason(string $ts, string $sig, ?int $now = null): string
{
    $now = $now ?? time();
    if ($ts === '' || $sig === '' || !ctype_digit($ts)) {
        return 'sello';
    }
    if (!hash_equals(hash_hmac('sha256', $ts, lead_form_secret()), $sig)) {
        return 'sello';
    }

    $age = $now - (int) $ts;
    if ($age > LEAD_STAMP_TTL || $age < -60) {
        return 'sello';
    }
    if ($age < LEAD_MIN_SECONDS) {
        return 'rapido';
    }
    return '';
}

/**
 * Atribución de primer toque: la cookie vc_attr (la escribe vc-attribution.js del CRM) MANDA
 * sobre los parámetros del POST. Sin esto, un visitante que llega por campaña hoy y convierte
 * la semana que viene aparece como tráfico directo (skill vendercrm-lead-capture, regla 6).
 */
function lead_attribution(array $post, array $cookies): array
{
    $keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'];

    $attr = [];
    foreach ($keys as $key) {
        $value = trim((string) ($post[$key] ?? ''));
        if ($value !== '') {
            $attr[$key] = mb_substr($value, 0, 200);
        }
    }

    $cookie = json_decode((string) ($cookies['vc_attr'] ?? ''), true);
    if (is_array($cookie)) {
        foreach ($keys as $key) {
            $value = is_scalar($cookie[$key] ?? null) ? trim((string) $cookie[$key]) : '';
            if ($value !== '') {
                $attr[$key] = mb_substr($value, 0, 200); // primer toque gana
            }
        }
        foreach (['landing_page' => 'page_url', 'referrer' => 'referrer'] as $from => $to) {
            $value = is_scalar($cookie[$from] ?? null) ? trim((string) $cookie[$from]) : '';
            if ($value !== '') {
                $attr[$to] = mb_substr($value, 0, 2000);
            }
        }
    }

    return $attr;
}

/**
 * Arma el payload de POST {CRM_URL}/api/v1/leads exactamente como lo fija el plan §3.
 *
 * $input: phone_raw, phone_e164, nombre, mensaje, material, categoria, material_name,
 *         categoria_name, cantidad, ciudad, presupuesto_band, page_url, referrer,
 *         attribution[], consent_at (ISO-8601).
 */
function lead_build_payload(array $input): array
{
    $consentVersion = (string) site('consent_version', 'proveedores-v1');

    $fields = [
        'material'        => (string) ($input['material'] ?? ''),
        'categoria'       => (string) ($input['categoria'] ?? ''),
        'material_nombre' => (string) ($input['material_name'] ?? ''),
        'categoria_nombre'=> (string) ($input['categoria_name'] ?? ''),
        'cantidad'        => mb_substr(trim((string) ($input['cantidad'] ?? '')), 0, 200),
        'ciudad'          => mb_substr(trim((string) ($input['ciudad'] ?? '')), 0, 200),
        'presupuesto_band'=> (string) ($input['presupuesto_band'] ?? ''),
        // Constancia de consentimiento: versión del texto + momento exacto (plan §8.6).
        'consent'         => $consentVersion . ' @ ' . (string) ($input['consent_at'] ?? ''),
    ];

    $payload = [
        'phone'           => mb_substr(trim((string) ($input['phone_raw'] ?? '')), 0, 30),
        'idempotency_key' => (string) ($input['idempotency_key'] ?? ''),
        'name'            => mb_substr(trim((string) ($input['nombre'] ?? '')), 0, 200),
        'message'         => mb_substr(trim((string) ($input['mensaje'] ?? '')), 0, 5000),
        'source'          => 'site:materiales',
        'page_url'        => mb_substr((string) ($input['page_url'] ?? ''), 0, 2000),
        'referrer'        => mb_substr((string) ($input['referrer'] ?? ''), 0, 2000),
    ];

    foreach ((array) ($input['attribution'] ?? []) as $key => $value) {
        // La cookie ya trae page_url/referrer de primer toque: no los pisa el request actual.
        $payload[$key] = $value;
    }

    // La API rechaza '' (p. ej. en email) en vez de ignorarlo: se omite, no se manda vacío.
    $payload = array_filter($payload, static fn($v): bool => $v !== '' && $v !== null);
    $payload['fields'] = array_filter($fields, static fn($v): bool => $v !== '' && $v !== null);

    return $payload;
}

/**
 * Payload del lado PROVEEDOR (fase 10, decisión §1.17). Función aparte a propósito: el
 * contrato del payload del comprador es fundacional (plan §4.4) y no se toca ni un byte por
 * agregar un segundo formulario.
 *
 * Diferencias con el del comprador: viaja `fields.tipo = 'proveedor'` (el comprador NO manda
 * `tipo` y el handler nunca se lo pone por defecto), `fields.empresa`, `fields.rubros`
 * (slugs separados por coma) y una constancia de consentimiento con SU propia versión
 * (consent_version_proveedor). No lleva material, categoría, cantidad ni banda de precio:
 * un proveedor no está pidiendo cotización. `source` sigue siendo 'site:materiales' — el
 * ruteo se configura en VenderCRM por `fields.tipo`, nunca acá.
 *
 * $input: phone_raw, idempotency_key, nombre, empresa, rubros[], ciudad, mensaje, page_url,
 *         referrer, attribution[], consent_at (ISO-8601).
 */
function lead_build_supplier_payload(array $input): array
{
    $consentVersion = (string) site('consent_version_proveedor', 'proveedor-v1');

    $fields = [
        'tipo'    => 'proveedor',
        'empresa' => mb_substr(trim((string) ($input['empresa'] ?? '')), 0, 200),
        'rubros'  => implode(',', array_map(
            static fn($slug): string => (string) $slug,
            (array) ($input['rubros'] ?? [])
        )),
        'ciudad'  => mb_substr(trim((string) ($input['ciudad'] ?? '')), 0, 200),
        'consent' => $consentVersion . ' @ ' . (string) ($input['consent_at'] ?? ''),
    ];

    $payload = [
        'phone'           => mb_substr(trim((string) ($input['phone_raw'] ?? '')), 0, 30),
        'idempotency_key' => (string) ($input['idempotency_key'] ?? ''),
        'name'            => mb_substr(trim((string) ($input['nombre'] ?? '')), 0, 200),
        'message'         => mb_substr(trim((string) ($input['mensaje'] ?? '')), 0, 5000),
        'source'          => 'site:materiales',
        'page_url'        => mb_substr((string) ($input['page_url'] ?? ''), 0, 2000),
        'referrer'        => mb_substr((string) ($input['referrer'] ?? ''), 0, 2000),
    ];

    foreach ((array) ($input['attribution'] ?? []) as $key => $value) {
        $payload[$key] = $value;
    }

    // La API rechaza '' en vez de ignorarlo: se omite, no se manda vacío.
    $payload = array_filter($payload, static fn($v): bool => $v !== '' && $v !== null);
    $payload['fields'] = array_filter($fields, static fn($v): bool => $v !== '' && $v !== null);

    return $payload;
}

/**
 * Rubros válidos de un alta de proveedor: sólo slugs de categorías PUBLICADAS, sin repetir y
 * en el orden del catálogo. Lo que no existe se descarta en silencio (un checkbox tipeado a
 * mano no puede meter basura en el CRM).
 *
 * @param mixed $raw lo que llegó en $_POST['rubros']
 * @return list<string>
 */
function lead_supplier_rubros(mixed $raw): array
{
    $posted = array_map(static fn($v): string => is_scalar($v) ? (string) $v : '', (array) $raw);
    $valid  = [];
    foreach (data('categories') as $slug => $category) {
        if (is_published($category) && in_array((string) $slug, $posted, true)) {
            $valid[] = (string) $slug;
        }
    }
    return $valid;
}

/**
 * POST al CRM. Nunca lanza: cualquier fallo vuelve como ['status' => 0, 'error' => ...] y el
 * visitante igual llega a /gracias/ (skill vendercrm-lead-capture, regla 5).
 */
function lead_send(array $payload, array $config): array
{
    $result = ['status' => 0, 'body' => '', 'error' => '', 'ms' => 0];
    $started = microtime(true);

    try {
        if (!function_exists('curl_init')) {
            $result['error'] = 'curl no disponible en este PHP';
            return $result;
        }

        $ch = curl_init($config['url'] . '/api/v1/leads');
        if ($ch === false) {
            $result['error'] = 'curl_init falló';
            return $result;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => max(1, (int) $config['timeout']),
            CURLOPT_CONNECTTIMEOUT => max(1, min(5, (int) $config['timeout'])),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-Api-Key: ' . $config['api_key'],
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        $body = curl_exec($ch);
        $result['status'] = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result['body']   = is_string($body) ? mb_substr($body, 0, 2000) : '';
        $result['error']  = curl_error($ch);
        curl_close($ch);
    } catch (Throwable $e) {
        $result['error'] = get_class($e) . ': ' . $e->getMessage();
    }

    $result['ms'] = (int) round((microtime(true) - $started) * 1000);
    return $result;
}

/** 200 (idempotente, duplicate:true) y 201 son ambos éxito (skill: el reintento funcionando). */
function lead_send_ok(array $result): bool
{
    return $result['status'] === 200 || $result['status'] === 201;
}

/**
 * Una línea JSON por evento en storage/leads.log. SIEMPRE se escribe, haya CRM o no: es el
 * respaldo ante caída del CRM y la pista de auditoría del consentimiento (plan §2, §3).
 * Devuelve false si no se pudo escribir — el visitante nunca se entera.
 */
function lead_log(array $record): bool
{
    try {
        $storageIsNew = !is_dir(STORAGE_DIR);
        if ($storageIsNew && !@mkdir(STORAGE_DIR, 0770, true) && !is_dir(STORAGE_DIR)) {
            error_log('lead_log: no se pudo crear ' . STORAGE_DIR . ' — lead perdido');
            return false;
        }
        if ($storageIsNew) {
            // Defensa en profundidad además de las reglas [F] de la raíz: si esas reglas
            // fallan alguna vez, este Require all denied sigue protegiendo storage/ (plan §2).
            $htaccess = STORAGE_DIR . '/.htaccess';
            if (!is_file($htaccess)) {
                @copy(dirname(__DIR__) . '/tools/.htaccess', $htaccess);
            }
        }
        $line = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($line === false) {
            error_log('lead_log: json_encode falló — lead perdido');
            return false;
        }
        $written = @file_put_contents(STORAGE_DIR . '/leads.log', $line . "\n", FILE_APPEND | LOCK_EX);
        if ($written === false) {
            error_log('lead_log: no se pudo escribir en ' . STORAGE_DIR . '/leads.log — lead perdido');
            return false;
        }
        return true;
    } catch (Throwable $e) {
        error_log('lead_log: excepción — lead perdido: ' . $e->getMessage());
        return false;
    }
}

/**
 * Huella de IP para diagnosticar spam sin guardar la IP en claro. Firmada con HMAC y el
 * secreto de lead_form_secret(): con site('base_url') como única sal (público, en cada URL
 * del sitio), el espacio IPv4 se recorre en segundos y la "huella" no anonimiza nada.
 */
function lead_ip_fingerprint(string $ip): string
{
    return $ip === '' ? '' : substr(hash_hmac('sha256', $ip, lead_form_secret()), 0, 16);
}

/** Limita leads distintos por huella; permite reintentos. Ante fallos deja pasar el lead. */
function lead_ip_throttled(string $ip, string $idempotencyKey, ?int $now = null): bool
{
    try {
        if ($ip === '') {
            return false;
        }
        $now = $now ?? time();
        $dir = STORAGE_DIR . '/throttle';
        $storageIsNew = !is_dir($dir);
        if ($storageIsNew && !@mkdir($dir, 0770, true) && !is_dir($dir)) {
            return false;
        }
        if ($storageIsNew) {
            $htaccess = $dir . '/.htaccess';
            if (!is_file($htaccess) && !@copy(dirname(__DIR__) . '/tools/.htaccess', $htaccess)) {
                return false;
            }
        }
        $handle = @fopen($dir . '/' . lead_ip_fingerprint($ip), 'c+');
        if ($handle === false) {
            return false;
        }
        try {
            if (!@flock($handle, LOCK_EX)) {
                return false;
            }
            $last = @stream_get_contents($handle);
            if ($last === false) {
                return false;
            }
            if (preg_match('/\A([0-9a-fA-F]+)\n([0-9]+)\z/', $last, $record) === 1) {
                if ($record[1] === $idempotencyKey) {
                    return false;
                }
                if ($now - (int) $record[2] < LEAD_IP_WINDOW_SECONDS) {
                    return true;
                }
            }
            // Un registro vacío o corrupto se reemplaza para recuperar el límite.
            $timestamp = $idempotencyKey . "\n" . (string) $now;
            if (!@rewind($handle) || !@ftruncate($handle, 0)
                || @fwrite($handle, $timestamp) !== strlen($timestamp) || !@fflush($handle)) {
                return false;
            }
            return false;
        } finally {
            // Cerrar libera también el LOCK_EX, incluso ante una excepción.
            @fclose($handle);
        }
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Resuelve el slug enviado a material + categoría. El namespace es plano y compartido: un
 * slug puede ser categoría O material (plan §2). Devuelve null si el slug no existe.
 */
function lead_resolve_slug(string $slug): ?array
{
    if ($slug === '' || !preg_match('/^[a-z0-9-]+$/', $slug)) {
        return null;
    }

    $categories = data('categories');
    if (isset($categories[$slug])) {
        return [
            'material'         => $slug,
            'material_name'    => $categories[$slug]['name'],
            'categoria'        => $slug,
            'categoria_name'   => $categories[$slug]['name'],
            'presupuesto_band' => '',
        ];
    }

    $materials = data('materials');
    if (isset($materials[$slug])) {
        $categorySlug = (string) ($materials[$slug]['category'] ?? '');
        return [
            'material'         => $slug,
            'material_name'    => $materials[$slug]['name'],
            'categoria'        => $categorySlug,
            'categoria_name'   => $categories[$categorySlug]['name'] ?? '',
            // Interno: nunca se renderiza como precio (plan §8.7). Se completa en la fase 3.
            'presupuesto_band' => (string) ($materials[$slug]['price_band'] ?? ''),
        ];
    }

    return null;
}

/**
 * Ruta interna segura para volver al formulario tras un error. Cualquier cosa que no sea una
 * ruta propia cae en /cotizar/ — así el handler no se convierte en un open redirect.
 */
function lead_safe_path(string $candidate, string $fallback = '/cotizar/'): string
{
    $candidate = trim($candidate);

    // Se descarta ANTES de parsear: parse_url() de 'https://evil.example/x' devuelve '/x',
    // así que confiar en su PHP_URL_PATH convertiría una URL ajena en una ruta interna
    // plausible en vez de mandarla al fallback.
    if ($candidate === '' || $candidate[0] !== '/' || str_starts_with($candidate, '//')) {
        return $fallback;
    }

    $path = parse_url($candidate, PHP_URL_PATH);
    if (!is_string($path) || $path === '' || $path[0] !== '/') {
        return $fallback;
    }
    if (!preg_match('#^/[a-z0-9\-/]*$#', $path) || strlen($path) > 200) {
        return $fallback;
    }
    return $path;
}
