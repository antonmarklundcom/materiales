<?php
/**
 * partials/schema.php — constructores de JSON-LD (plan §6).
 *
 * Reglas duras:
 *  - Ningún campo se emite con dato vacío: NAP incompleto se omite, nunca se inventa.
 *  - NUNCA aggregateRating ni review mientras no haya reseñas reales visibles en la página.
 *  - Sin Product en páginas de material: sin precios publicados sería un ítem inválido (S3).
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

/**
 * Organization sitewide (S10). Antes era LocalBusiness, que pide dirección y horarios de un
 * local al que se puede ir; esto es un intermediario online sin local abierto al público, así
 * que Organization + contactPoint + areaServed es lo que describe la realidad. El @id lo
 * referencian WebSite.publisher y Article.author/publisher.
 */
function schema_organization(): array
{
    $site = site();
    $logo = is_file(PUBLIC_ROOT . '/apple-touch-icon.png') ? url('/apple-touch-icon.png') : '';

    return schema_prune([
        '@context'     => 'https://schema.org',
        '@type'        => 'Organization',
        '@id'          => url('/') . '#organization',
        'name'         => $site['brand'],
        'legalName'    => $site['legal_name'] ?? '',
        'url'          => url('/'),
        'logo'         => $logo,
        'description'  => $site['tagline'] ?? '',
        'email'        => $site['email'] ?? '',
        'contactPoint' => schema_prune([
            '@type'             => 'ContactPoint',
            'contactType'       => 'customer service',
            'telephone'         => $site['phone'] ?? '',
            'email'             => $site['email'] ?? '',
            'areaServed'        => 'PY',
            'availableLanguage' => 'es',
        ]),
        'areaServed'   => array_map(
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
        'publisher'  => ['@id' => url('/') . '#organization'],
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

/**
 * Article para guías y calculadoras (S16): autor y editor = la organización (no hay todavía un
 * revisor técnico con nombre — decisión D4). Fechas de 'published'/'updated', que salen de
 * git (tools/sync-dates.php). Sin fechas no se emite: un Article sin fecha no suma nada.
 */
function schema_article(array $entry, string $path, string $headline, string $image = ''): array
{
    if (($entry['published'] ?? '') === '') {
        return [];
    }
    $org = [
        '@type' => 'Organization',
        '@id'   => url('/') . '#organization',
        'name'  => site('brand'),
        'url'   => url('/'),
    ];

    return schema_prune([
        '@context'         => 'https://schema.org',
        '@type'            => 'Article',
        'headline'         => mb_substr($headline, 0, 110),
        'description'      => $entry['meta'] ?? '',
        'url'              => url($path),
        'mainEntityOfPage' => url($path),
        'inLanguage'       => site('locale', 'es-PY'),
        'datePublished'    => $entry['published'] ?? '',
        'dateModified'     => $entry['updated'] ?? ($entry['published'] ?? ''),
        'image'            => $image,
        'author'           => $org,
        'publisher'        => $org,
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
             json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG),
             "</script>\n";
    }
}
