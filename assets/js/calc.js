/*
 * calc.js — motor de las calculadoras (fase 12, decisión §1.21, CONTENT-SPEC §12.3).
 *
 * Lee el bloque <script type="application/json" data-calc> de la página y evalúa un ÁRBOL DE
 * EXPRESIONES nodo por nodo. Nunca eval(), nunca Function(): lo que no está en la tabla de
 * operaciones de abajo no se puede ejecutar, aunque el JSON lo pida.
 *
 * Un archivo sirve a todas las calculadoras: la fórmula viaja en el contenido, no en el JS.
 * Sin JS la página sigue siendo correcta — la cuenta está escrita en palabras en la prosa.
 */
(function () {
  var widget = document.querySelector('[data-calc-widget]');
  var source = document.querySelector('script[type="application/json"][data-calc]');
  if (!widget || !source) return;

  var spec;
  try {
    spec = JSON.parse(source.textContent);
  } catch (e) {
    return; // JSON roto: la prosa sigue siendo la respuesta.
  }
  if (!spec || !Array.isArray(spec.outputs)) return;

  var tables = spec.tables || {};
  var inputs = {};
  var nodes = widget.querySelectorAll('[data-calc-input]');
  Array.prototype.forEach.call(nodes, function (el) { inputs[el.getAttribute('data-calc-input')] = el; });

  var OPS = {
    add: function (a) { return a.reduce(function (x, y) { return x + y; }, 0); },
    sub: function (a) { return a.slice(1).reduce(function (x, y) { return x - y; }, a[0]); },
    mul: function (a) { return a.reduce(function (x, y) { return x * y; }, 1); },
    div: function (a) { return a.slice(1).reduce(function (x, y) { return y === 0 ? NaN : x / y; }, a[0]); },
    ceil: function (a) { return Math.ceil(a[0]); },
    floor: function (a) { return Math.floor(a[0]); },
    round: function (a) { return Math.round(a[0]); },
    min: function (a) { return Math.min.apply(null, a); },
    max: function (a) { return Math.max.apply(null, a); }
  };

  /** Valor actual de un input: número, o su string si es un selector. */
  function value(id) {
    var el = inputs[id];
    if (!el) return NaN;
    if (el.tagName === 'SELECT') return el.value;
    var n = parseFloat(String(el.value).replace(',', '.'));
    return isFinite(n) ? n : NaN;
  }

  /** Evalúa un nodo del árbol. Cualquier forma desconocida devuelve NaN, nunca una excepción. */
  function evaluate(node) {
    if (typeof node === 'number') return node;
    if (!node || typeof node !== 'object') return NaN;

    if (typeof node.const === 'number') return node.const;
    if (typeof node.var === 'string') return value(node.var);

    if (typeof node.table === 'string') {
      if (!Object.prototype.hasOwnProperty.call(tables, node.table)) return NaN;
      var table = tables[node.table];
      var key = node.key && typeof node.key === 'object' ? evaluate(node.key) : node.key;
      if (typeof key !== 'string' && typeof key !== 'number') return NaN;
      if (!Object.prototype.hasOwnProperty.call(table, key)) return NaN;
      var found = table[key];
      return typeof found === 'number' ? found : NaN;
    }

    if (typeof node.op === 'string' && Object.prototype.hasOwnProperty.call(OPS, node.op) && Array.isArray(node.args)) {
      var args = node.args.map(evaluate);
      for (var i = 0; i < args.length; i++) {
        if (typeof args[i] !== 'number' || !isFinite(args[i])) return NaN;
      }
      var out = OPS[node.op](args);
      return isFinite(out) ? out : NaN;
    }

    return NaN;
  }

  /** Formato es-PY: coma decimal, sin decimales de más. */
  function format(n) {
    if (!isFinite(n)) return '—';
    var rounded = Math.abs(n - Math.round(n)) < 0.005 ? Math.round(n) : n;
    return rounded.toLocaleString('es-PY', { maximumFractionDigits: 2 });
  }

  var ctaButton = widget.querySelector('[data-calc-cta]');
  var ctaTemplate = ctaButton ? ctaButton.getAttribute('data-calc-cta-template') || '' : '';
  var ctaDefault = ctaButton ? ctaButton.textContent : '';
  var used = false;

  /** calculator_use: una vez por página, al primer cambio real del visitante (C1). */
  function trackUse() {
    if (used) return;
    used = true;
    var slug = widget.getAttribute('data-calc-slug') || '';
    if (window.matTrack) window.matTrack('calculator_use', { ev_loc: slug });
  }

  var quantityField = document.querySelector('.lead-form__form [name="cantidad"]');
  var materialField = document.querySelector('.lead-form__form [name="material"]');
  var template = widget.getAttribute('data-calc-quantity') || '';
  var material = widget.getAttribute('data-calc-material') || '';

  // Preselección del material: la plantilla ya la hace en el servidor; esto sólo cubre el
  // caso de que el visitante lo haya cambiado y después vuelva a tocar la calculadora.
  function preselect() {
    if (materialField && material && materialField.value === '') materialField.value = material;
  }

  // C11 "Sumá todo el pedido" (calculadoras con `bundle` en data/calculators.php).
  var bundleButton = widget.querySelector('[data-calc-bundle]');
  var bundleItems = {};
  var bundleOn = false;
  if (bundleButton) {
    try { bundleItems = JSON.parse(bundleButton.getAttribute('data-calc-bundle-items') || '{}') || {}; } catch (e) { bundleItems = {}; }
  }

  /** "22 bolsas de cemento de 50 kg + 1,1 m³ de arena + …", sin los ítems que dan 0. */
  function bundleText(results) {
    return Object.keys(bundleItems).filter(function (id) {
      return isFinite(results[id]) && results[id] > 0;
    }).map(function (id) {
      return String(bundleItems[id]).replace('{n}', format(results[id]));
    }).join(' + ').slice(0, 200);
  }

  function run() {
    var results = {};
    spec.outputs.forEach(function (output) {
      if (!output || typeof output.id !== 'string') return;
      var n = evaluate(output.expr);
      results[output.id] = n;
      var target = widget.querySelector('[data-calc-output="' + output.id + '"]');
      if (target) target.textContent = format(n);
    });

    // C11: con el paquete activo, la cantidad es el pedido completo (cemento + arena + ripio)
    // y se sigue actualizando si el visitante cambia la cuenta después de sumarlo.
    if (quantityField && bundleOn && !quantityField.dataset.touched) {
      quantityField.value = bundleText(results);
    } else
    // La cantidad del formulario se precarga desde el resultado, salvo que el visitante ya
    // haya escrito algo propio: lo tipeado a mano nunca se pisa.
    if (quantityField && template && !quantityField.dataset.touched) {
      var filled = template.replace(/\{([a-z0-9_]+)\}/gi, function (match, id) {
        return results[id] !== undefined && isFinite(results[id]) ? format(results[id]) : '';
      }).trim();
      quantityField.value = filled;
    }
    preselect();

    // C2: "Cotizá estas 22 bolsas →" con el número de esta cuenta; si alguna salida no da un
    // número válido, vuelve al texto genérico en vez de mostrar "Cotizá estas — bolsas".
    if (ctaButton) {
      var broken = false;
      var label = ctaTemplate.replace(/\{([a-z0-9_]+)\}/gi, function (match, id) {
        if (results[id] === undefined || !isFinite(results[id])) { broken = true; return ''; }
        return format(results[id]);
      });
      ctaButton.textContent = ctaTemplate && !broken ? label : ctaDefault;
    }
  }

  if (quantityField) {
    quantityField.addEventListener('input', function () { quantityField.dataset.touched = '1'; });
  }
  Array.prototype.forEach.call(nodes, function (el) {
    el.addEventListener('input', run);
    el.addEventListener('change', run);
    el.addEventListener('change', trackUse);
  });

  if (bundleButton && quantityField) {
    var bundleWrap = widget.querySelector('[data-calc-bundle-wrap]');
    var bundleStatus = widget.querySelector('[data-calc-bundle-status]');
    if (bundleWrap) bundleWrap.hidden = false;
    bundleButton.addEventListener('click', function () {
      bundleOn = true;
      delete quantityField.dataset.touched; // el clic es una orden explícita: se reescribe
      var wanted = bundleButton.getAttribute('data-calc-bundle-material') || '';
      if (materialField && wanted && materialField.querySelector('option[value="' + wanted + '"]')) {
        materialField.value = wanted;
      }
      run();
      if (bundleStatus) bundleStatus.textContent = 'Listo: sumamos todo al formulario de abajo.';
      var form = quantityField.closest('.lead-form');
      if (form && form.scrollIntoView) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
      var phone = form ? form.querySelector('[name="telefono"]') : null;
      if (phone && phone.value === '') {
        try { phone.focus({ preventScroll: true }); } catch (e) { phone.focus(); }
      }
    });
  }

  widget.classList.add('calc--live');
  run();
})();
