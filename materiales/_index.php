<?php
/**
 * materiales/_index.php — índice /materiales/ (ItemList de categorías). Lo incluye el router
 * cuando no hay slug; no se sirve directamente (ver .htaccess).
 *
 * S4: title, H1 e intro propios. Antes compartía title y H1 con la home ("Materiales de
 * construcción en Paraguay") y las dos páginas competían por el mismo término con 59
 * palabras acá. Esta página es el CATÁLOGO: rubros + la lista completa de materiales por
 * rubro, que además le da un enlace más a cada página de material.
 */

declare(strict_types=1);

$categories = categories_ordered();
$materials  = array_filter(data('materials'), 'is_published');

$breadcrumbs = [['Inicio', '/'], ['Materiales', null]];
$items = [];
foreach ($categories as $categorySlug => $category) {
    if (is_published($category)) {
        $items[] = [$category['name'], '/materiales/' . $categorySlug . '/'];
    }
}
$maxProv = (int) site('max_proveedores', 3);

page([
    'title'       => 'Catálogo de materiales de construcción en Paraguay (' . count($materials) . ')',
    'meta'        => count($materials) . ' materiales de obra en ' . count($items) . ' rubros: hierro, cemento, áridos, ladrillos, chapas, pisos y más. Elegí y cotizá gratis con hasta ' . $maxProv . ' proveedores.',
    'canonical'   => '/materiales/',
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [
        schema_breadcrumbs($breadcrumbs),
        schema_item_list('Catálogo de materiales de construcción', '/materiales/', $items),
    ],
    'body_class'  => 'page-materiales',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero page-hero--compact band--dark grain bleed">
  <div class="wrap">
    <h1>Catálogo de materiales de construcción</h1>
    <p class="lead"><?= count($materials) ?> materiales en <?= count($items) ?> rubros. Elegí el que necesitás y pedí tu cotización en un paso.</p>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel">
    <h2>Rubros</h2>
    <?php require PUBLIC_ROOT . '/partials/category-tiles.php'; ?>

    <div class="prose">
      <?php require CONTENT_DIR . '/hubs/materiales.php'; ?>
    </div>

    <h2>Todos los materiales por rubro</h2>
    <div class="catalog">
      <?php foreach ($categories as $categorySlug => $category):
          if (!is_published($category)) {
              continue;
          }
          $children = array_filter(materials_in($categorySlug), 'is_published');
          if ($children === []) {
              continue;
          }
      ?>
      <section class="catalog__group">
        <h3><a href="/materiales/<?= e($categorySlug) ?>/"><?= e($category['name']) ?></a></h3>
        <ul>
          <?php foreach ($children as $materialSlug => $material): ?>
          <li><a href="/materiales/<?= e($materialSlug) ?>/"><?= e($material['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </section>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
