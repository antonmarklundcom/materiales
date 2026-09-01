<?php
/**
 * index.php — homepage. La copy definitiva la fija la fase 3; acá va el esqueleto con el
 * <title>/meta reales, el schema sitewide y los enlaces a las categorías de lanzamiento.
 */

declare(strict_types=1);

require __DIR__ . '/partials/init.php';
require __DIR__ . '/partials/schema.php';

$categories = categories_ordered();

page([
    'title'      => 'Materiales de construcción en Paraguay | Cotizá gratis',
    'meta'       => 'Pedí cotización de hierro, cemento, arena, ladrillos o chapas y hasta ' . (int) site('max_proveedores', 3) . ' proveedores verificados de Gran Asunción te escriben por WhatsApp.',
    'canonical'  => '/',
    'h1'         => 'Materiales de construcción en Paraguay',
    'schema'     => [schema_website(), schema_local_business()],
    'body_class' => 'page-home',
]);

require __DIR__ . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>Cotizá materiales de construcción en Paraguay</h1>
    <p class="lead">
      Decinos qué material necesitás, cuánto y para qué zona. Hasta
      <?= (int) site('max_proveedores', 3) ?> proveedores verificados te pasan precio por WhatsApp,
      normalmente dentro del día.
    </p>
    <p><a class="btn btn--primary" href="/cotizar/" data-ev="form_submit" data-ev-loc="home-hero">Pedí tu cotización</a></p>
  </div>
</div>

<div class="field wrap">
  <div class="field__panel">
    <h2>Rubros</h2>
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
    <p class="card card--accent closing-cta">
      <a href="/materiales/">Ver todos los materiales</a>
    </p>
  </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
