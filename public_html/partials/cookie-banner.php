<?php
/**
 * partials/cookie-banner.php — shell del banner de consentimiento (Ley 6534 + 7593, plan §6).
 *
 * Necesarias: siempre activas. Estadísticas y Marketing: por defecto APAGADAS.
 * GA4 y Meta Pixel se cargan SÓLO después del consentimiento correspondiente — el cableado
 * de los scripts vive en la fase 2; acá está el shell y el almacenamiento de la elección.
 */

declare(strict_types=1);
?>
<div id="cookie-banner" class="cookie-banner" role="dialog" aria-modal="false"
     aria-labelledby="cookie-banner-title" hidden>
  <h2 id="cookie-banner-title" class="cookie-banner__title">Usamos cookies</h2>
  <p class="cookie-banner__text">
    Usamos cookies necesarias para que el sitio funcione. Las de estadísticas y marketing
    sólo se activan si vos las aceptás. Leé más en la
    <a href="/politica-de-privacidad/">Política de privacidad</a>.
  </p>
  <form class="cookie-banner__options" id="cookie-banner-form">
    <label><input type="checkbox" checked disabled> Necesarias</label>
    <label><input type="checkbox" name="analytics" value="1"> Estadísticas</label>
    <label><input type="checkbox" name="marketing" value="1"> Marketing</label>
    <div class="cookie-banner__actions">
      <button type="button" data-consent="reject">Rechazar</button>
      <button type="submit" data-consent="save">Guardar elección</button>
      <button type="button" data-consent="accept-all">Aceptar todo</button>
    </div>
  </form>
</div>
