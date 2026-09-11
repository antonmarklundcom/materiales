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
 * Guías publicadas que apuntan a esta página de dinero (fase 11, decisión §1.19).
 *
 * Es el inverso de `guides[].related`: la guía declara a qué material o categoría sirve, y
 * la página de dinero la muestra sin que nadie tipee el enlace dos veces. Se acepta tanto el
 * slug propio como el de su categoría, así que una guía que apunta a `hierro` aparece en la
 * categoría y en todos sus materiales.
 *
 * @return array<string, array> slug de guía ⇒ entrada, ordenadas por 'order'
 */
function guides_for(string $slug, string $categorySlug = ''): array
{
    $targets = array_filter([$slug, $categorySlug], static fn(string $s): bool => $s !== '');

    $matches = array_filter(
        data('guides'),
        static fn(array $guide): bool => is_published($guide)
            && array_intersect($targets, (array) ($guide['related'] ?? [])) !== []
    );
    uasort($matches, static fn(array $a, array $b): int => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));

    return $matches;
}

/**
 * Calculadoras publicadas que apuntan a esta página de dinero. Misma forma que guides_for().
 *
 * data/calculators.php lo crea la fase 12: hasta entonces el archivo no existe y esto
 * devuelve [] sin romper nada (por eso el is_file y no un data() directo, que lanzaría).
 *
 * @return array<string, array>
 */
function calculators_for(string $slug, string $categorySlug = ''): array
{
    if (!is_file(DATA_DIR . '/calculators.php')) {
        return [];
    }

    $targets = array_filter([$slug, $categorySlug], static fn(string $s): bool => $s !== '');

    $matches = array_filter(
        data('calculators'),
        static fn(array $calculator): bool => is_published($calculator)
            && array_intersect($targets, (array) ($calculator['related'] ?? [])) !== []
    );
    uasort($matches, static fn(array $a, array $b): int => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));

    return $matches;
}

/**
 * Imagen de una entrada, con la herencia de la decisión §1.20 (una foto por CATEGORÍA por
 * defecto): un material sin `image` propia hereda la de su categoría; una guía hereda la de
 * su primera página de dinero en `related[]`.
 *
 * Devuelve la ruta relativa ('assets/img/cat/hierro.jpg') o null si no hay imagen declarada
 * o si el archivo no está en disco — así una imagen declarada pero todavía no subida no
 * rompe la página: se renderiza exactamente como antes (héroe de paleta, og-default.jpg).
 *
 * @param string $type 'categoria' | 'material' | 'guia' (cualquier otro valor: sin herencia,
 *                     se usa tal cual lo declarado — así lo llama la home con hero_image)
 */
function image_for(array $entry, string $type): ?string
{
    $candidate = trim((string) ($entry['image'] ?? ''));

    if ($candidate === '' && $type === 'material') {
        $category  = data('categories')[(string) ($entry['category'] ?? '')] ?? [];
        $candidate = trim((string) ($category['image'] ?? ''));
    }

    if ($candidate === '' && $type === 'guia') {
        foreach ((array) ($entry['related'] ?? []) as $target) {
            $target = (string) $target;
            if (isset(data('categories')[$target])) {
                $inherited = image_for(data('categories')[$target], 'categoria');
            } elseif (isset(data('materials')[$target])) {
                $inherited = image_for(data('materials')[$target], 'material');
            } else {
                continue;
            }
            if ($inherited !== null) {
                return $inherited;
            }
        }
    }

    if ($candidate === '' || !is_file(PUBLIC_ROOT . '/' . $candidate)) {
        return null;
    }
    return $candidate;
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
            // Ruta relativa de la imagen de la página (fase 11). '' ⇒ og-default.jpg.
            'image'       => '',
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
