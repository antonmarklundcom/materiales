<?php
/**
 * proveedores/index.php — landing de captación de proveedores (plan §11.2, decisión §1.17).
 *
 * Es una página de RECLUTAMIENTO, no el pivot a marketplace: `/proveedores/` queda como raíz
 * del namespace reservado y las futuras fichas `/proveedores/{empresa}/` se cuelgan debajo
 * sin mover nada.
 *
 * Sin números de negocio inventados: las condiciones salen de data/site.php → supplier_pitch
 * y, si está vacío, la página dice que se cuentan por WhatsApp (plan §11.2).
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';
require PUBLIC_ROOT . '/partials/lead.php';

$maxProv = (int) site('max_proveedores', 3);

// Rubros con captación activa AHORA (plan §5: las 5 categorías de lanzamiento). Es una
// constante del plan, no un dato editorial: vive acá y no en data/categories.php.
$recruitingNow = ['hierro', 'cemento-y-cal', 'aridos', 'ladrillos-y-bloques', 'chapas-y-techos'];

$activeCategories = array_filter(categories_ordered(), 'is_published');
$supplierPitch    = trim((string) site('supplier_pitch'));
$categoriesNote   = trim((string) site('supplier_categories_note'));
$ok               = ($_GET['ok'] ?? '') === '1';

$faq = [
    [
        'q' => '¿Cuánto cuesta recibir pedidos?',
        'a' => $supplierPitch !== ''
            ? $supplierPitch
            : 'Te contamos las condiciones por WhatsApp cuando verificamos la empresa. Pagás por pedido recibido, no por publicidad ni por aparecer en el sitio.',
    ],
    [
        'q' => '¿Cuántos proveedores reciben el mismo pedido?',
        'a' => 'Hasta ' . $maxProv . ' por pedido. Al comprador se lo decimos de entrada, así que sabe que va a comparar y no se sorprende cuando lo contactan.',
    ],
    [
        'q' => '¿Qué datos trae cada pedido?',
        'a' => 'Material, cantidad, ciudad o zona de entrega, nombre y WhatsApp de quien pide, más lo que haya aclarado en el mensaje. Con eso podés cotizar sin volver a preguntar lo básico.',
    ],
    [
        'q' => '¿Tengo que publicar mis precios en el sitio?',
        'a' => 'No. El sitio no publica precios de nadie: vos le pasás tu precio directo al comprador por WhatsApp y cerrás la venta como siempre.',
    ],
];

$breadcrumbs = [['Inicio', '/'], ['Para proveedores', null]];

page([
    'title'       => 'Recibí pedidos de cotización de tu rubro | Proveedores',
    'meta'        => 'Corralones, depósitos y fábricas: recibí pedidos de cotización con material, cantidad y zona de compradores de Gran Asunción. Sumate en un minuto.',
    'canonical'   => '/proveedores/',
    'h1'          => 'Recibí pedidos de cotización de tu rubro',
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs), schema_faq($faq)],
    'body_class'  => 'page-proveedores',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>Recibí pedidos de cotización de tu rubro</h1>
    <p class="lead">
      Compradores de Gran Asunción piden precio de materiales todos los días. Nosotros les
      pedimos el material, la cantidad y la zona, y te pasamos el pedido para que cotices.
    </p>
    <p><a class="btn btn--primary" href="#sumate" data-ev="form_submit" data-ev-loc="proveedores-hero">Sumate como proveedor</a></p>
  </div>
</div>

<div class="field wrap">
  <div class="field__panel">

    <?php if ($ok): ?>
    <p class="notice" id="gracias" role="status">
      Listo, recibimos tus datos. Te escribimos por WhatsApp para verificar la empresa y
      arrancar con los pedidos de tu rubro.
    </p>
    <?php endif; ?>

    <div class="prose">
      <h2>Qué recibís</h2>
      <ul>
        <li><strong>Pedidos reales, con datos.</strong> Material, cantidad, ciudad o zona de entrega y el WhatsApp de quien pide. No es una lista de contactos fríos: es alguien que acaba de pedir precio.</li>
        <li><strong>Hasta <?= $maxProv ?> proveedores por pedido.</strong> Competís con dos, no con veinte, y el comprador ya sabe que va a recibir varias respuestas.</li>
        <li><strong>Pagás por pedido, no por publicidad.</strong> No hay banner, no hay posición destacada y no se vende visibilidad: lo único que se paga es el pedido que te llega.</li>
        <li><strong>No publicamos tus precios.</strong> Cotizás directo por WhatsApp y cerrás la venta vos.</li>
      </ul>
    </div>

    <h2>Cómo funciona para proveedores</h2>
    <ol class="steps">
      <li class="steps__item card card--hair">
        <span class="steps__n" aria-hidden="true">1</span>
        <h3>Cargás tu rubro y tu zona</h3>
        <p>El formulario de abajo. Te llamamos para verificar que la empresa existe y que vendés lo que decís.</p>
      </li>
      <li class="steps__item card card--hair">
        <span class="steps__n" aria-hidden="true">2</span>
        <h3>Te llegan los pedidos de esos rubros</h3>
        <p>Con material, cantidad y zona. Si un pedido no te sirve, lo dejás pasar y avisás.</p>
      </li>
      <li class="steps__item card card--hair">
        <span class="steps__n" aria-hidden="true">3</span>
        <h3>Cotizás y cerrás directo</h3>
        <p>Le escribís por WhatsApp con tu precio y tus condiciones de entrega. Nosotros no participamos de la venta.</p>
      </li>
    </ol>

    <h2>Condiciones</h2>
    <?php if ($supplierPitch !== ''): ?>
    <p class="card card--hair"><?= e($supplierPitch) ?></p>
    <?php else: ?>
    <p class="card card--hair">
      Te contamos las condiciones por WhatsApp cuando verificamos la empresa. Preferimos
      explicarlas hablando y no publicar una tabla que después no se ajusta a tu rubro.
    </p>
    <?php endif; ?>

    <h2>Rubros</h2>
    <p>Hoy estamos sumando proveedores con prioridad en estos rubros, pero podés cargarte en cualquiera de los que publicamos.</p>
    <ul class="tile-grid tile-grid--3">
      <?php foreach ($activeCategories as $catSlug => $category): ?>
      <?php $isNow = in_array($catSlug, $recruitingNow, true); ?>
      <li>
        <a class="tile card--hair<?= $isNow ? ' tile--featured' : '' ?>" href="/materiales/<?= e($catSlug) ?>/">
          <span><?= e($category['name']) ?><?= $isNow ? ' — buscamos proveedores ahora' : '' ?></span>
          <span class="tile__arrow" aria-hidden="true">→</span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php if ($categoriesNote !== ''): ?>
    <p class="lead-form__note"><?= e($categoriesNote) ?></p>
    <?php endif; ?>

    <section class="faq">
      <h2>Preguntas frecuentes de proveedores</h2>
      <?php foreach ($faq as $item): ?>
      <details>
        <summary><?= e($item['q']) ?></summary>
        <p><?= e($item['a']) ?></p>
      </details>
      <?php endforeach; ?>
    </section>

    <?php require PUBLIC_ROOT . '/partials/form-proveedor.php'; ?>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
