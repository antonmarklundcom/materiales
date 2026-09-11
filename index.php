<?php
/**
 * index.php — homepage. Copy de CONTENT-SPEC §1 (title, meta, H1, bajada, CTA, H2 Rubros,
 * cierre) más la capa de conversión de la fase 9 (plan §11.1): franja de datos, "Cómo
 * funciona", prosa con los términos de cabecera y locales, teaser de guías y el formulario.
 *
 * Las tres cifras de la franja se calculan desde los datos: nunca se tipea un número que
 * pueda desincronizarse del catálogo (regla de encabezado de CONTENT-SPEC).
 */

declare(strict_types=1);

require __DIR__ . '/partials/init.php';
require __DIR__ . '/partials/schema.php';
require __DIR__ . '/partials/lead.php';

$categories = categories_ordered();

$activeCategories = array_filter($categories, 'is_published');
$activeMaterials  = array_filter(data('materials'), 'is_published');
$maxProv          = (int) site('max_proveedores', 3);

// Teaser de guías: las tres primeras publicadas por 'order'. Se lee del dato, así que una
// guía nueva entra sola cuando la fase de contenido la publica.
$guides = array_filter(data('guides'), 'is_published');
uasort($guides, static fn(array $a, array $b): int => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));
$guidesTeaser = array_slice($guides, 0, 3, true);

page([
    'title'      => 'Materiales de construcción en Paraguay | Cotizá gratis',
    'meta'       => 'Pedí cotización de hierro, cemento, arena, ladrillos o chapas y hasta ' . $maxProv . ' proveedores verificados de Gran Asunción te escriben por WhatsApp.',
    'canonical'  => '/',
    'h1'         => 'Materiales de construcción en Paraguay',
    'schema'     => [schema_website(), schema_local_business()],
    'body_class' => 'page-home',
]);

require __DIR__ . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap page-hero__grid page-hero__grid--split">
    <div>
      <h1>Cotizá materiales de construcción en Paraguay</h1>
      <p class="lead">
        Decinos qué material necesitás, cuánto y para qué zona. Hasta
        <?= $maxProv ?> proveedores verificados te pasan precio por WhatsApp,
        normalmente dentro del día.
      </p>
      <p><a class="btn btn--primary" href="#cotizar" data-ev="form_submit" data-ev-loc="home-hero">Pedí tu cotización</a></p>
    </div>
    <ul class="page-hero__facts">
      <li class="page-hero__fact"><strong><?= count($activeMaterials) ?></strong> <span>materiales para cotizar, del hierro a las aberturas</span></li>
      <li class="page-hero__fact"><strong><?= count($activeCategories) ?></strong> <span>rubros de obra, con su vocabulario de plaza</span></li>
      <li class="page-hero__fact"><strong>hasta&nbsp;<?= $maxProv ?></strong> <span>cotizaciones por pedido, gratis y sin compromiso</span></li>
    </ul>
  </div>
</div>

<div class="field wrap">
  <div class="field__panel">

    <h2>Cómo funciona</h2>
    <ol class="steps">
      <li class="steps__item card card--hair">
        <span class="steps__n" aria-hidden="true">1</span>
        <h3>Contás qué necesitás</h3>
        <p>Material, cantidad y la zona donde lo querés. Un minuto, sin registrarte.</p>
      </li>
      <li class="steps__item card card--hair">
        <span class="steps__n" aria-hidden="true">2</span>
        <h3>Hasta <?= $maxProv ?> proveedores verificados te escriben</h3>
        <p>Les llega tu pedido con los datos que necesitan para cotizar, así que te contestan con un precio, no con una pregunta.</p>
      </li>
      <li class="steps__item card card--hair">
        <span class="steps__n" aria-hidden="true">3</span>
        <h3>Elegís el mejor precio</h3>
        <p>Comparás sobre la misma cantidad y la misma entrega, y cerrás directo con el proveedor.</p>
      </li>
    </ol>

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

    <div class="prose">
      <?php require CONTENT_DIR . '/home/intro.php'; ?>
    </div>

    <?php if ($guidesTeaser !== []): ?>
    <h2>Guías para calcular antes de comprar</h2>
    <ul class="tile-grid tile-grid--3">
      <?php foreach ($guidesTeaser as $guideSlug => $guide): ?>
      <li>
        <a class="tile card--hair" href="/guias/<?= e($guideSlug) ?>/">
          <span><?= e($guide['name']) ?></span>
          <span class="tile__arrow" aria-hidden="true">→</span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
    <p class="card card--accent closing-cta">
      <a href="/guias/">Ver todas las guías</a>
    </p>
    <?php endif; ?>

    <?php
    // El formulario cierra la home sin preselección: el visitante que llega por el término
    // de cabecera todavía no eligió material (CONTENT-SPEC §7).
    $formSlug   = '';
    $formOrigen = '/';
    $formTitle  = 'Pedí tu cotización';
    require PUBLIC_ROOT . '/partials/form.php';
    ?>
  </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
