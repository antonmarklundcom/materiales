/*
 * forms.js — validación en el navegador de los formularios de leads (C5).
 *
 * Antes el formulario iba con `novalidate` y sin JS de validación: un teléfono mal tipeado o
 * la casilla sin marcar hacían el viaje completo al servidor, que volvía con ?error= y el
 * nombre y el teléfono borrados a propósito (no viajan en la URL). Ahora se frena ANTES de
 * enviar, con el mismo mensaje que daría el servidor, sin perder nada de lo tipeado. El
 * servidor sigue validando todo igual (cotizar/enviar.php): esto es comodidad, no seguridad.
 *
 * La regla del teléfono es un espejo de lead_normalize_phone() en partials/lead.php. Si
 * cambia una, cambia la otra.
 */
(function () {
  'use strict';

  var MESSAGES = {
    telefono: 'Revisá el teléfono: necesitamos un número paraguayo, por ejemplo 0981 123 456.',
    consentimiento: 'Para poder pasarle tu pedido a los proveedores necesitamos que marques la casilla.',
    consentimientoProveedor: 'Para poder contactarte necesitamos que marques la casilla.',
    empresa: 'Escribí el nombre de tu empresa o corralón.',
    rubros: 'Marcá al menos un rubro: es lo que define qué pedidos te llegan.'
  };

  function phoneOk(raw) {
    var digits = String(raw || '').replace(/\D+/g, '');
    if (!digits) return false;
    var national = digits.indexOf('595') === 0 ? digits.slice(3)
      : digits.charAt(0) === '0' ? digits.replace(/^0+/, '') : digits;
    if (national.length < 7 || national.length > 11) return false;
    if (!/^[2-9]/.test(national)) return false;
    if (national.charAt(0) === '9' && national.length !== 9) return false;
    return true;
  }

  function errorBox(form) {
    var section = form.closest('.lead-form') || form.parentNode;
    var box = section.querySelector('.lead-form__error');
    if (!box) {
      box = document.createElement('p');
      box.className = 'lead-form__error';
      box.id = (section.id || 'form') + '-error';
      box.setAttribute('role', 'alert');
      form.parentNode.insertBefore(box, form);
    }
    return box;
  }

  function clear(form) {
    Array.prototype.forEach.call(form.querySelectorAll('[aria-invalid="true"]'), function (el) {
      el.removeAttribute('aria-invalid');
    });
  }

  function check(form) {
    var isSupplier = !!form.querySelector('input[name="tipo"][value="proveedor"]');
    var empresa = form.querySelector('[name="empresa"]');
    if (isSupplier && empresa && !empresa.value.trim()) return { field: empresa, msg: MESSAGES.empresa };
    if (isSupplier && !form.querySelector('input[name="rubros[]"]:checked')) {
      return { field: form.querySelector('input[name="rubros[]"]'), msg: MESSAGES.rubros };
    }
    var phone = form.querySelector('[name="telefono"]');
    if (phone && !phoneOk(phone.value)) return { field: phone, msg: MESSAGES.telefono };
    var consent = form.querySelector('[name="consentimiento"]');
    if (consent && !consent.checked) {
      return { field: consent, msg: isSupplier ? MESSAGES.consentimientoProveedor : MESSAGES.consentimiento };
    }
    return null;
  }

  /* C3: formulario corto del héroe. Arranca con cantidad + WhatsApp; el primer foco o tecleo
     despliega el resto (material, zona, nombre, mensaje y el consentimiento). Sin JS se ve
     completo: la regla que lo pliega en site.css depende de html.js. */
  function expand(form) {
    var section = form.closest('.lead-form');
    if (section && !section.classList.contains('is-expanded')) section.classList.add('is-expanded');
  }

  Array.prototype.forEach.call(document.querySelectorAll('form[data-lead-form]'), function (form) {
    if (form.hasAttribute('data-lead-compact')) {
      form.addEventListener('focusin', function () { expand(form); });
      form.addEventListener('input', function () { expand(form); });
    }
    form.addEventListener('submit', function (event) {
      clear(form);
      var problem = check(form);
      if (!problem) return;
      event.preventDefault();
      expand(form);
      var box = errorBox(form);
      box.textContent = problem.msg;
      box.hidden = false;
      if (problem.field) {
        problem.field.setAttribute('aria-invalid', 'true');
        problem.field.setAttribute('aria-describedby', box.id);
        problem.field.focus();
      }
      if (window.matTrack) window.matTrack('form_invalid', { ev_loc: problem.field ? problem.field.name : '' });
    });
    form.addEventListener('input', function (event) {
      if (event.target && event.target.getAttribute('aria-invalid') === 'true') {
        event.target.removeAttribute('aria-invalid');
      }
    });
  });
})();
