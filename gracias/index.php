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

// Sólo se declara el evento si el token viene del handler con su firma válida
// (lead_conversion_token). Sin token, o con uno tipeado a mano, no hay conversión que contar.
require PUBLIC_ROOT . '/partials/lead.php';
$token = (string) ($_GET['k'] ?? '');
$reference = '';
if (lead_conversion_token_valid($token)) {
    $reference = lead_reference($token);
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
    <span class="done-badge" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24"><path d="M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z" fill="currentColor"/></svg></span>
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

    <?php
    // C8: la referencia del pedido va en el mensaje de WhatsApp, así el que atiende lo
    // encuentra en el registro sin preguntar de nuevo nombre, material y cantidad.
    $whatsapp = preg_replace('/\D+/', '', (string) site('whatsapp'));
    $waText   = 'Hola, hice un pedido en ' . site('brand')
        . ($reference !== '' ? ' (ref. ' . $reference . ')' : '')
        . ' y quiero agregar o corregir algo: …';
    ?>
    <?php if ($reference !== ''): ?>
    <p class="gracias__ref">Referencia de tu pedido: <strong><?= e($reference) ?></strong></p>
    <?php endif; ?>
    <?php if ($whatsapp !== ''): ?>
    <p class="gracias__wa">
      ¿Te olvidaste de aclarar algo del pedido?
      <a class="btn btn--wa" href="https://wa.me/<?= e($whatsapp) ?>?text=<?= e(rawurlencode($waText)) ?>" data-ev="whatsapp_click" data-ev-loc="gracias">Escribinos por WhatsApp</a>
    </p>
    <p class="gracias__save">
      <strong>Guardá nuestro número</strong> para reconocer el mensaje:
      <a href="tel:<?= e((string) site('whatsapp')) ?>" data-ev="call_click" data-ev-loc="gracias"><?= e((string) site('whatsapp')) ?></a>.
      Los proveedores te escriben desde sus propios números.
    </p>
    <?php endif; ?>

    <?php
    // C8: lo que suele faltar en el mismo pedido. Los related[] del material (o los materiales
    // del rubro, si se pidió un rubro entero), cada uno con el formulario ya preseleccionado.
    $addOns = [];
    if ($entry !== null) {
        $candidates = isset(data('materials')[$slug])
            ? ($entry['related'] ?? [])
            : array_keys(materials_in($slug));
        foreach ($candidates as $candidate) {
            $candidateEntry = data('materials')[$candidate] ?? data('categories')[$candidate] ?? null;
            if ($candidate !== $slug && $candidateEntry !== null && is_published($candidateEntry)) {
                $addOns[$candidate] = $candidateEntry;
            }
        }
        $addOns = array_slice($addOns, 0, 4, true);
    }
    ?>
    <?php if ($addOns !== []): ?>
    <h2>¿Te falta algo? Sumalo al pedido</h2>
    <p>Mandá otro pedido corto y aclará en el mensaje que es para la misma obra: así te lo pueden cotizar junto con lo anterior.</p>
    <ul class="tile-grid">
      <?php foreach ($addOns as $addSlug => $addEntry): ?>
      <li>
        <a class="tile card--hair" href="/cotizar/?m=<?= e($addSlug) ?>" data-ev="cta_click" data-ev-loc="gracias-sumar-<?= e($addSlug) ?>">
          <span>Sumá <?= e(mb_strtolower($addEntry['name'])) ?></span>
          <span class="tile__arrow" aria-hidden="true">→</span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
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
