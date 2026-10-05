/* Exercise the shipped calculator engine with its real data/formulas, without network. */
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { spawnSync } from 'node:child_process';
import vm from 'node:vm';

const php = process.env.PHP_BINARY || 'php';
const data = spawnSync(php, ['-r', 'echo json_encode(require "data/calculators.php");'], { encoding: 'utf8' });
assert.equal(data.status, 0, data.stderr);
const calculators = JSON.parse(data.stdout);
const engine = readFileSync('assets/js/calc.js', 'utf8');
let checks = 0;
function check(condition, message) { assert.ok(condition, message); checks++; }

function element(tagName, value = '', attributes = {}) {
  return {
    tagName, value: String(value), textContent: '', dataset: {}, listeners: {}, hidden: false,
    validity: { valid: true }, disabled: false,
    getAttribute(key) { return Object.hasOwn(attributes, key) ? String(attributes[key]) : null; },
    setAttribute(key, val) { attributes[key] = val; },
    addEventListener(event, fn) { this.listeners[event] = fn; },
    focus() {}, closest() { return null; }, querySelector() { return null; },
  };
}

function mount(slug, overrides = {}) {
  const entry = calculators[slug];
  const content = readFileSync(`content/calculadoras/${slug}.php`, 'utf8');
  const spec = JSON.parse(content.match(/<script type="application\/json" data-calc>\s*([\s\S]*?)<\/script>/)[1]);
  const inputs = Object.fromEntries(entry.inputs.map(input => [input.id,
    element(input.type === 'select' ? 'SELECT' : 'INPUT', overrides[input.id] ?? input.default,
      { 'data-calc-input': input.id, ...(input.type === 'select' ? {} : { min: input.min, max: input.max }) })]));
  const outputs = Object.fromEntries(entry.outputs.map(output => [output.id, element('STRONG')]));
  const quantity = element('INPUT');
  const material = element('SELECT', entry.cta_material);
  material.querySelector = () => ({});
  const validation = element('P');
  const cta = element('A', '', { 'data-calc-cta-template': entry.cta_button || entry.cta_label || '' });
  cta.textContent = 'Pedí tu cotización';
  const bundle = entry.bundle ? element('BUTTON', '', {
    'data-calc-bundle-items': JSON.stringify(entry.bundle.items),
    'data-calc-bundle-material': entry.bundle.material,
  }) : null;
  const widget = {
    classList: { add() {} },
    getAttribute(key) { return ({ 'data-calc-slug': slug, 'data-calc-quantity': entry.cta_quantity_template, 'data-calc-material': entry.cta_material })[key] || ''; },
    querySelectorAll() { return Object.values(inputs); },
    querySelector(selector) {
      if (selector === '[data-calc-cta]') return cta;
      if (selector === '[data-calc-bundle]') return bundle;
      if (selector === '[data-calc-validation]') return validation;
      const id = selector.match(/^\[data-calc-output="(.+)"\]$/)?.[1];
      return id ? outputs[id] : null;
    },
  };
  const document = { querySelector(selector) {
    if (selector === '[data-calc-widget]') return widget;
    if (selector.includes('script[')) return { textContent: JSON.stringify(spec) };
    if (selector.includes('cantidad')) return quantity;
    if (selector.includes('material')) return material;
    return null;
  } };
  vm.runInNewContext(engine, { document, window: {} });
  return { inputs, outputs, quantity, material, validation, cta, bundle,
    change(id, value, browserValid = true) { inputs[id].value = String(value); inputs[id].validity.valid = browserValid; inputs[id].listeners.input(); } };
}

for (const [slug, entry] of Object.entries(calculators)) {
  const calc = mount(slug);
  check(calc.validation.hidden, `${slug}: valid defaults`);
  check(Object.values(calc.outputs).every(el => el.textContent !== '—'), `${slug}: all defaults computed`);
  const numeric = entry.inputs.find(input => input.type !== 'select');
  for (const invalid of ['', '-1', String(Number(numeric.max) + 1), '10x', 'Infinity']) {
    calc.change(numeric.id, invalid);
    check(!calc.validation.hidden, `${slug}: reject ${JSON.stringify(invalid)}`);
    check(Object.values(calc.outputs).every(el => el.textContent === '—'), `${slug}: no partial results`);
    check(calc.quantity.value === '', `${slug}: no invalid automatic quantity`);
    let prevented = false;
    calc.cta.listeners.click({ preventDefault() { prevented = true; } });
    check(prevented, `${slug}: invalid CTA cannot go to quote`);
    if (calc.bundle) check(calc.bundle.disabled, `${slug}: invalid bundle disabled`);
  }
  calc.change(numeric.id, numeric.default);
  check(calc.validation.hidden && calc.quantity.value !== '', `${slug}: recover after correction`);
  calc.quantity.value = 'Pedido escrito por la persona';
  calc.quantity.listeners.input();
  calc.change(numeric.id, '');
  check(calc.quantity.value === 'Pedido escrito por la persona', `${slug}: preserve manual quantity`);
  calc.change(numeric.id, numeric.default, false);
  check(!calc.validation.hidden, `${slug}: respect browser validity (e.g. step mismatch)`);
}

// Independent worked amounts: 2 m³ structural, 10% waste, 350 kg/m³, bags of 50 kg.
const concrete = mount('hormigon-por-m3', { m3: 2, tipo: 'estructural' });
check(concrete.outputs.bolsas.textContent === '16', 'concrete: round 15.4 bags upwards');
check(concrete.outputs.arena.textContent === (1.1).toLocaleString('es-PY'), 'concrete: 1.10 m³ sand');
check(concrete.outputs.ripio.textContent === (1.65).toLocaleString('es-PY'), 'concrete: 1.65 m³ aggregate');
check(concrete.outputs.agua.textContent === '385', 'concrete: 385 L water');
concrete.bundle.listeners.click();
check(/16 bolsas/.test(concrete.quantity.value) && /arena/.test(concrete.quantity.value) && /ripio/.test(concrete.quantity.value), 'bundle retains all units');
concrete.change('m3', 1);
check(/8 bolsas/.test(concrete.quantity.value), 'bundle updates after changing volume');
concrete.change('m3', -1);
check(concrete.quantity.value === '', 'invalid volume clears automatically generated bundle');

const floor = mount('bolsas-de-cemento-por-m2', { m2: 40, espesor: 10, tipo: 'contrapiso' });
check(floor.outputs.bolsas.textContent === '22', '40 m² x 10 cm + 10%: 22 bags');
check(floor.outputs.arena.textContent === (2.2).toLocaleString('es-PY'), 'floor: 2.20 m³ sand');
check(floor.outputs.ripio.textContent === (3.74).toLocaleString('es-PY'), 'floor: 3.74 m³ aggregate');
console.log(`CALCULATOR PASS: ${Object.keys(calculators).length} real calculators, ${checks} assertions`);
