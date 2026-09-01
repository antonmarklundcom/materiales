<?php
/**
 * guias/index.php — router de /guias/ y /guias/{slug}/ (contenido informativo, plan §5).
 * Cada guía enlaza a sus páginas de dinero; la prosa llega en content/guias/{slug}.php.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$guides = data('guides');
$slug   = (string) ($_GET['slug'] ?? '');

if ($slug === '') {
    $breadcrumbs = [['Inicio', '/'], ['Guías', null]];
    $items = [];
    foreach ($guides as $guideSlug => $guide) {
        $items[] = [$guide['name'], '/guias/' . $guideSlug . '/'];
    }

    page([
        'title'       => 'Guías de obra y materiales en Paraguay',
        'meta'        => 'Cálculos y comparativas para comprar bien: bolsas de cemento por m², qué piedra va en cimientos, qué chapa conviene y más. Con cotización en un paso.',
        'canonical'   => '/guias/',
        'breadcrumbs' => $breadcrumbs,
        'schema'      => [
            schema_breadcrumbs($breadcrumbs),
            schema_item_list('Guías de obra y materiales', '/guias/', $items),
        ],
        'body_class'  => 'page-guias',
    ]);

    require PUBLIC_ROOT . '/partials/header.php';
    echo "<h1>Guías de obra y materiales</h1>\n";
    echo "<ul class=\"card-list\">\n";
    foreach ($guides as $guideSlug => $guide) {
        printf(
            "  <li><a href=\"/guias/%s/\"%s>%s</a></li>\n",
            e($guideSlug),
            is_published($guide) ? '' : ' class="is-proxima"',
            e($guide['name'])
        );
    }
    echo "</ul>\n";
    require PUBLIC_ROOT . '/partials/footer.php';
    exit;
}

if (!preg_match('/^[a-z0-9-]+$/', $slug) || !isset($guides[$slug])) {
    not_found();
}

$guide       = $guides[$slug];
$breadcrumbs = [['Inicio', '/'], ['Guías', '/guias/'], [$guide['name'], null]];
$contentFile = CONTENT_DIR . '/guias/' . $slug . '.php';

page([
    'title'       => $guide['title'],
    'meta'        => $guide['meta'],
    'canonical'   => '/guias/' . $slug . '/',
    'noindex'     => !is_published($guide),
    'h1'          => $guide['name'],
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs)],
    'body_class'  => 'page-guia',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<h1><?= e($guide['name']) ?></h1>
<?php if (is_file($contentFile)): ?>
<?php require $contentFile; ?>
<?php else: ?>
<p class="notice">Estamos escribiendo esta guía. Mientras tanto, <a href="/cotizar/">pedí tu cotización</a>.</p>
<?php endif; ?>
<?php
$related = array_filter(
    $guide['related'] ?? [],
    static fn(string $target): bool => isset(data('categories')[$target]) || isset(data('materials')[$target])
);
if ($related !== []):
?>
<h2>Páginas relacionadas</h2>
<ul class="card-list">
  <?php foreach ($related as $target): ?>
  <?php $entry = data('categories')[$target] ?? data('materials')[$target]; ?>
  <li><a href="/materiales/<?= e($target) ?>/"><?= e($entry['name']) ?></a></li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
