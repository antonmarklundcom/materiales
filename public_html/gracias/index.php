<?php
/**
 * gracias/index.php — página de confirmación tras el envío (destino del redirect 303 del
 * handler, plan §3). Los eventos GA4 `cotizacion_form_submitted` y Meta Pixel `Lead` se
 * cablean en la fase 2, condicionados al consentimiento de marketing/estadísticas.
 * noindex siempre: es una página de conversión, no de búsqueda.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$slug  = (string) ($_GET['m'] ?? '');
$entry = data('categories')[$slug] ?? data('materials')[$slug] ?? null;

page([
    'title'      => 'Recibimos tu pedido de cotización',
    'meta'       => 'Recibimos tu pedido. Hasta 3 proveedores verificados te van a escribir por WhatsApp, normalmente dentro del día.',
    'canonical'  => '/gracias/',
    'noindex'    => true,
    'body_class' => 'page-gracias',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<h1>Listo, recibimos tu pedido</h1>
<p>
  Hasta <?= (int) site('max_proveedores', 3) ?> proveedores verificados te van a escribir por
  WhatsApp, normalmente dentro del día.
</p>
<?php if ($entry !== null): ?>
<p><a href="/materiales/<?= e($slug) ?>/">Volver a <?= e($entry['name']) ?></a></p>
<?php endif; ?>
<p><a href="/materiales/">Ver otros materiales</a></p>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
