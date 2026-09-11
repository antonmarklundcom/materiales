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
// Páginas que llevan el formulario en la misma página: el botón ancla al bloque en vez de
// mandar a /cotizar/ y hacer perder el contexto del material que se estaba mirando.
$ctaHasForm  = in_array($ctaPage['body_class'] ?? '', ['page-home', 'page-categoria', 'page-material'], true);
$ctaHref     = $ctaHasForm ? '#cotizar' : '/cotizar/';
$ctaWhatsapp = preg_replace('/\D+/', '', (string) site('whatsapp'));
$ctaLoc      = 'sticky-' . str_replace('page-', '', (string) ($ctaPage['body_class'] ?? 'cta'));
?>
<div class="cta-bar" data-cta-bar>
  <a class="btn btn--primary cta-bar__primary" href="<?= e($ctaHref) ?>" data-ev="form_submit" data-ev-loc="<?= e($ctaLoc) ?>">Pedir cotización</a>
  <?php if ($ctaWhatsapp !== ''): ?>
  <a class="btn btn--wa cta-bar__wa" href="https://wa.me/<?= e($ctaWhatsapp) ?>" data-ev="whatsapp_click" data-ev-loc="<?= e($ctaLoc) ?>">
    <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2Zm5.3 14.1c-.2.6-1.2 1.2-1.7 1.2-.4 0-1 .1-3-.8-2.5-1-4.1-3.6-4.2-3.8-.1-.2-1-1.3-1-2.5s.6-1.8.8-2c.2-.2.5-.3.6-.3h.5c.2 0 .4 0 .6.4l.8 2c.1.2.1.3 0 .5l-.3.5-.3.3c-.1.1-.2.3-.1.5.1.2.6 1.1 1.4 1.8 1 .9 1.8 1.2 2 1.3.2.1.4.1.5-.1l.7-.8c.2-.2.3-.2.5-.1l2 .9c.2.1.4.2.4.3.1.2.1.8-.2 1.4Z"/></svg>
    WhatsApp
  </a>
  <?php endif; ?>
</div>
