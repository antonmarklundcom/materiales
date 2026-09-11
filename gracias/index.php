<?php
/**
 * gracias/index.php — página de confirmación tras el envío (destino del redirect 303 del
 * handler, plan §3).
 *
 * Los eventos GA4 `cotizacion_form_submitted` y Meta Pixel `Lead` los dispara
 * assets/js/analytics.js, cada uno detrás de su consentimiento (estadísticas / marketing) y
 * UNA sola vez por token `k` del redirect: refrescar esta página no infla las conversiones.
 * noindex siempre: es una página de conversión, no de búsqueda.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$slug  = (string) ($_GET['m'] ?? '');
$entry = data('categories')[$slug] ?? data('materials')[$slug] ?? null;

// Sólo se declara el evento si el token viene del handler y tiene la forma que emite
// (16 hex). Sin token no hay conversión que contar: alguien llegó a /gracias/ por su cuenta.
$token = (string) ($_GET['k'] ?? '');
if (preg_match('/^[0-9a-f]{16}$/', $token)) {
    $leadEvent = [
        'token'     => $token,
        'material'  => $entry !== null ? $slug : '',
        'categoria' => (string) ($entry['category'] ?? ($entry !== null ? $slug : '')),
    ];
}

page([
    'title'      => 'Recibimos tu pedido de cotización',
    'meta'       => 'Recibimos tu pedido. Hasta 3 proveedores verificados te van a escribir por WhatsApp, normalmente dentro del día.',
    'canonical'  => '/gracias/',
    'noindex'    => true,
    'body_class' => 'page-gracias',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>Listo, recibimos tu pedido</h1>
    <p class="lead">
      Hasta <?= (int) site('max_proveedores', 3) ?> proveedores verificados te van a escribir por
      WhatsApp, normalmente dentro del día.
    </p>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel">

    <?php // Fase 9: qué esperar, en el orden en que va a pasar. Nada de plazos que no
          // controlamos: "normalmente dentro del día" es lo único que se promete. ?>
    <h2>Qué pasa ahora</h2>
    <ol class="steps">
      <li class="steps__item card card--hair">
        <span class="steps__n" aria-hidden="true">1</span>
        <p>Le pasamos tu pedido a proveedores que trabajan ese rubro y entregan en tu zona.</p>
      </li>
      <li class="steps__item card card--hair">
        <span class="steps__n" aria-hidden="true">2</span>
        <p>Te escriben por WhatsApp al número que cargaste, hasta <?= (int) site('max_proveedores', 3) ?> en total.</p>
      </li>
      <li class="steps__item card card--hair">
        <span class="steps__n" aria-hidden="true">3</span>
        <p>Comparás precio y entrega, y cerrás directo con el que más te sirva. Nosotros no cobramos nada.</p>
      </li>
    </ol>

    <?php $whatsapp = preg_replace('/\D+/', '', (string) site('whatsapp')); ?>
    <?php if ($whatsapp !== ''): ?>
    <p class="gracias__wa">
      ¿Te olvidaste de aclarar algo del pedido?
      <a class="btn btn--wa" href="https://wa.me/<?= e($whatsapp) ?>" data-ev="whatsapp_click" data-ev-loc="gracias">Escribinos por WhatsApp</a>
    </p>
    <?php endif; ?>

    <h2>Mientras tanto</h2>
    <?php
    $guides = array_filter(data('guides'), 'is_published');
    uasort($guides, static fn(array $a, array $b): int => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));
    $guidesTeaser = array_slice($guides, 0, 3, true);
    ?>
    <?php if ($guidesTeaser !== []): ?>
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
    <?php endif; ?>

    <ul class="tile-grid">
      <?php if ($entry !== null): ?>
      <li>
        <a class="tile card--hair" href="/materiales/<?= e($slug) ?>/">
          <span>Volver a <?= e($entry['name']) ?></span>
          <span class="tile__arrow" aria-hidden="true">→</span>
        </a>
      </li>
      <?php endif; ?>
      <li>
        <a class="tile card--hair" href="/materiales/">
          <span>Ver otros materiales</span>
          <span class="tile__arrow" aria-hidden="true">→</span>
        </a>
      </li>
    </ul>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
