/*
 * consent.js — almacenamiento de la elección de cookies (Ley 6534 + 7593, plan §6).
 * Estadísticas y Marketing arrancan APAGADAS. Nada de tracking se carga desde acá:
 * la fase 2 escucha el evento `consent:changed` para inyectar GA4 / Meta Pixel.
 */
(function () {
  'use strict';

  var KEY = 'mat_consent_v1';
  var banner = document.getElementById('cookie-banner');
  var form = document.getElementById('cookie-banner-form');

  function read() {
    try {
      return JSON.parse(localStorage.getItem(KEY) || 'null');
    } catch (err) {
      return null;
    }
  }

  function save(consent) {
    consent.ts = new Date().toISOString();
    try {
      localStorage.setItem(KEY, JSON.stringify(consent));
    } catch (err) { /* modo privado: la elección dura la sesión */ }
    publish(consent);
    if (banner) banner.hidden = true;
  }

  function publish(consent) {
    window.matConsent = consent;
    document.dispatchEvent(new CustomEvent('consent:changed', { detail: consent }));
  }

  var stored = read();
  if (stored) {
    publish(stored);
  } else {
    publish({ necessary: true, analytics: false, marketing: false });
    if (banner) banner.hidden = false;
  }

  if (form) {
    form.addEventListener('click', function (event) {
      var action = event.target && event.target.getAttribute('data-consent');
      if (action === 'reject') {
        save({ necessary: true, analytics: false, marketing: false });
      } else if (action === 'accept-all') {
        save({ necessary: true, analytics: true, marketing: true });
      }
    });
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      save({
        necessary: true,
        analytics: form.analytics.checked,
        marketing: form.marketing.checked
      });
    });
  }
})();
