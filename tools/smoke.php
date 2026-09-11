<?php
/**
 * tools/smoke.php — chequeo de integridad de los archivos de datos. Es el check requerido en
 * CI junto a `php -l` (plan §1 fase 1).
 *
 * Verifica: que los cuatro archivos de datos carguen, que los slugs sean únicos en el
 * namespace COMPARTIDO categorías+materiales, el formato de slug, la coherencia de status,
 * las referencias entre archivos, y los límites de title/meta.
 *
 * Desde la fase 2 verifica además el pipeline de leads como UNIDADES sobre
 * partials/lead.php — sin servidor, sin CRM y sin red: normalización de
 * teléfonos paraguayos, idempotencia, trampa de tiempo, atribución de primer toque, forma
 * del payload (incluido lo que NUNCA se manda) y escritura de leads.log. El camino HTTP
 * completo (honeypot, rechazos, redirects) lo cubre tools/render-check.sh con POSTs reales.
 *
 *   php tools/smoke.php     → sale 0 si todo pasa, 1 con la lista de errores
 */

declare(strict_types=1);

$root = dirname(__DIR__);

/** @var list<string> $errors */
$errors = [];
$fail = static function (string $message) use (&$errors): void { $errors[] = $message; };

$load = static function (string $name) use ($root, $fail): array {
    $file = $root . '/data/' . $name . '.php';
    if (!is_file($file)) {
        $fail("data/{$name}.php no existe");
        return [];
    }
    $value = require $file;
    if (!is_array($value)) {
        $fail("data/{$name}.php no devuelve un array");
        return [];
    }
    return $value;
};

$site       = $load('site');
$categories = $load('categories');
$materials  = $load('materials');
$guides     = $load('guides');

// ---- site.php: claves obligatorias -------------------------------------------------
foreach (['brand', 'base_url', 'locale', 'area_served', 'staging_noindex', 'consent_version', 'max_proveedores'] as $key) {
    if (!array_key_exists($key, $site)) {
        $fail("data/site.php: falta la clave '{$key}'");
    }
}
if (($site['base_url'] ?? '') === '' || !str_starts_with((string) ($site['base_url'] ?? ''), 'https://')) {
    $fail("data/site.php: base_url debe ser una URL https");
}

// ---- validación común de entradas ---------------------------------------------------
$checkEntry = static function (string $file, string $slug, array $entry, array $requiredKeys) use ($fail): void {
    if (!preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $slug)) {
        $fail("{$file}: slug inválido '{$slug}' (sólo a-z, 0-9 y guiones; sin acentos ni ñ)");
    }
    foreach ($requiredKeys as $key) {
        if (!array_key_exists($key, $entry)) {
            $fail("{$file}[{$slug}]: falta la clave '{$key}'");
        }
    }
    foreach (['name', 'title', 'meta'] as $key) {
        if (trim((string) ($entry[$key] ?? '')) === '') {
            $fail("{$file}[{$slug}]: '{$key}' vacío");
        }
    }
    if (mb_strlen((string) ($entry['title'] ?? '')) > 60) {
        $fail(sprintf('%s[%s]: title de %d caracteres (máx. 60)', $file, $slug, mb_strlen((string) $entry['title'])));
    }
    if (mb_strlen((string) ($entry['meta'] ?? '')) > 155) {
        $fail(sprintf('%s[%s]: meta de %d caracteres (máx. 155)', $file, $slug, mb_strlen((string) $entry['meta'])));
    }
    if (!in_array($entry['status'] ?? '', ['activa', 'proxima'], true)) {
        $fail("{$file}[{$slug}]: status debe ser 'activa' o 'proxima'");
    }
};

foreach ($categories as $slug => $category) {
    $checkEntry('categories.php', (string) $slug, $category, ['name', 'status', 'order', 'title', 'meta', 'faq']);
}
foreach ($materials as $slug => $material) {
    $checkEntry('materials.php', (string) $slug, $material, ['category', 'name', 'status', 'title', 'meta', 'synonyms', 'faq', 'related']);
}
foreach ($guides as $slug => $guide) {
    $checkEntry('guides.php', (string) $slug, $guide, ['name', 'status', 'order', 'title', 'meta', 'related']);
}

// ---- fase 3: el contenido tiene que estar cerrado, no sólo declarado --------------
// Una FAQ vacía significaría FAQPage sin preguntas visibles, y un material sin sale_unit ni
// synonyms es una página que no puede rankear ni cotizarse bien. Se exige a TODAS las
// entradas (también a las 'proxima': se publican tal cual cuando les toque el turno).
$checkContent = static function (string $file, string $slug, array $entry, array $textKeys, array $listKeys) use ($fail): void {
    foreach ($textKeys as $key) {
        if (trim((string) ($entry[$key] ?? '')) === '') {
            $fail("{$file}[{$slug}]: '{$key}' vacío — la fase 3 lo tiene que cerrar");
        }
    }
    foreach ($listKeys as $key) {
        if (($entry[$key] ?? []) === []) {
            $fail("{$file}[{$slug}]: '{$key}' vacío — la fase 3 lo tiene que cerrar");
        }
    }
    $faq = $entry['faq'] ?? [];
    if (count($faq) < 3 || count($faq) > 5) {
        $fail(sprintf('%s[%s]: %d preguntas en faq (se esperan 3 a 5)', $file, $slug, count($faq)));
    }
    foreach ($faq as $i => $item) {
        if (trim((string) ($item['q'] ?? '')) === '' || trim((string) ($item['a'] ?? '')) === '') {
            $fail("{$file}[{$slug}]: faq[{$i}] sin pregunta o sin respuesta");
        }
    }
};

foreach ($categories as $slug => $category) {
    $checkContent('categories.php', (string) $slug, $category, ['keyword', 'intro'], ['intro_keywords', 'faq']);
}
foreach ($materials as $slug => $material) {
    $checkContent('materials.php', (string) $slug, $material, ['keyword', 'intro', 'sale_unit', 'price_band'], ['synonyms', 'faq', 'related']);
    foreach ($material['related'] ?? [] as $target) {
        if (!isset($materials[$target]) && !isset($categories[$target])) {
            $fail("materials.php[{$slug}]: related '{$target}' no existe en materials.php ni en categories.php");
        }
        if ($target === $slug) {
            $fail("materials.php[{$slug}]: related se apunta a sí mismo");
        }
    }
}
foreach ($guides as $slug => $guide) {
    if (trim((string) ($guide['keyword'] ?? '')) === '') {
        $fail("guides.php[{$slug}]: 'keyword' vacío — la fase 3 lo tiene que cerrar");
    }
}

// ---- EL check crítico: namespace de slugs plano y compartido (plan §2) --------------
$collisions = array_intersect(array_keys($categories), array_keys($materials));
foreach ($collisions as $slug) {
    $fail("colisión de slug '{$slug}': existe como categoría Y como material — /materiales/{$slug}/ es ambiguo");
}

// ---- referencias e implicancias de status -------------------------------------------
foreach ($materials as $slug => $material) {
    $categorySlug = (string) ($material['category'] ?? '');
    if (!isset($categories[$categorySlug])) {
        $fail("materials.php[{$slug}]: category '{$categorySlug}' no existe en categories.php");
        continue;
    }
    if (($material['status'] ?? '') === 'activa' && ($categories[$categorySlug]['status'] ?? '') !== 'activa') {
        $fail("materials.php[{$slug}]: está 'activa' pero su categoría '{$categorySlug}' está 'proxima'");
    }
}

// Una categoría publicada con una o dos páginas es una categoría vacía: no rankea y no da
// de dónde elegir en el formulario. La fase 5c fija el piso en 3 materiales activos.
$activeByCategory = [];
foreach ($materials as $material) {
    if (($material['status'] ?? '') === 'activa') {
        $activeByCategory[(string) ($material['category'] ?? '')] = ($activeByCategory[(string) ($material['category'] ?? '')] ?? 0) + 1;
    }
}
foreach ($categories as $slug => $category) {
    if (($category['status'] ?? '') !== 'activa') {
        continue;
    }
    $count = $activeByCategory[(string) $slug] ?? 0;
    if ($count < 3) {
        $fail(sprintf("categories.php[%s]: está 'activa' con %d materiales activos (mínimo 3)", $slug, $count));
    }
}

// Toda página de material cierra su faq[] con la pregunta de precio (CONTENT-SPEC §11.3):
// son ~2.500 búsquedas/mes con el modificador 'precio' y sin respuesta visible rebotan.
foreach ($materials as $slug => $material) {
    $faq = $material['faq'] ?? [];
    $last = is_array($faq) && $faq !== [] ? (string) (end($faq)['q'] ?? '') : '';
    if (!str_starts_with($last, '¿Cuánto cuesta')) {
        $fail("materials.php[{$slug}]: la última faq tiene que ser '¿Cuánto cuesta …?' (CONTENT-SPEC §11.3)");
    }
}

foreach ($guides as $slug => $guide) {
    foreach ($guide['related'] ?? [] as $target) {
        if (!isset($categories[$target]) && !isset($materials[$target])) {
            $fail("guides.php[{$slug}]: related '{$target}' no existe en categories.php ni en materials.php");
        }
    }
}

// ---- los archivos de contenido referenciados deben existir (si se declararon) --------
foreach (['categorias' => $categories, 'materiales' => $materials, 'guias' => $guides] as $dir => $entries) {
    $contentDir = $root . '/content/' . $dir;
    foreach (glob($contentDir . '/*.php') ?: [] as $file) {
        $slug = basename($file, '.php');
        if (!isset($entries[$slug])) {
            $fail("content/{$dir}/{$slug}.php no tiene entrada en el archivo de datos correspondiente");
        }
    }
}

// ====================================================================================
// PIPELINE DE LEADS (fase 2) — unidades sobre partials/lead.php
// ====================================================================================

require $root . '/partials/init.php';
require $root . '/partials/lead.php';

$is = static function (string $what, $actual, $expected) use ($fail): void {
    if ($actual !== $expected) {
        $fail(sprintf(
            'lead: %s — devolvió %s, esperaba %s',
            $what,
            var_export($actual, true),
            var_export($expected, true)
        ));
    }
};

// ---- teléfonos: aceptar lo que la gente escribe de verdad --------------------------
// Rechazar un lead real cuesta plata (plan §8.1): estos casos son los formatos que
// efectivamente se tipean en Paraguay y NINGUNO puede volverse un rechazo por accidente.
// (Lista de pares y no un mapa: PHP convertiría una clave como '595981123456' en int.)
foreach ([
    ['0981 123 456',    '+595981123456'],
    ['0981123456',      '+595981123456'],
    ['+595 981 123456', '+595981123456'],
    ['595981123456',    '+595981123456'],
    ['(021) 123-456',   '+59521123456'],
    ['021 123 456',     '+59521123456'],
] as [$typed, $expected]) {
    $is("normalize_phone('{$typed}')", lead_normalize_phone($typed), $expected);
}
foreach (['', 'no es un teléfono', '12', '0981 12', '098112345678', '0181123456'] as $bad) {
    $is("normalize_phone rechaza '{$bad}'", lead_normalize_phone($bad), null);
}

// ---- idempotencia: el doble clic NO puede crear un segundo contacto ------------------
$t0 = gmmktime(10, 30, 0, 6, 15, 2026);
$keyA = lead_idempotency_key('+595981123456', $t0);
$keyB = lead_idempotency_key('+595981123456', $t0 + 900); // misma hora UTC, 15 min después
$is('idempotency_key estable dentro de la hora', $keyA, $keyB);
if ($keyA === lead_idempotency_key('+595981123456', $t0 + 3600)) {
    $fail('lead: idempotency_key no cambia en la hora siguiente — nadie podría volver a consultar');
}
if ($keyA === lead_idempotency_key('+595971000000', $t0)) {
    $fail('lead: idempotency_key igual para teléfonos distintos');
}
if (strlen($keyA) < 8 || strlen($keyA) > 100) {
    $fail('lead: idempotency_key fuera del rango 8–100 que exige la API');
}
// El formato tipeado no puede generar claves distintas: por eso se hashea el normalizado.
$is(
    'idempotency_key indiferente al formato tipeado',
    lead_idempotency_key((string) lead_normalize_phone('0981 123 456'), $t0),
    lead_idempotency_key((string) lead_normalize_phone('+595981123456'), $t0)
);

// ---- trampa de tiempo: sello firmado ------------------------------------------------
$stamp = lead_form_stamp($t0);
$is('sello válido a los 10s', lead_form_stamp_reason($stamp['ts'], $stamp['sig'], $t0 + 10), '');
$is('sello a 1s = bot', lead_form_stamp_reason($stamp['ts'], $stamp['sig'], $t0 + 1), 'rapido');
$is('sello falsificado', lead_form_stamp_reason($stamp['ts'], 'firma-inventada', $t0 + 10), 'sello');
$is('sello ausente', lead_form_stamp_reason('', '', $t0 + 10), 'sello');
$is('sello no numérico', lead_form_stamp_reason('ayer', $stamp['sig'], $t0 + 10), 'sello');
$is('sello vencido', lead_form_stamp_reason($stamp['ts'], $stamp['sig'], $t0 + LEAD_STAMP_TTL + 60), 'sello');
// Un sello robado de otra página sigue chocando con el mínimo: es firma Y tiempo, no una sola cosa.
$is('sello del futuro', lead_form_stamp_reason((string) ($t0 + 3600), lead_form_stamp($t0 + 3600)['sig'], $t0), 'sello');

// ---- atribución: la cookie de primer toque MANDA sobre el POST ----------------------
$attr = lead_attribution(
    ['utm_source' => 'directo-de-hoy', 'utm_medium' => 'organic'],
    ['vc_attr' => json_encode([
        'utm_source'   => 'google-ads',
        'gclid'        => 'Cj0KAQ',
        'landing_page' => 'https://materiales.com.py/materiales/hierro/',
        'referrer'     => 'https://www.google.com/',
    ])]
);
$is('atribución: la cookie pisa el POST', $attr['utm_source'] ?? null, 'google-ads');
$is('atribución: el POST completa lo que la cookie no trae', $attr['utm_medium'] ?? null, 'organic');
$is('atribución: gclid de la cookie', $attr['gclid'] ?? null, 'Cj0KAQ');
$is('atribución: landing_page → page_url', $attr['page_url'] ?? null, 'https://materiales.com.py/materiales/hierro/');
$is('atribución: sin cookie ni POST no inventa nada', lead_attribution([], []), []);

// ---- payload: el contrato del plan §3 (fundacional, §4.4) ---------------------------
$payload = lead_build_payload([
    'phone_raw'       => '0981 123 456',
    'idempotency_key' => $keyA,
    'nombre'          => 'Ana Benítez',
    'mensaje'         => '',
    'material'        => 'piedra-bruta',
    'material_name'   => 'Piedra bruta',
    'categoria'       => 'aridos',
    'categoria_name'  => 'Áridos',
    'cantidad'        => '2 camiones',
    'ciudad'          => 'Luque',
    'page_url'        => 'https://materiales.com.py/materiales/piedra-bruta/',
    'attribution'     => ['utm_source' => 'google-ads'],
    'consent_at'      => '2026-06-15T10:30:00+00:00',
]);

// Lo que NUNCA se manda: el ruteo vive en el registro del sitio dentro del CRM.
foreach (['pipeline', 'stage', 'owner', 'tag'] as $forbidden) {
    if (array_key_exists($forbidden, $payload) || array_key_exists($forbidden, $payload['fields'])) {
        $fail("lead: el payload incluye '{$forbidden}' — el ruteo se configura en el CRM, nunca acá");
    }
}
$is('payload.phone', $payload['phone'] ?? null, '0981 123 456');
$is('payload.source', $payload['source'] ?? null, 'site:materiales');
$is('payload.idempotency_key', $payload['idempotency_key'] ?? null, $keyA);
$is('payload.utm_source', $payload['utm_source'] ?? null, 'google-ads');
$is('payload.fields.material', $payload['fields']['material'] ?? null, 'piedra-bruta');
$is('payload.fields.categoria', $payload['fields']['categoria'] ?? null, 'aridos');
$is('payload.fields.cantidad', $payload['fields']['cantidad'] ?? null, '2 camiones');
$is('payload.fields.ciudad', $payload['fields']['ciudad'] ?? null, 'Luque');
// Constancia de consentimiento: versión del texto + momento exacto (plan §8.6).
$is(
    'payload.fields.consent',
    $payload['fields']['consent'] ?? null,
    site('consent_version') . ' @ 2026-06-15T10:30:00+00:00'
);
// La API rechaza '' (p. ej. en email) en vez de ignorarlo: se omite, no se manda vacío.
foreach ($payload as $key => $value) {
    if ($value === '' || $value === null) {
        $fail("lead: payload['{$key}'] va vacío — la API rechaza '' en vez de ignorarlo");
    }
}
if (array_key_exists('message', $payload)) {
    $fail("lead: payload incluye 'message' vacío en vez de omitirlo");
}
if (!json_encode($payload)) {
    $fail('lead: el payload no serializa a JSON');
}

// ---- resolución de slug -------------------------------------------------------------
$firstMaterial = (string) array_key_first($materials);
$resolved = lead_resolve_slug($firstMaterial);
if ($resolved === null) {
    $fail("lead: resolve_slug no resolvió el material '{$firstMaterial}'");
} else {
    $is('resolve_slug: categoría del material', $resolved['categoria'], (string) $materials[$firstMaterial]['category']);
}
$is('resolve_slug: slug inexistente', lead_resolve_slug('no-existe-este-slug'), null);
$is('resolve_slug: slug con forma inválida', lead_resolve_slug('../../etc/passwd'), null);

// ---- redirect de error: no puede volverse un open redirect --------------------------
$is('safe_path: ruta propia', lead_safe_path('/materiales/hierro/'), '/materiales/hierro/');
foreach (['https://evil.example/x', '//evil.example/x', 'javascript:alert(1)', ''] as $hostile) {
    $is("safe_path rechaza '{$hostile}'", lead_safe_path($hostile), '/cotizar/');
}

// ---- leads.log: se escribe SIEMPRE, con CRM o sin CRM -------------------------------
// Es el respaldo ante caída del CRM y la pista de auditoría del consentimiento (plan §3.5).
$logFile = STORAGE_DIR . '/leads.log';
$linesBefore = is_file($logFile) ? count(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) : 0;
$marker = 'smoke-' . bin2hex(random_bytes(4));
if (!lead_log(['ts' => gmdate('c'), 'outcome' => 'smoke', 'marker' => $marker])) {
    $fail('lead: lead_log() no pudo escribir storage/leads.log');
} else {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    if (count($lines) !== $linesBefore + 1) {
        $fail('lead: lead_log() no agregó exactamente una línea a leads.log');
    }
    $last = json_decode((string) end($lines), true);
    if (!is_array($last) || ($last['marker'] ?? '') !== $marker) {
        $fail('lead: la última línea de leads.log no es el JSON que se acaba de escribir');
    }
}

// ---- sin config de CRM el sitio igual funciona (plan §4.5) ---------------------------
if (!is_file(CONFIG_DIR . '/vendercrm.php') && lead_crm_configured()) {
    $fail('lead: crm_configured() dice true sin config/vendercrm.php');
}

// ---- texto de consentimiento y contrato: cambiarlos es una parada (plan §4.4) --------
// Este check existe para que un cambio accidental falle en CI en vez de invalidar en
// silencio la constancia de consentimiento ya guardada en los leads del CRM.
$formSource = (string) @file_get_contents($root . '/partials/form.php');
$consentText = 'Acepto que mis datos sean compartidos con proveedores del rubro para recibir';
if (!str_contains($formSource, $consentText)) {
    $fail('lead: cambió el texto de consentimiento de partials/form.php (plan §8.6 — es una parada §4.4)');
}
foreach (['name="website"', 'name="ts"', 'name="tsg"', 'name="consentimiento"', 'name="telefono"'] as $needed) {
    if (!str_contains($formSource, $needed)) {
        $fail("lead: partials/form.php ya no trae el campo {$needed}");
    }
}
if (!str_contains($formSource, 'action="/cotizar/enviar.php" method="post"')) {
    $fail('lead: partials/form.php ya no postea a /cotizar/enviar.php por POST');
}
// ---- fase 10: alta de proveedores (decisión §1.17) ----------------------------------
// El payload del PROVEEDOR es otro contrato: lleva fields.tipo, empresa y rubros, y su
// propia versión de consentimiento. El del COMPRADOR no puede contaminarse con nada de eso.
if (array_key_exists('tipo', $payload) || array_key_exists('tipo', $payload['fields'])) {
    $fail("lead: el payload del COMPRADOR incluye 'tipo' — sólo el alta de proveedor lo manda (decisión §1.17)");
}

$supplierPayload = lead_build_supplier_payload([
    'phone_raw'       => '0981 123 456',
    'idempotency_key' => $keyA,
    'nombre'          => 'Ana Benítez',
    'empresa'         => 'Corralón San Blas',
    'rubros'          => ['hierro', 'aridos'],
    'ciudad'          => 'Luque',
    'mensaje'         => 'Entregamos con camión propio',
    'page_url'        => 'https://materiales.com.py/proveedores/',
    'attribution'     => [],
    'consent_at'      => '2026-06-15T10:30:00+00:00',
]);
$is('proveedor: fields.tipo', $supplierPayload['fields']['tipo'] ?? null, 'proveedor');
$is('proveedor: fields.empresa', $supplierPayload['fields']['empresa'] ?? null, 'Corralón San Blas');
$is('proveedor: fields.rubros', $supplierPayload['fields']['rubros'] ?? null, 'hierro,aridos');
$is('proveedor: fields.ciudad', $supplierPayload['fields']['ciudad'] ?? null, 'Luque');
$is('proveedor: source', $supplierPayload['source'] ?? null, 'site:materiales');
$is('proveedor: message', $supplierPayload['message'] ?? null, 'Entregamos con camión propio');
$is(
    'proveedor: fields.consent con SU versión',
    $supplierPayload['fields']['consent'] ?? null,
    site('consent_version_proveedor') . ' @ 2026-06-15T10:30:00+00:00'
);
// Un alta de proveedor no es un pedido de cotización: nada de material, categoría ni banda.
foreach (['material', 'categoria', 'material_nombre', 'categoria_nombre', 'cantidad', 'presupuesto_band'] as $forbidden) {
    if (array_key_exists($forbidden, $supplierPayload['fields'])) {
        $fail("lead: el payload del proveedor incluye '{$forbidden}' — no está pidiendo cotización");
    }
}
foreach (['pipeline', 'stage', 'owner', 'tag'] as $forbidden) {
    if (array_key_exists($forbidden, $supplierPayload) || array_key_exists($forbidden, $supplierPayload['fields'])) {
        $fail("lead: el payload del proveedor incluye '{$forbidden}' — el ruteo se configura en el CRM");
    }
}
foreach ($supplierPayload as $key => $value) {
    if ($value === '' || $value === null) {
        $fail("lead: payload de proveedor['{$key}'] va vacío — la API rechaza '' en vez de ignorarlo");
    }
}

// Rubros: sólo categorías publicadas, sin inventos ni duplicados, en el orden del catálogo.
$is('proveedor: rubros descarta lo que no existe', lead_supplier_rubros(['hierro', 'no-existe']), ['hierro']);
$is('proveedor: rubros descarta duplicados', lead_supplier_rubros(['hierro', 'hierro']), ['hierro']);
$is('proveedor: rubros vacío', lead_supplier_rubros([]), []);
$is('proveedor: rubros ignora basura', lead_supplier_rubros('../../etc/passwd'), []);
$proximas = array_keys(array_filter($categories, static fn(array $c): bool => ($c['status'] ?? '') !== 'activa'));
if ($proximas !== []) {
    $is('proveedor: rubros descarta categorías proxima', lead_supplier_rubros([$proximas[0]]), []);
}

// Guard del texto de consentimiento del proveedor, igual que el del comprador.
$supplierFormSource = (string) @file_get_contents($root . '/partials/form-proveedor.php');
$supplierConsentText = 'Acepto que Materiales.com.py guarde mis datos para contactarme sobre pedidos de';
if (!str_contains($supplierFormSource, $supplierConsentText)) {
    $fail('lead: cambió el texto de consentimiento de partials/form-proveedor.php (parada §4.4 — sincronizar con la política y con consent_version_proveedor)');
}
foreach (['name="website"', 'name="ts"', 'name="tsg"', 'name="consentimiento"', 'name="telefono"', 'name="empresa"', 'name="rubros[]"', 'name="tipo" value="proveedor"'] as $needed) {
    if (!str_contains($supplierFormSource, $needed)) {
        $fail("lead: partials/form-proveedor.php ya no trae el campo {$needed}");
    }
}
if (($site['consent_version_proveedor'] ?? '') !== 'proveedor-v1') {
    $fail("lead: consent_version_proveedor cambió a '" . ($site['consent_version_proveedor'] ?? '') . "' — sincronizar con la política de privacidad antes (plan §8.6)");
}
foreach (['supplier_pitch', 'supplier_categories_note'] as $key) {
    if (!array_key_exists($key, $site)) {
        $fail("data/site.php: falta la clave '{$key}' (fase 10 — la página de proveedores la lee)");
    }
}

if (($site['consent_version'] ?? '') !== 'proveedores-v1') {
    $fail("lead: consent_version cambió a '" . ($site['consent_version'] ?? '') . "' — sincronizar con la política de privacidad antes (plan §8.6)");
}

// ---- reporte -------------------------------------------------------------------------
if ($errors !== []) {
    fwrite(STDERR, "SMOKE FAIL (" . count($errors) . ")\n");
    foreach ($errors as $error) {
        fwrite(STDERR, "  - {$error}\n");
    }
    exit(1);
}

printf(
    "SMOKE OK — %d categorías (%d activas), %d materiales (%d activos), %d guías; %d slugs únicos en /materiales/\n",
    count($categories),
    count(array_filter($categories, static fn(array $c): bool => $c['status'] === 'activa')),
    count($materials),
    count(array_filter($materials, static fn(array $m): bool => $m['status'] === 'activa')),
    count($guides),
    count($categories) + count($materials)
);
exit(0);
