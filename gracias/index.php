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
