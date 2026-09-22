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
$calculators = is_file($root . '/data/calculators.php') ? $load('calculators') : [];

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

// ---- fase 12: calculadoras (decisión §1.21, CONTENT-SPEC §12) -----------------------
// El archivo puede no existir (lo crea la fase 12): si existe, se valida entero.
foreach ($calculators as $slug => $calculator) {
    $where = "calculators.php[{$slug}]";
    $checkEntry('calculators.php', (string) $slug, $calculator, [
        'name', 'status', 'order', 'title', 'meta', 'keyword', 'intro',
        'inputs', 'outputs', 'formula_note', 'assumptions', 'related', 'faq',
        'cta_material', 'cta_quantity_template',
    ]);

    if (count($calculator['faq'] ?? []) < 3 || count($calculator['faq'] ?? []) > 5) {
        $fail(sprintf('%s: %d preguntas en faq (se esperan 3 a 5)', $where, count($calculator['faq'] ?? [])));
    }
    $lastQ = ($calculator['faq'] ?? []) !== [] ? (string) (end($calculator['faq'])['q'] ?? '') : '';
    if (!str_starts_with($lastQ, '¿Cuánto cuesta')) {
        $fail("{$where}: la última faq tiene que ser '¿Cuánto cuesta …?' (CONTENT-SPEC §11.3)");
    }

    foreach (['inputs', 'outputs', 'assumptions', 'related'] as $listKey) {
        if (($calculator[$listKey] ?? []) === []) {
            $fail("{$where}: '{$listKey}' vacío");
        }
    }
    if (trim((string) ($calculator['formula_note'] ?? '')) === '') {
        $fail("{$where}: 'formula_note' vacío — la cuenta tiene que estar visible (CONTENT-SPEC §12)");
    }

    $outputIds = [];
    foreach ($calculator['outputs'] ?? [] as $i => $output) {
        if (!preg_match('/^[a-z0-9_]+$/', (string) ($output['id'] ?? ''))) {
            $fail("{$where}: outputs[{$i}] sin id válido");
            continue;
        }
        $outputIds[] = (string) $output['id'];
    }
    foreach ($calculator['inputs'] ?? [] as $i => $input) {
        if (!preg_match('/^[a-z0-9_]+$/', (string) ($input['id'] ?? ''))) {
            $fail("{$where}: inputs[{$i}] sin id válido");
        }
        if (($input['type'] ?? 'number') === 'select' && ($input['options'] ?? []) === []) {
            $fail("{$where}: inputs[{$i}] es un selector sin opciones");
        }
    }

    // La plantilla de cantidad sólo puede nombrar salidas que existen: si no, el formulario
    // se precarga con un hueco y el proveedor recibe un pedido sin cantidad.
    preg_match_all('/\{([a-z0-9_]+)\}/i', (string) ($calculator['cta_quantity_template'] ?? ''), $placeholders);
    foreach ($placeholders[1] ?? [] as $placeholder) {
        if (!in_array($placeholder, $outputIds, true)) {
            $fail("{$where}: cta_quantity_template usa '{{$placeholder}}' y no hay una salida con ese id");
        }
    }

    $ctaMaterial = (string) ($calculator['cta_material'] ?? '');
    if ($ctaMaterial !== '' && !isset($materials[$ctaMaterial]) && !isset($categories[$ctaMaterial])) {
        $fail("{$where}: cta_material '{$ctaMaterial}' no existe en materials.php ni en categories.php");
    }

    foreach ($calculator['related'] ?? [] as $target) {
        if (!isset($materials[$target]) && !isset($categories[$target]) && !isset($guides[$target])) {
            $fail("{$where}: related '{$target}' no existe en materials.php, categories.php ni guides.php");
        }
    }

    $contentFile = $root . '/content/calculadoras/' . $slug . '.php';
    if (($calculator['status'] ?? '') === 'activa' && !is_file($contentFile)) {
        $fail("{$where}: está 'activa' pero falta content/calculadoras/{$slug}.php");
    }
    if (is_file($contentFile)) {
        $body = (string) @file_get_contents($contentFile);
        if (!preg_match('#<script type="application/json" data-calc>(.*?)</script>#s', $body, $m)) {
            $fail("content/calculadoras/{$slug}.php: falta el bloque <script type=\"application/json\" data-calc>");
        } else {
            $formula = json_decode(trim($m[1]), true);
            if (!is_array($formula) || !is_array($formula['outputs'] ?? null)) {
                $fail("content/calculadoras/{$slug}.php: el JSON de la fórmula no parsea o no trae 'outputs'");
            } else {
                $formulaIds = array_map(static fn(array $o): string => (string) ($o['id'] ?? ''), $formula['outputs']);
                foreach ($outputIds as $outputId) {
                    if (!in_array($outputId, $formulaIds, true)) {
                        $fail("content/calculadoras/{$slug}.php: la fórmula no calcula la salida '{$outputId}' declarada en el dato");
                    }
                }
            }
        }
    }
}

// La frase de cierre de TODO resultado es literal (CONTENT-SPEC §12): vive una sola vez, en
// la plantilla, así que se verifica una sola vez.
if ($calculators !== []) {
    $calcTemplate = (string) @file_get_contents($root . '/calculadoras/index.php');
    if (!str_contains($calcTemplate, 'Es una referencia — confirmá con tu proveedor.')) {
        $fail('calculadoras/index.php: falta la frase literal "Es una referencia — confirmá con tu proveedor." (CONTENT-SPEC §12)');
    }
}

foreach (glob($root . '/content/calculadoras/*.php') ?: [] as $file) {
    $slug = basename($file, '.php');
    if (!isset($calculators[$slug])) {
        $fail("content/calculadoras/{$slug}.php no tiene entrada en data/calculators.php");
    }
}

// ---- fase 11: imágenes declaradas (decisión §1.20) ----------------------------------
// Una imagen DECLARADA cuyo archivo no está en disco es una promesa rota: el héroe y el
// og:image de esa página se caen al fallback sin que nadie se entere. Declararla y subirla
// son dos pasos, y este check es el que los ata. No declarar nada sigue siendo válido.
$checkImage = static function (string $where, string $value) use ($root, $fail): void {
    if ($value === '') {
        return;
    }
    // Ruta base sin extensión (fase de imágenes responsive): cada base tiene AVIF+WebP en
    // 640/1280/1920, generados por webimg. El WebP de 1280 siempre existe si la conversión
    // corrió, así que es el archivo que este check usa como prueba de existencia.
    if (!preg_match('#^assets/img/[a-z0-9/_-]+$#', $value)) {
        $fail("{$where}: image '{$value}' no cumple el formato assets/img/… (minúsculas, sin espacios, sin extensión)");
        return;
    }
    if (!is_file($root . '/' . $value . '-1280.webp')) {
        $fail("{$where}: image '{$value}' declarada pero {$value}-1280.webp no existe en el repo");
    }
};

foreach (['categories.php' => $categories, 'materials.php' => $materials, 'guides.php' => $guides, 'calculators.php' => $calculators] as $file => $entries) {
    foreach ($entries as $slug => $entry) {
        $checkImage("{$file}[{$slug}]", trim((string) ($entry['image'] ?? '')));
    }
}
$checkImage('site.php[hero_image]', trim((string) ($site['hero_image'] ?? '')));

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

// storage/ temporal: smoke NUNCA escribe en el storage/ real (el repo es el docroot, así que
// correr esto en el servidor le agregaba líneas de prueba al leads.log de producción).
$smokeStorage = getenv('MATERIALES_STORAGE_DIR');
$smokeOwnsStorage = $smokeStorage === false || $smokeStorage === '';
if ($smokeOwnsStorage) {
    $smokeStorage = sys_get_temp_dir() . '/materiales-smoke-storage-' . bin2hex(random_bytes(4));
    putenv('MATERIALES_STORAGE_DIR=' . $smokeStorage);
}
register_shutdown_function(static function () use ($smokeStorage, $smokeOwnsStorage): void {
    if ($smokeOwnsStorage && is_dir((string) $smokeStorage)) {
        exec('rm -rf ' . escapeshellarg((string) $smokeStorage));
    }
});

require $root . '/partials/init.php';
require $root . '/partials/lead.php';

// storage/ recién creado SIEMPRE queda con su .htaccess, venga por donde venga la escritura
// (antes el primer request lo creaba desde el secreto del formulario, sin .htaccess).
lead_form_secret_auto();
if (!is_file(STORAGE_DIR . '/.htaccess')) {
    $fail('lead: storage/ se creó sin su .htaccess de Require all denied');
}

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
$is('sello vencido', lead_form_stamp_reason($stamp['ts'], $stamp['sig'], $t0 + LEAD_STAMP_TTL + 60), 'vencido');
$is('sello de 24 h sigue válido', lead_form_stamp_reason($stamp['ts'], $stamp['sig'], $t0 + 86400), '');

// ---- idempotencia por pedido: dos materiales en la misma hora son dos leads ----------
if (lead_idempotency_key('+595981123456', $t0, 'cemento') === lead_idempotency_key('+595981123456', $t0, 'hierro')) {
    $fail('lead: idempotency_key igual para dos materiales distintos en la misma hora — el segundo pedido se pierde como duplicado');
}
$is('idempotency_key con scope estable en la hora',
    lead_idempotency_key('+595981123456', $t0, 'cemento'),
    lead_idempotency_key('+595981123456', $t0 + 900, 'cemento'));

// ---- teléfono hacia el CRM: lo tipeado, pero sólo caracteres de teléfono (KNOWN-ISSUES #28)
$is('phone_for_crm conserva el formato tipeado', lead_phone_for_crm('0981 123-456'), '0981 123-456');
$is('phone_for_crm quita marcado', lead_phone_for_crm('<b>0981123456</b>'), '0981123456');
$is('phone_for_crm conserva +595 y paréntesis', lead_phone_for_crm('+595 (981) 123.456'), '+595 (981) 123.456');

// ---- token de conversión de /gracias/: firmado, uno tipeado a mano no cuenta ----------
$is('token de conversión válido', lead_conversion_token_valid(lead_conversion_token()), true);
$is('token de conversión inventado', lead_conversion_token_valid('0123456789abcdef'), false);
$is('token de conversión con otra forma', lead_conversion_token_valid('xyz'), false);
// Un sello robado de otra página sigue chocando con el mínimo: es firma Y tiempo, no una sola cosa.
$is('sello del futuro', lead_form_stamp_reason((string) ($t0 + 3600), lead_form_stamp($t0 + 3600)['sig'], $t0), 'sello');

// ---- throttle por IP: proceso aislado para definir STORAGE_DIR temporal -------------
// Como el fixture de replay, nunca escribe en el storage real. Sin sleep: reloj inyectado.
$throttleFixtureDir = sys_get_temp_dir() . '/materiales-smoke-throttle-' . bin2hex(random_bytes(4));
if (!@mkdir($throttleFixtureDir, 0770, true)) {
    $fail('lead: no se pudo crear el directorio temporal del throttle');
} else {
    try {
        $throttleCode = <<<'PHP'
define('STORAGE_DIR', $argv[1]);
define('CONFIG_DIR', STORAGE_DIR . '/config');
require $argv[2] . '/partials/lead.php';
$errors = [];
$is = static function (string $what, $actual, $expected) use (&$errors): void {
    if ($actual !== $expected) {
        $errors[] = $what;
    }
};
$t0 = 1781520000;
$keyA = str_repeat('a', 64);
$keyB = str_repeat('b', 64);
$is('IP vacía pasa', lead_ip_throttled('', $keyA, $t0), false);
$is('IP vacía no crea directorio', is_dir(STORAGE_DIR . '/throttle'), false);
$is('primera IP pasa', lead_ip_throttled('192.0.2.1', $keyA, $t0), false);
$path = STORAGE_DIR . '/throttle/' . lead_ip_fingerprint('192.0.2.1');
$is('archivo contiene clave y timestamp', @file_get_contents($path), $keyA . ' ' . $t0 . "\n");
$is('throttle protegido', @file_get_contents(STORAGE_DIR . '/throttle/.htaccess'), file_get_contents($argv[2] . '/tools/.htaccess'));
$is('misma IP y clave pasa', lead_ip_throttled('192.0.2.1', $keyA, $t0 + 1), false);
$is('reintento no reescribe', @file_get_contents($path), $keyA . ' ' . $t0 . "\n");
// Una IP compartida (CGNAT) puede mandar varios pedidos distintos: el límite es por cantidad.
for ($i = 1; $i < LEAD_IP_MAX_LEADS; $i++) {
    $is("pedido distinto #{$i} de la misma IP pasa", lead_ip_throttled('192.0.2.1', str_repeat(dechex($i + 1), 64), $t0 + $i), false);
}
$is('pasado el máximo, otra clave queda limitada', lead_ip_throttled('192.0.2.1', $keyB, $t0 + 10), true);
$is('una clave ya aceptada sigue pasando', lead_ip_throttled('192.0.2.1', $keyA, $t0 + 11), false);
$is('otra IP pasa', lead_ip_throttled('192.0.2.2', $keyB, $t0 + 1), false);
$is('antes del límite sigue bloqueada', lead_ip_throttled('192.0.2.1', $keyB, $t0 + LEAD_IP_WINDOW_SECONDS - 1), true);
$is('vencida la ventana vuelve a pasar', lead_ip_throttled('192.0.2.1', $keyB, $t0 + LEAD_IP_WINDOW_SECONDS + LEAD_IP_MAX_LEADS), false);
$is('vencida la ventana sólo queda el registro nuevo', @file_get_contents($path), $keyB . ' ' . ($t0 + LEAD_IP_WINDOW_SECONDS + LEAD_IP_MAX_LEADS) . "\n");
foreach (['timestamp-invalido', (string) $t0, $keyA . " ayer", "no-hex " . $t0] as $corrupt) {
    file_put_contents($path, $corrupt);
    $is('contenido inválido deja pasar', lead_ip_throttled('192.0.2.1', $keyA, $t0), false);
    $is('contenido inválido se repara', @file_get_contents($path), $keyA . ' ' . $t0 . "\n");
}
unlink($path);
mkdir($path);
$is('fallo al abrir deja pasar', lead_ip_throttled('192.0.2.1', $keyA, $t0), false);
rmdir($path);
echo $errors === [] ? 'THROTTLE OK' : implode("\n", $errors);
PHP;
        $cmd = sprintf(
            '%s -r %s %s %s 2>&1',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($throttleCode),
            escapeshellarg($throttleFixtureDir),
            escapeshellarg($root)
        );
        $is('throttle aislado', trim((string) shell_exec($cmd)), 'THROTTLE OK');
    } finally {
        foreach (glob($throttleFixtureDir . '/throttle/*') ?: [] as $file) {
            is_dir($file) ? @rmdir($file) : @unlink($file);
        }
        @unlink($throttleFixtureDir . '/throttle/.htaccess');
        @rmdir($throttleFixtureDir . '/throttle');
        @unlink($throttleFixtureDir . '/.form-secret');
        @rmdir($throttleFixtureDir);
    }
}

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

// ---- fase 14: tools/replay-leads.php (decisión §1.26) --------------------------------
// Fixture en un dir temporal — nunca toca storage/leads.log real. Sólo se verifica la
// LÓGICA DE SELECCIÓN (--dry-run, sin red): un 'fallo_crm' pendiente aparece, y una vez que
// su idempotency_key ya está en replayed.log con ok:true, deja de aparecer.
$replayFixtureDir = sys_get_temp_dir() . '/materiales-smoke-replay-' . bin2hex(random_bytes(4));
if (!@mkdir($replayFixtureDir, 0770, true)) {
    $fail('replay-leads: no se pudo crear el directorio temporal del fixture');
} else {
    $fixtureKey = 'smoke-' . bin2hex(random_bytes(4));
    $fixtureLog = $replayFixtureDir . '/leads.log';
    $fixtureReplayed = $replayFixtureDir . '/replayed.log';
    file_put_contents($fixtureLog, json_encode([
        'ts' => gmdate('c'), 'outcome' => 'fallo_crm',
        'crm' => ['status' => 0, 'ms' => 1, 'error' => 'timeout', 'body' => ''],
        'payload' => ['idempotency_key' => $fixtureKey, 'phone' => '0981123456', 'source' => 'site:materiales'],
    ], JSON_UNESCAPED_UNICODE) . "\n");

    $runReplay = static function () use ($replayFixtureDir, $fixtureLog, $fixtureReplayed): string {
        $cmd = sprintf(
            '%s %s --dry-run --log=%s --replayed=%s 2>&1',
            escapeshellarg(PHP_BINARY),
            escapeshellarg(dirname(__DIR__) . '/tools/replay-leads.php'),
            escapeshellarg($fixtureLog),
            escapeshellarg($fixtureReplayed)
        );
        return (string) shell_exec($cmd);
    };

    $out1 = $runReplay();
    if (!str_contains($out1, $fixtureKey) || !str_contains($out1, '1 pendientes')) {
        $fail("replay-leads: no detectó el fallo_crm pendiente del fixture (salida: {$out1})");
    }

    file_put_contents($fixtureReplayed, json_encode([
        'ts' => gmdate('c'), 'idempotency_key' => $fixtureKey, 'ok' => true, 'status' => 200, 'error' => '',
    ], JSON_UNESCAPED_UNICODE) . "\n");

    $out2 = $runReplay();
    if (!str_contains($out2, '0 pendientes')) {
        $fail("replay-leads: sigue contando como pendiente un idempotency_key ya reenviado con éxito (salida: {$out2})");
    }

    $fixtureSoloKey = 'smoke-' . bin2hex(random_bytes(4));
    file_put_contents($fixtureLog, json_encode([
        'ts' => gmdate('c'), 'outcome' => 'solo_log',
        'payload' => ['idempotency_key' => $fixtureSoloKey, 'phone' => '0981123456', 'source' => 'site:materiales'],
    ], JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);

    $out3 = $runReplay();
    if (!str_contains($out3, $fixtureSoloKey) || !str_contains($out3, '1 pendientes')) {
        $fail("replay-leads: no detectó el solo_log pendiente del fixture (salida: {$out3})");
    }

    // Un solo_log de hace 200 horas no debe reintentarse con el tope por defecto (72h): si se
    // configura el CRM después de meses de log-only, no hay que volcarle todo el historial de
    // golpe a los proveedores.
    $fixtureOldKey = 'smoke-' . bin2hex(random_bytes(4));
    file_put_contents($fixtureLog, json_encode([
        'ts' => gmdate('c', time() - 200 * 3600), 'outcome' => 'solo_log',
        'payload' => ['idempotency_key' => $fixtureOldKey, 'phone' => '0981123456', 'source' => 'site:materiales'],
    ], JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);

    $out4 = $runReplay();
    if (str_contains($out4, $fixtureOldKey) || !str_contains($out4, '1 descartados por antiguos')) {
        $fail("replay-leads: un solo_log de 200h debería descartarse por antiguo, no reintentarse (salida: {$out4})");
    }

    @unlink($fixtureLog);
    @unlink($fixtureReplayed);
    @rmdir($replayFixtureDir);
}

// ---- aviso de lead: asunto sin datos personales, cuerpo con lo necesario para contestar
$noticeRecord = ['ts' => gmdate('c'), 'outcome' => 'solo_log', 'phone_e164' => '+595981123456', 'payload' => $payload];
$noticeSubject = lead_notify_subject($noticeRecord);
if (!str_contains($noticeSubject, 'SIN CRM') || str_contains($noticeSubject, '981')) {
    $fail("lead: asunto del aviso inesperado o con el teléfono: {$noticeSubject}");
}
$noticeText = lead_notify_text($noticeRecord);
foreach (['Ana Benítez', '+595981123456', 'https://wa.me/595981123456', 'Luque'] as $needed) {
    if (!str_contains($noticeText, $needed)) {
        $fail("lead: el aviso de lead no incluye '{$needed}'");
    }
}
// Sin notify_email ni Telegram configurados, lead_notify() no hace nada y no lanza.
lead_notify($noticeRecord);

$sup = lead_notify_subject(['outcome' => 'retenido', 'payload' => ['fields' => ['tipo' => 'proveedor']]]);
if (!str_contains($sup, 'retenido') || !str_contains($sup, 'proveedor')) {
    $fail("lead: asunto del aviso de proveedor retenido inesperado: {$sup}");
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
foreach (['name="hp_extra"', 'name="ts"', 'name="tsg"', 'name="consentimiento"', 'name="telefono"'] as $needed) {
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
foreach (['name="hp_extra"', 'name="ts"', 'name="tsg"', 'name="consentimiento"', 'name="telefono"', 'name="empresa"', 'name="rubros[]"', 'name="tipo" value="proveedor"'] as $needed) {
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
    "SMOKE OK — %d categorías (%d activas), %d materiales (%d activos), %d guías, %d calculadoras; %d slugs únicos en /materiales/\n",
    count($categories),
    count(array_filter($categories, static fn(array $c): bool => $c['status'] === 'activa')),
    count($materials),
    count(array_filter($materials, static fn(array $m): bool => $m['status'] === 'activa')),
    count($guides),
    count($calculators),
    count($categories) + count($materials)
);
exit(0);
