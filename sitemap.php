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

// lastmod sale SÓLO de la clave opcional 'updated' (YYYY-MM-DD) de cada entrada de datos
// (decisión §1.25): el deploy de Hostinger reescribe los mtimes en cada push, así que
// filemtime() no sirve de nada acá. Sin 'updated' ⇒ sin lastmod para esa URL.
$add = static function (string $path, ?string $updated = null) use (&$urls): void {
    $lastmod = null;
    if ($updated !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $updated) === 1) {
        $lastmod = $updated;
    }
    $urls[] = ['loc' => url($path), 'lastmod' => $lastmod];
};

// Páginas fijas indexables: sin entrada de datos, sin lastmod.
foreach (['/', '/materiales/', '/guias/', '/calculadoras/', '/cotizar/', '/proveedores/', '/contacto/', '/politica-de-privacidad/'] as $path) {
    $add($path);
}

foreach (categories_ordered() as $slug => $category) {
    if (is_published($category)) {
        $add('/materiales/' . $slug . '/', isset($category['updated']) ? (string) $category['updated'] : null);
    }
}

foreach (data('materials') as $slug => $material) {
    if (is_published($material)) {
        $add('/materiales/' . $slug . '/', isset($material['updated']) ? (string) $material['updated'] : null);
    }
}

foreach (data('guides') as $slug => $guide) {
    if (is_published($guide)) {
        $add('/guias/' . $slug . '/', isset($guide['updated']) ? (string) $guide['updated'] : null);
    }
}

foreach (data('calculators') as $slug => $calculator) {
    if (is_published($calculator)) {
        $add('/calculadoras/' . $slug . '/', isset($calculator['updated']) ? (string) $calculator['updated'] : null);
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
