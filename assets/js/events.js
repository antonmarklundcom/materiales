/*
 * events.js — medición de UI sin proveedor propio (web-design-system, analytics-prep; C1).
 *
 * window.matTrack(nombre, params) es el único punto de salida de eventos del sitio:
 *   - siempre empuja un objeto a window.dataLayer (lo que leería un GTM el día que exista);
 *   - si GA4 está configurado (data/site.php → ga4_id), partials/analytics.php ya definió el
 *     stub de gtag con Consent Mode v2 en "denied", así que además va como gtag('event', …).
 *     Hasta que el visitante acepta "Estadísticas" gtag.js no se descarga y nada sale del
 *     navegador; si acepta en la misma página, gtag.js procesa la cola al cargar.
 *
 * Eventos: cta_click, whatsapp_click, call_click (clic en [data-ev]), form_start /
 * supplier_form_start (primer foco en un formulario de leads), form_submit_attempt /
 * supplier_submit_attempt (clic en enviar), form_invalid (validación del navegador, C5) y
 * calculator_use (calc.js). La conversión real (lead) la dispara analytics.js en /gracias/.
 */
(function () {
  window.dataLayer = window.dataLayer || [];

  window.matTrack = function (name, params) {
    var data = params || {};
    var payload = {
      event: name,
      ev_loc: data.ev_loc || '',
      page_path: location.pathname,
      site: location.hostname
    };
    window.dataLayer.push(payload);
    if (typeof window.gtag === 'function') {
      window.gtag('event', name, { ev_loc: payload.ev_loc });
    }
  };

  document.addEventListener('click', function (e) {
    if (!e.target || typeof e.target.closest !== 'function') return;
    var t = e.target.closest('[data-ev]');
    if (!t) return;
    window.matTrack(t.dataset.ev, { ev_loc: t.dataset.evLoc || '' });
  }, true);

  /* form_start: primer foco dentro de un formulario de leads, una vez por formulario y por
     página. Junto con la conversión real (/gracias/ con token firmado) da la tasa de
     abandono del formulario. Los clics en CTA son `cta_click`, no envíos. */
  document.addEventListener('focusin', function (e) {
    if (!e.target || typeof e.target.closest !== 'function') return;
    var form = e.target.closest('form[action="/cotizar/enviar.php"]');
    if (!form || form.getAttribute('data-ev-started') === '1') return;
    form.setAttribute('data-ev-started', '1');
    var tipo = form.querySelector('input[name="tipo"]');
    var material = form.querySelector('select[name="material"]');
    var prefix = form.hasAttribute('data-lead-compact') ? 'hero-' : '';
    window.matTrack(tipo && tipo.value === 'proveedor' ? 'supplier_form_start' : 'form_start', {
      ev_loc: prefix + (material && material.value ? material.value : '')
    });
  }, true);
})();
