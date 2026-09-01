<?php
/**
 * tools/smoke.php — chequeo de integridad de los archivos de datos. Es el check requerido en
 * CI junto a `php -l` (plan §1 fase 1).
 *
 * Verifica: que los cuatro archivos de datos carguen, que los slugs sean únicos en el
 * namespace COMPARTIDO categorías+materiales, el formato de slug, la coherencia de status,
 * las referencias entre archivos, y los límites de title/meta.
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
