<?php
/**
 * partials/schema.php — constructores de JSON-LD (plan §6).
 *
 * Reglas duras:
 *  - Ningún campo se emite con dato vacío: NAP incompleto se omite, nunca se inventa.
 *  - NUNCA aggregateRating ni review mientras no haya reseñas reales visibles en la página.
 *  - Product en páginas de material va SIN offers (no publicamos precios).
 *  - FAQPage sólo si las preguntas están visibles en la página.
 *  - Teléfonos en E.164 (+595...).
 */

declare(strict_types=1);

/** Quita recursivamente claves con valor vacío ('' , [] , null). */
function schema_prune(array $node): array
{
    foreach ($node as $key => $value) {
        if (is_array($value)) {
            $value = schema_prune($value);
            $node[$key] = $value;
        }
        if ($value === '' || $value === [] || $value === null) {
            unset($node[$key]);
        }
    }
    return $node;
}

/** LocalBusiness sitewide, patrón service-area. */
function schema_local_business(): array
{
    $site    = site();
    $address = $site['address'] ?? [];

    return schema_prune([
        '@context'    => 'https://schema.org',
        '@type'       => 'LocalBusiness',
        '@id'         => url('/') . '#business',
        'name'        => $site['legal_name'] !== '' ? $site['legal_name'] : $site['brand'],
        'url'         => url('/'),
        'description' => $site['tagline'] ?? '',
        'telephone'   => $site['phone'] ?? '',
        'email'       => $site['email'] ?? '',
        'address'     => schema_prune([
            '@type'           => 'PostalAddress',
            'streetAddress'   => $address['street'] ?? '',
            'addressLocality' => $address['locality'] ?? '',
            'addressRegion'   => $address['region'] ?? '',
            'addressCountry'  => $address['country'] ?? '',
        ]),
        'areaServed'  => array_map(
            static fn(string $city): array => ['@type' => 'City', 'name' => $city],
            $site['area_served'] ?? []
        ),
    ]);
}

/** WebSite (sólo homepage). SearchAction únicamente si existe búsqueda interna real. */
function schema_website(): array
{
    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'WebSite',
        'name'       => site('brand'),
        'url'        => url('/'),
        'inLanguage' => site('locale', 'es-PY'),
    ];

    if (site('has_search', false) === true) {
        $schema['potentialAction'] = [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => url('/buscar/') . '?q={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ];
    }

    return $schema;
}

/** BreadcrumbList. $crumbs = [[nombre, ruta|null], ...]; el último ítem omite 'item'. */
function schema_breadcrumbs(array $crumbs): array
{
    $items = [];
    $position = 1;
    foreach ($crumbs as [$name, $path]) {
        $item = ['@type' => 'ListItem', 'position' => $position++, 'name' => $name];
        if ($path !== null) {
            $item['item'] = url($path);
        }
        $items[] = $item;
    }

    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}

/** ItemList para /materiales/ y páginas de categoría. $items = [[nombre, ruta], ...]. */
function schema_item_list(string $name, string $canonical, array $items): array
{
    $elements = [];
    $position = 1;
    foreach (array_slice($items, 0, 20) as [$itemName, $path]) {
        $elements[] = [
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => $itemName,
            'url'      => url($path),
        ];
    }

    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => $name,
        'url'             => url($canonical),
        'numberOfItems'   => count($items),
        'itemListElement' => $elements,
    ];
}

/** Product mínimo para páginas de material: sin offers (precios no publicados, plan §6). */
function schema_product(string $slug, array $material): array
{
    return schema_prune([
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $material['name'] ?? '',
        'description' => $material['meta'] ?? '',
        'url'         => url('/materiales/' . $slug . '/'),
        'image'       => $material['image'] ?? '',
        'category'    => data('categories')[$material['category']]['name'] ?? '',
    ]);
}

/** FAQPage. $faq = [['q' => ..., 'a' => ...], ...]. Devuelve [] si no hay preguntas. */
function schema_faq(array $faq): array
{
    if ($faq === []) {
        return [];
    }

    return [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(static fn(array $item): array => [
            '@type'          => 'Question',
            'name'           => $item['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
        ], $faq),
    ];
}

/** Imprime cada bloque en su propio <script type="application/ld+json">. */
function schema_render(array $blocks): void
{
    foreach ($blocks as $block) {
        if ($block === []) {
            continue;
        }
        echo '<script type="application/ld+json">',
             json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
             "</script>\n";
    }
}
