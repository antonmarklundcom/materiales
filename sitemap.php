<?php
/**
 * sitemap.php — se sirve como /sitemap.xml (reescritura en .htaccess).
 *
 * Se genera en cada request desde los MISMOS archivos de datos que usa el router, así que no
 * puede desincronizarse (plan §1.2). Sólo entran páginas con status 'activa' más las páginas
 * fijas indexables. /gracias/ y 404 nunca entran (son noindex).
 */

declare(strict_types=1);

require __DIR__ . '/partials/init.php';

/** @var array<int, array{loc: string, lastmod: ?string}> $urls */
$urls = [];

$add = static function (string $path, ?string $contentFile = null) use (&$urls): void {
    $lastmod = null;
    if ($contentFile !== null && is_file($contentFile)) {
        $lastmod = date('Y-m-d', (int) filemtime($contentFile));
    }
    $urls[] = ['loc' => url($path), 'lastmod' => $lastmod];
};

// Páginas fijas indexables.
foreach (['/', '/materiales/', '/guias/', '/calculadoras/', '/cotizar/', '/proveedores/', '/contacto/', '/politica-de-privacidad/'] as $path) {
    $add($path);
}

foreach (categories_ordered() as $slug => $category) {
    if (is_published($category)) {
        $add('/materiales/' . $slug . '/', CONTENT_DIR . '/categorias/' . $slug . '.php');
    }
}

foreach (data('materials') as $slug => $material) {
    if (is_published($material)) {
        $add('/materiales/' . $slug . '/', CONTENT_DIR . '/materiales/' . $slug . '.php');
    }
}

foreach (data('guides') as $slug => $guide) {
    if (is_published($guide)) {
        $add('/guias/' . $slug . '/', CONTENT_DIR . '/guias/' . $slug . '.php');
    }
}

foreach (data('calculators') as $slug => $calculator) {
    if (is_published($calculator)) {
        $add('/calculadoras/' . $slug . '/', CONTENT_DIR . '/calculadoras/' . $slug . '.php');
    }
}

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
foreach ($urls as $entry) {
    echo '  <url><loc>', htmlspecialchars($entry['loc'], ENT_XML1, 'UTF-8'), '</loc>';
    if ($entry['lastmod'] !== null) {
        echo '<lastmod>', $entry['lastmod'], '</lastmod>';
    }
    echo "</url>\n";
}
echo '</urlset>', "\n";
