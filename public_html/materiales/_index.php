<?php
/**
 * materiales/_index.php — índice /materiales/ (ItemList de categorías). Lo incluye el router
 * cuando no hay slug; no se sirve directamente (ver .htaccess).
 */

declare(strict_types=1);

$categories = categories_ordered();

$breadcrumbs = [['Inicio', '/'], ['Materiales', null]];
$items = [];
foreach ($categories as $categorySlug => $category) {
    $items[] = [$category['name'], '/materiales/' . $categorySlug . '/'];
}

page([
    'title'       => 'Materiales de construcción en Paraguay | Cotizá gratis',
    'meta'        => 'Hierro, cemento, áridos, ladrillos, chapas y más. Elegí el material y pedí cotización: hasta ' . (int) site('max_proveedores', 3) . ' proveedores verificados te escriben.',
    'canonical'   => '/materiales/',
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [
        schema_breadcrumbs($breadcrumbs),
        schema_item_list('Materiales de construcción en Paraguay', '/materiales/', $items),
    ],
    'body_class'  => 'page-materiales',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<h1>Materiales de construcción en Paraguay</h1>
<p>Elegí el rubro que necesitás y pedí tu cotización en un paso.</p>
<ul class="card-list">
  <?php foreach ($categories as $categorySlug => $category): ?>
  <li><a href="/materiales/<?= e($categorySlug) ?>/"<?= is_published($category) ? '' : ' class="is-proxima"' ?>><?= e($category['name']) ?></a></li>
  <?php endforeach; ?>
</ul>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
