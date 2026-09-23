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

// Rediseño: teaser de calculadoras (la calculadora precarga la cantidad del formulario, C2).
$calcs = array_filter(data('calculators'), 'is_published');
uasort($calcs, static fn(array $a, array $b): int => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));
$calcsTeaser = array_slice($calcs, 0, 3, true);

// Foto del héroe de la home (fase 11, decisión §1.20): data/site.php → hero_image. Si la
// clave está vacía o el archivo no está subido todavía, la home queda exactamente como hoy.
$homeImage = image_for(['image' => (string) site('hero_image')], 'home');
$heroImage = $homeImage;
$heroAlt   = 'Materiales de construcción en Paraguay';

page([
    'title'      => 'Materiales de construcción en Paraguay | Cotizá gratis',
    'meta'       => 'Pedí cotización de hierro, cemento, arena, ladrillos o chapas y hasta ' . $maxProv . ' proveedores verificados de Gran Asunción te escriben por WhatsApp.',
    'canonical'  => '/',
    'h1'         => 'Materiales de construcción en Paraguay',
    'schema'     => [schema_website(), schema_organization()],
    'body_class' => 'page-home',
    'image'      => (string) $homeImage,
]);

require __DIR__ . '/partials/header.php';
?>
<div class="page-hero page-hero--home band--dark grain bleed">
  <?php
  // Rediseño: la foto es el fondo del héroe a todo el ancho (sizes 100vw) y el pedido empieza
  // acá mismo con la variante corta del formulario (material + cantidad + WhatsApp). Antes el
  // único CTA del héroe mandaba a un formulario a ~5.000 px en mobile.
  $heroSizes = '100vw';
  require __DIR__ . '/partials/hero-image.php';
  ?>
  <div class="wrap page-hero__grid page-hero__grid--split">
    <div class="page-hero__copy">
      <h1>Cotizá materiales de construcción en Paraguay</h1>
      <p class="lead">
        Decinos qué material necesitás, cuánto y para qué zona. Hasta
        <?= $maxProv ?> proveedores verificados te pasan precio por WhatsApp,
        normalmente dentro del día.
      </p>
      <ul class="page-hero__facts">
        <li class="page-hero__fact"><strong><?= count($activeMaterials) ?></strong> <span>materiales para cotizar</span></li>
        <li class="page-hero__fact"><strong><?= count($activeCategories) ?></strong> <span>rubros de obra</span></li>
        <li class="page-hero__fact"><strong>hasta&nbsp;<?= $maxProv ?></strong> <span>cotizaciones por pedido</span></li>
      </ul>
    </div>
    <div class="page-hero__aside">
    <?php
    $formSlug       = '';
    $formOrigen     = '/';
    $formTitle      = 'Pedí tu cotización';
    $formVariant    = 'hero';
    $formLeadFields = ['material', 'cantidad', 'telefono'];
    require __DIR__ . '/partials/form.php';
    $formVariant    = 'full';
    unset($formLeadFields);
    ?>
    </div>
  </div>
</div>

<div class="field wrap">
  <div class="field__panel">

    <?php $howVariant = 'full'; require __DIR__ . '/partials/how-it-works.php'; ?>

    <h2>Rubros</h2>
    <?php require __DIR__ . '/partials/category-tiles.php'; ?>
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

    <?php if ($calcsTeaser !== []): ?>
    <h2>¿No sabés cuánto pedir?</h2>
    <p>Hacé la cuenta con una calculadora y la cantidad pasa sola al pedido.</p>
    <ul class="tile-grid tile-grid--3">
      <?php foreach ($calcsTeaser as $calcSlug => $calc): ?>
      <li>
        <a class="tile card--hair" href="/calculadoras/<?= e($calcSlug) ?>/">
          <span><?= e($calc['name']) ?></span>
          <span class="tile__arrow" aria-hidden="true">→</span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
    <p class="card card--accent closing-cta">
      <a href="/calculadoras/">Ver todas las calculadoras</a>
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
