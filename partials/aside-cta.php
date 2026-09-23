<?php
/**
 * partials/aside-cta.php — tarjeta pegajosa de pedido en la columna lateral (sólo escritorio,
 * rediseño docs/design/REDESIGN.md §4). En mobile no se muestra: ahí el mismo trabajo lo
 * hace la barra pegajosa de partials/cta.php.
 *
 * Variables que define la página:
 *   $asideTitle  encabezado corto ("Cotizá cemento")
 *   $asideHref   destino del botón (#cotizar o /cotizar/?m=…)
 *   $asideLoc    data-ev-loc del botón
 */

declare(strict_types=1);

$asideTitle = isset($asideTitle) ? (string) $asideTitle : 'Pedí tu cotización';
$asideHref  = isset($asideHref) ? (string) $asideHref : '/cotizar/';
$asideLoc   = isset($asideLoc) ? (string) $asideLoc : 'aside';
$asideWa    = wa_url((string) (page()['wa_subject'] ?? ''));
$asideMax   = (int) site('max_proveedores', 3);
?>
<aside class="page-cols__aside" aria-label="Pedir cotización">
  <div class="aside-cta">
    <p class="aside-cta__title"><?= e($asideTitle) ?></p>
    <p class="aside-cta__text">Hasta <?= $asideMax ?> proveedores verificados te pasan su precio por WhatsApp.</p>
    <a class="btn btn--primary btn--block" href="<?= e($asideHref) ?>" data-ev="cta_click" data-ev-loc="<?= e($asideLoc) ?>">Pedir cotización</a>
    <?php if ($asideWa !== ''): ?>
    <a class="aside-cta__wa" href="<?= e($asideWa) ?>" data-ev="whatsapp_click" data-ev-loc="<?= e($asideLoc) ?>">o escribinos por WhatsApp</a>
    <?php endif; ?>
    <ul class="trust-list trust-list--stack">
      <li>Gratis y sin compromiso</li>
      <li>No publicamos tu teléfono</li>
    </ul>
  </div>
</aside>
