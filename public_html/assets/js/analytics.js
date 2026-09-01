/*
 * analytics.js — inyección de GA4 y Meta Pixel DESPUÉS del consentimiento, y disparo del
 * evento de conversión en /gracias/ (plan §3 y §6).
 *
 * Reglas que implementa:
 *   - GA4 sólo con consentimiento de ESTADÍSTICAS; Meta Pixel sólo con el de MARKETING.
 *     Rechazar o no elegir = no se descarga ni un byte de ninguno de los dos.
 *   - `cotizacion_form_submitted` (GA4) y `Lead` (Pixel) se disparan UNA vez por token `k`
 *     del redirect del handler. Refrescar /gracias/, volver con el botón atrás o compartir
 *     la URL no vuelve a contar la conversión.
 *   - Si el visitante acepta cookies estando ya en /gracias/, el evento se dispara ahí mismo:
 *     por eso apply() vuelve a intentar el disparo cada vez que cambia el consentimiento.
 */
(function () {
  'use strict';

  var cfg = window.matAnalytics || {};
  var lead = window.matLead || null;
  var loaded = { ga4: false, pixel: false };

  /* El token viaja en la URL pero la marca de "ya disparado" vive en sessionStorage: nada
     que dependa de la URL puede impedir un doble conteo tras un refresh. */
  function seen(vendor) {
    if (!lead || !lead.token) return true;
    try {
      return sessionStorage.getItem('mat_lead_' + vendor + '_' + lead.token) === '1';
    } catch (err) {
      return false; // modo privado: preferimos contar de más que no contar
    }
  }

  function mark(vendor) {
    try {
      sessionStorage.setItem('mat_lead_' + vendor + '_' + lead.token, '1');
    } catch (err) { /* sin storage: el evento igual se dispara una vez por carga */ }
  }

  function loadGa4() {
    if (loaded.ga4 || !cfg.ga4_id) return;
    loaded.ga4 = true;
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () { window.dataLayer.push(arguments); };
    window.gtag('js', new Date());
    window.gtag('config', cfg.ga4_id, { anonymize_ip: true });
    var script = document.createElement('script');
    script.async = true;
    script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(cfg.ga4_id);
    document.head.appendChild(script);
  }

  function loadPixel() {
    if (loaded.pixel || !cfg.pixel_id) return;
    loaded.pixel = true;
    /* Snippet oficial de Meta, con la carga del script recién acá dentro. */
    !function (f, b, e, v, n, t, s) {
      if (f.fbq) return; n = f.fbq = function () {
        n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
      };
      if (!f._fbq) f._fbq = n;
      n.push = n; n.loaded = true; n.version = '2.0'; n.queue = [];
      t = b.createElement(e); t.async = true; t.src = v;
      s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s);
    }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
    window.fbq('init', cfg.pixel_id);
    window.fbq('track', 'PageView');
  }

  function fireLead() {
    if (!lead || !lead.token) return;
    var params = { material: lead.material || '', categoria: lead.categoria || '' };

    if (loaded.ga4 && !seen('ga4')) {
      mark('ga4');
      window.gtag('event', 'cotizacion_form_submitted', params);
    }
    if (loaded.pixel && !seen('pixel')) {
      mark('pixel');
      window.fbq('track', 'Lead', { content_name: params.material, content_category: params.categoria });
    }
  }

  function apply(consent) {
    if (!consent) return;
    if (consent.analytics) loadGa4();
    if (consent.marketing) loadPixel();
    fireLead();
  }

  document.addEventListener('consent:changed', function (event) { apply(event.detail); });

  /* consent.js va declarado antes que este script, así que su publicación inicial ya ocurrió:
     se lee el estado actual en vez de esperar un evento que no va a volver a llegar. */
  apply(window.matConsent);
})();
