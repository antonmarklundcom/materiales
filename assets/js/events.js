/*
 * events.js — web-design-system (analytics-prep). Shim inerte de ~350 bytes: no descarga ni
 * envía nada a ningún proveedor, sólo empuja a window.dataLayer para que el día que se
 * conecte GTM/GA4/Plausible cada `data-ev`/`data-ev-loc` ya exista en el marcado. No sustituye
 * a assets/js/analytics.js (fase 2, detrás de consentimiento): esto es tracking de UI sin
 * proveedor, no analítica con terceros.
 */
(function () {
  window.dataLayer = window.dataLayer || [];
  document.addEventListener('click', function (e) {
    if (!e.target || typeof e.target.closest !== 'function') return;
    var t = e.target.closest('[data-ev]');
    if (!t) return;
    window.dataLayer.push({
      event: t.dataset.ev,
      ev_loc: t.dataset.evLoc || '',
      page_path: location.pathname,
      site: location.hostname
    });
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
    window.dataLayer.push({
      event: tipo && tipo.value === 'proveedor' ? 'supplier_form_start' : 'form_start',
      ev_loc: material && material.value ? material.value : '',
      page_path: location.pathname,
      site: location.hostname
    });
  }, true);
})();
