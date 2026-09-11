<?php
/**
 * partials/init.php — bootstrap de cada request: rutas, carga de datos y helpers.
 * Se incluye UNA vez al comienzo de cada página. No emite salida.
 */

declare(strict_types=1);

// partials/init.php → PUBLIC_ROOT y APP_ROOT son el mismo directorio: el Git de Hostinger
// en hosting compartido sólo despliega DENTRO de public_html, así que el repo entero (y
// por lo tanto data/, content/, config/, storage/) vive en el docroot. Quedan no-públicos
// por las reglas [F] de .htaccess, no por estar fuera de esta carpeta. Ver DEPLOY.md.
define('PUBLIC_ROOT', dirname(__DIR__));
define('APP_ROOT', PUBLIC_ROOT);
define('DATA_DIR', APP_ROOT . '/data');
define('CONTENT_DIR', APP_ROOT . '/content');
define('CONFIG_DIR', APP_ROOT . '/config');
define('STORAGE_DIR', APP_ROOT . '/storage');

/** Carga (y cachea) un archivo de datos: data('categories') → data/categories.php */
function data(string $name): array
{
    static $cache = [];
    if (!isset($cache[$name])) {
        $file = DATA_DIR . '/' . $name . '.php';
        if (!is_file($file)) {
            throw new RuntimeException("Archivo de datos faltante: {$name}.php");
        }
        $cache[$name] = require $file;
    }
    return $cache[$name];
}

/** Valor de data/site.php. site() devuelve todo el array. */
function site(?string $key = null, mixed $default = '') : mixed
{
    $site = data('site');
    if ($key === null) {
        return $site;
    }
    return $site[$key] ?? $default;
}

/** Escape para HTML. Usar SIEMPRE al imprimir cualquier valor. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** URL absoluta a partir de una ruta interna ('/materiales/hierro/'). */
function url(string $path = '/'): string
{
    return rtrim((string) site('base_url'), '/') . '/' . ltrim($path, '/');
}

/** true si la página está publicada (entra en sitemap y es indexable). */
function is_published(array $entry): bool
{
    return ($entry['status'] ?? 'proxima') === 'activa';
}

/** Categorías ordenadas por 'order'. */
function categories_ordered(): array
{
    $categories = data('categories');
    uasort($categories, static fn(array $a, array $b): int => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));
    return $categories;
}

/** Materiales de una categoría, en el orden en que están declarados. */
function materials_in(string $categorySlug): array
{
    return array_filter(
        data('materials'),
        static fn(array $m): bool => ($m['category'] ?? '') === $categorySlug
    );
}

/**
 * Define la página actual. Devuelve el array normalizado que header.php y footer.php leen.
 *
 * Claves: title, meta, canonical (ruta interna), noindex, h1, breadcrumbs [[nombre, ruta|null]],
 * schema (lista de arrays JSON-LD), body_class.
 */
function page(array $page = []): array
{
    static $current = null;
    if ($page !== []) {
        $current = $page + [
            'title'       => site('brand'),
            'meta'        => site('tagline'),
            'canonical'   => '/',
            'noindex'     => false,
            'h1'          => '',
            'breadcrumbs' => [],
            'schema'      => [],
            'body_class'  => '',
        ];
    }
    return $current ?? page(['title' => site('brand')]);
}

/** Envía el status HTTP y termina la request sirviendo la página 404. */
function not_found(): never
{
    http_response_code(404);
    require PUBLIC_ROOT . '/404.php';
    exit;
}
