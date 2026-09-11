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
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>Materiales de construcción en Paraguay</h1>
    <p class="lead">Elegí el rubro que necesitás y pedí tu cotización en un paso.</p>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel">
    <ul class="tile-grid tile-grid--3">
      <?php foreach ($categories as $categorySlug => $category): ?>
      <li>
        <a class="tile card--hair<?= is_published($category) ? ' tile--featured' : ' is-proxima' ?>"
           href="/materiales/<?= e($categorySlug) ?>/"<?= is_published($category) ? '' : ' aria-disabled="true"' ?>>
          <span><?= e($category['name']) ?></span>
          <span class="tile__arrow" aria-hidden="true">→</span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
