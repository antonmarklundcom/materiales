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
    var t = e.target.closest('[data-ev]');
    if (!t) return;
    window.dataLayer.push({
      event: t.dataset.ev,
      ev_loc: t.dataset.evLoc || '',
      page_path: location.pathname,
      site: location.hostname
    });
  }, true);
})();
