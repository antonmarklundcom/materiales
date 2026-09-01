<?php
/**
 * contacto/index.php — NAP + canales. Los datos reales llegan en la fase 3 (plan §7); los
 * bloques vacíos no se renderizan (nada de placeholders visibles ni datos inventados).
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$site        = site();
$whatsapp    = preg_replace('/\D+/', '', (string) ($site['whatsapp'] ?? ''));
$breadcrumbs = [['Inicio', '/'], ['Contacto', null]];

page([
    'title'       => 'Contacto | Materiales.com.py',
    'meta'        => 'Escribinos por WhatsApp si tenés dudas sobre materiales, cantidades o entregas. Para precios, pedí tu cotización y te contactan proveedores verificados.',
    'canonical'   => '/contacto/',
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs), schema_local_business()],
    'body_class'  => 'page-contacto',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<h1>Contacto</h1>
<p>
  ¿Dudas sobre cantidades, unidades de venta o entregas? Escribinos. Si lo que querés es
  precio, <a href="/cotizar/">pedí tu cotización</a>: así te responden directamente los
  proveedores.
</p>
<?php if ($whatsapp !== '' || ($site['phone'] ?? '') !== '' || ($site['email'] ?? '') !== ''): ?>
<ul class="card-list">
  <?php if ($whatsapp !== ''): ?><li><a href="https://wa.me/<?= e($whatsapp) ?>?text=<?= rawurlencode('Hola, tengo una consulta sobre materiales de construcción.') ?>">Escribinos por WhatsApp</a></li><?php endif; ?>
  <?php if (($site['phone'] ?? '') !== ''): ?><li><a href="tel:<?= e($site['phone']) ?>">Llamanos: <?= e($site['phone']) ?></a></li><?php endif; ?>
  <?php if (($site['email'] ?? '') !== ''): ?><li><a href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a></li><?php endif; ?>
</ul>
<?php endif; ?>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
