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
<div class="page-hero bleed">
  <div class="wrap">
    <h1>Contacto</h1>
    <p class="lead">
      ¿Dudas sobre cantidades, unidades de venta o entregas? Escribinos. Si lo que querés es
      precio, <a href="/cotizar/">pedí tu cotización</a>: así te responden directamente los
      proveedores.
    </p>
  </div>
</div>
<?php if ($whatsapp !== '' || ($site['phone'] ?? '') !== '' || ($site['email'] ?? '') !== ''): ?>
<div class="field wrap">
  <div class="field__panel">
    <ul class="tile-grid">
      <?php if ($whatsapp !== ''): ?>
      <li>
        <a class="btn btn--wa" href="https://wa.me/<?= e($whatsapp) ?>?text=<?= rawurlencode('Hola, tengo una consulta sobre materiales de construcción.') ?>" data-ev="whatsapp_click" data-ev-loc="contacto">
          <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.36 5.07L2 22l5.07-1.33A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm0 18a7.9 7.9 0 0 1-4.03-1.1l-.29-.17-3 .79.8-2.92-.19-.3A7.94 7.94 0 1 1 12 20Zm4.4-5.9c-.24-.12-1.43-.7-1.65-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1.01-.37-1.92-1.18-.71-.63-1.19-1.42-1.33-1.66-.14-.24-.01-.37.11-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.19-.46-.39-.4-.54-.4h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.43-.58 1.63-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28Z"/></svg>
          <span>Escribinos por WhatsApp</span>
        </a>
      </li>
      <?php endif; ?>
      <?php if (($site['phone'] ?? '') !== ''): ?>
      <li>
        <a class="tile card--hair" href="tel:<?= e($site['phone']) ?>" data-ev="call_click" data-ev-loc="contacto">
          <span>Llamanos: <?= e($site['phone']) ?></span>
        </a>
      </li>
      <?php endif; ?>
      <?php if (($site['email'] ?? '') !== ''): ?>
      <li>
        <a class="tile card--hair" href="mailto:<?= e($site['email']) ?>"><span><?= e($site['email']) ?></span></a>
      </li>
      <?php endif; ?>
    </ul>
  </div>
</div>
<?php endif; ?>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
