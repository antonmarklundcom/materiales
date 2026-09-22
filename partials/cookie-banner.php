<?php
/**
 * partials/cookie-banner.php — banner de consentimiento (Ley 6534 + 7593, plan §6).
 *
 * Necesarias: siempre activas. Estadísticas y Marketing: por defecto APAGADAS.
 * GA4 y Meta Pixel se cargan SÓLO después del consentimiento correspondiente (analytics.js).
 *
 * C4: tira compacta. Antes era una tarjeta de 351 px que tapaba el 42 % de la pantalla de un
 * celular y el CTA del héroe en cada primera visita. Ahora: una línea de texto y Aceptar /
 * Rechazar con el mismo peso visual (rechazar no puede costar más que aceptar), y
 * "Configurar" despliega las casillas por categoría.
 */

declare(strict_types=1);
?>
<div id="cookie-banner" class="cookie-banner" role="dialog" aria-modal="false"
     aria-labelledby="cookie-banner-title" hidden>
  <p class="cookie-banner__text">
    <strong id="cookie-banner-title">Cookies.</strong>
    Usamos las necesarias para que el sitio funcione; las de estadísticas y marketing, sólo si
    las aceptás. <a href="/politica-de-privacidad/">Política de privacidad</a>.
  </p>
  <form class="cookie-banner__form" id="cookie-banner-form">
    <div class="cookie-banner__actions">
      <button type="button" data-consent="accept-all">Aceptar</button>
      <button type="button" data-consent="reject">Rechazar</button>
      <button type="button" class="cookie-banner__configure" data-consent-configure
              aria-expanded="false" aria-controls="cookie-banner-options">Configurar</button>
    </div>
    <div class="cookie-banner__options" id="cookie-banner-options" hidden>
      <label><input type="checkbox" checked disabled> Necesarias</label>
      <label><input type="checkbox" name="analytics" value="1"> Estadísticas</label>
      <label><input type="checkbox" name="marketing" value="1"> Marketing</label>
      <button type="submit" data-consent="save">Guardar elección</button>
    </div>
  </form>
</div>
