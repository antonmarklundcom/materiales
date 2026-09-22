<?php
/**
 * partials/cta.php — barra pegajosa de CTA en mobile (plan §11.1, decisión §1.18).
 *
 * El CTA primario es SIEMPRE el formulario: la reventa del pedido necesita campos
 * estructurados y consentimiento explícito, y un chat no los da (CONTENT-SPEC §9).
 * WhatsApp va segundo y sólo se renderiza cuando el número real está cargado en
 * data/site.php — nunca un botón que lleva a la nada.
 *
 * Se muestra sólo por debajo de 40rem (la barra existe para ahorrar scroll en el celular) y
 * se esconde mientras el formulario está a la vista (IntersectionObserver en motion.js).
 * Sin JS la barra queda siempre visible: es el comportamiento correcto por defecto.
 *
 * Lo incluye footer.php en todas las páginas salvo /cotizar/ y /gracias/, donde el
 * formulario o la confirmación YA son el contenido principal.
 */

declare(strict_types=1);

$ctaPage     = page();
$ctaClass    = (string) ($ctaPage['body_class'] ?? '');
// Páginas que llevan el formulario en la misma página: el botón ancla al bloque en vez de
// mandar a /cotizar/ y hacer perder el contexto del material que se estaba mirando. La
// calculadora también (C2): antes mandaba a un /cotizar/ en blanco y se perdía la cantidad
// que calc.js ya había precargado en el formulario de la página.
$ctaHasForm  = in_array($ctaClass, ['page-home', 'page-categoria', 'page-material', 'page-calculadora'], true);
$ctaHref     = $ctaHasForm ? '#cotizar' : '/cotizar/';
$ctaLabel    = 'Pedir cotización';
// C7: en /proveedores/ el visitante es un proveedor; el CTA de comprador lo mandaba al
// formulario equivocado.
if ($ctaClass === 'page-proveedores') {
    $ctaHref  = '#sumate';
    $ctaLabel = 'Sumate como proveedor';
}
$ctaWaUrl    = $ctaClass === 'page-proveedores' ? '' : wa_url((string) ($ctaPage['wa_subject'] ?? ''));
$ctaLoc      = 'sticky-' . str_replace('page-', '', $ctaClass !== '' ? $ctaClass : 'cta');
?>
<div class="cta-bar" data-cta-bar>
  <a class="btn btn--primary cta-bar__primary" href="<?= e($ctaHref) ?>" data-ev="cta_click" data-ev-loc="<?= e($ctaLoc) ?>"><?= e($ctaLabel) ?></a>
  <?php if ($ctaWaUrl !== ''): ?>
  <a class="btn btn--wa cta-bar__wa" href="<?= e($ctaWaUrl) ?>" data-ev="whatsapp_click" data-ev-loc="<?= e($ctaLoc) ?>">
    <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2Zm5.3 14.1c-.2.6-1.2 1.2-1.7 1.2-.4 0-1 .1-3-.8-2.5-1-4.1-3.6-4.2-3.8-.1-.2-1-1.3-1-2.5s.6-1.8.8-2c.2-.2.5-.3.6-.3h.5c.2 0 .4 0 .6.4l.8 2c.1.2.1.3 0 .5l-.3.5-.3.3c-.1.1-.2.3-.1.5.1.2.6 1.1 1.4 1.8 1 .9 1.8 1.2 2 1.3.2.1.4.1.5-.1l.7-.8c.2-.2.3-.2.5-.1l2 .9c.2.1.4.2.4.3.1.2.1.8-.2 1.4Z"/></svg>
    WhatsApp
  </a>
  <?php endif; ?>
</div>
