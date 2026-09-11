<?php
/**
 * calculadoras/index.php — router de /calculadoras/ y /calculadoras/{slug}/ (plan §11.4,
 * decisión §1.21). Espeja a guias/index.php: namespace de slugs propio, datos en
 * data/calculators.php y prosa en content/calculadoras/{slug}.php.
 *
 * La página funciona SIN JavaScript: la fórmula está escrita en palabras con un ejemplo
 * resuelto en la prosa, que además es el contenido que rankea. `calc.js` sólo agrega el
 * cálculo en vivo y precarga el formulario (CONTENT-SPEC §12).
 *
 * No se emite HowTo: ya no gana resultados enriquecidos y marcar de más es marcar mal.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';
require PUBLIC_ROOT . '/partials/lead.php';

$calculators = data('calculators');
$slug        = (string) ($_GET['slug'] ?? '');

if ($slug === '') {
    $breadcrumbs = [['Inicio', '/'], ['Calculadoras', null]];
    $published   = array_filter($calculators, 'is_published');
    uasort($published, static fn(array $a, array $b): int => ($a['order'] ?? 99) <=> ($b['order'] ?? 99));

    $items = [];
    foreach ($published as $calcSlug => $calculator) {
        $items[] = [$calculator['name'], '/calculadoras/' . $calcSlug . '/'];
    }

    page([
        'title'       => 'Calculadoras de materiales para tu obra',
        'meta'        => 'Calculá cuánto material lleva tu obra: bolsas de cemento por m² y más. Con la cuenta explicada, los supuestos a la vista y cotización en un paso.',
        'canonical'   => '/calculadoras/',
        'h1'          => 'Calculadoras de materiales',
        'breadcrumbs' => $breadcrumbs,
        'schema'      => [
            schema_breadcrumbs($breadcrumbs),
            schema_item_list('Calculadoras de materiales', '/calculadoras/', $items),
        ],
        'body_class'  => 'page-calculadoras',
    ]);

    require PUBLIC_ROOT . '/partials/header.php';
    ?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>Calculadoras de materiales</h1>
    <p class="lead">
      Cuánto material lleva tu obra, con la cuenta explicada y los supuestos a la vista.
      Cada resultado es una referencia — confirmá con tu proveedor.
    </p>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel">
    <?php if ($published === []): ?>
    <p class="notice">Estamos publicando las primeras calculadoras. Mientras tanto, <a href="/cotizar/">pedí tu cotización</a>.</p>
    <?php else: ?>
    <ul class="tile-grid">
      <?php foreach ($published as $calcSlug => $calculator): ?>
      <li>
        <a class="tile card--hair" href="/calculadoras/<?= e($calcSlug) ?>/">
          <span><?= e($calculator['name']) ?></span>
          <span class="tile__arrow" aria-hidden="true">→</span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <p class="card card--accent closing-cta">
      <a href="/guias/">Ver las guías de obra</a>
    </p>
  </div>
</div>
    <?php
    require PUBLIC_ROOT . '/partials/footer.php';
    exit;
}

if (!preg_match('/^[a-z0-9-]+$/', $slug) || !isset($calculators[$slug])) {
    not_found();
}

$calculator  = $calculators[$slug];
$canonical   = '/calculadoras/' . $slug . '/';
$contentFile = CONTENT_DIR . '/calculadoras/' . $slug . '.php';
$breadcrumbs = [['Inicio', '/'], ['Calculadoras', '/calculadoras/'], [$calculator['name'], null]];

$schema = [schema_breadcrumbs($breadcrumbs)];
if (($calculator['faq'] ?? []) !== []) {
    $schema[] = schema_faq($calculator['faq']);
}

page([
    'title'       => $calculator['title'],
    'meta'        => $calculator['meta'],
    'canonical'   => $canonical,
    'noindex'     => !is_published($calculator),
    'h1'          => $calculator['name'],
    'breadcrumbs' => $breadcrumbs,
    'schema'      => $schema,
    'body_class'  => 'page-calculadora',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1><?= e($calculator['name']) ?></h1>
    <?php if (($calculator['intro'] ?? '') !== ''): ?>
    <p class="lead"><?= e($calculator['intro']) ?></p>
    <?php endif; ?>
  </div>
</div>

<div class="field wrap">
  <div class="field__panel">

    <?php // El widget: sin JS quedan los campos y el aviso de que la cuenta está más abajo. ?>
    <section class="calc" data-calc-widget data-calc-slug="<?= e($slug) ?>"
             data-calc-quantity="<?= e((string) ($calculator['cta_quantity_template'] ?? '')) ?>"
             data-calc-material="<?= e((string) ($calculator['cta_material'] ?? '')) ?>">
      <h2>Hacé la cuenta</h2>
      <div class="calc__inputs">
        <?php foreach ($calculator['inputs'] ?? [] as $input): ?>
        <label class="lead-form__field">
          <span><?= e($input['label']) ?><?= ($input['unit'] ?? '') !== '' ? ' <em>(' . e($input['unit']) . ')</em>' : '' ?></span>
          <?php if (($input['type'] ?? 'number') === 'select'): ?>
          <select data-calc-input="<?= e($input['id']) ?>">
            <?php foreach ($input['options'] ?? [] as $option): ?>
            <option value="<?= e($option['value']) ?>"<?= ($input['default'] ?? '') === $option['value'] ? ' selected' : '' ?>><?= e($option['label']) ?></option>
            <?php endforeach; ?>
          </select>
          <?php else: ?>
          <input type="number" inputmode="decimal" data-calc-input="<?= e($input['id']) ?>"
                 min="<?= e((string) ($input['min'] ?? 0)) ?>" max="<?= e((string) ($input['max'] ?? 100000)) ?>"
                 step="<?= e((string) ($input['step'] ?? 1)) ?>" value="<?= e((string) ($input['default'] ?? '')) ?>">
          <?php endif; ?>
        </label>
        <?php endforeach; ?>
      </div>

      <ul class="calc__outputs">
        <?php foreach ($calculator['outputs'] ?? [] as $output): ?>
        <li class="calc__output">
          <strong data-calc-output="<?= e($output['id']) ?>">—</strong>
          <span><?= e($output['label']) ?><?= ($output['unit'] ?? '') !== '' ? ' (' . e($output['unit']) . ')' : '' ?></span>
        </li>
        <?php endforeach; ?>
      </ul>

      <p class="calc__disclaimer">Es una referencia — confirmá con tu proveedor.</p>
      <p class="calc__nojs">Si no ves los resultados, tu navegador tiene el JavaScript desactivado: la misma cuenta está explicada paso a paso más abajo, con un ejemplo resuelto.</p>

      <?php if (($calculator['formula_note'] ?? '') !== ''): ?>
      <p class="calc__formula"><strong>Cómo se calcula:</strong> <?= e($calculator['formula_note']) ?></p>
      <?php endif; ?>

      <?php if (($calculator['assumptions'] ?? []) !== []): ?>
      <div class="calc__assumptions">
        <h3>Supuestos</h3>
        <ul>
          <?php foreach ($calculator['assumptions'] as $assumption): ?>
          <li><?= e($assumption) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
    </section>

    <?php if (is_file($contentFile)): ?>
    <div class="prose">
    <?php require $contentFile; ?>
    </div>
    <?php else: ?>
    <p class="notice">Estamos escribiendo la explicación de esta calculadora. Mientras tanto, <a href="/cotizar/">pedí tu cotización</a>.</p>
    <?php endif; ?>

    <?php $faq = $calculator['faq'] ?? []; ?>
    <?php if ($faq !== []): ?>
    <section class="faq">
      <h2>Preguntas frecuentes</h2>
      <?php foreach ($faq as $item): ?>
      <details>
        <summary><?= e($item['q']) ?></summary>
        <p><?= e($item['a']) ?></p>
      </details>
      <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php
    // Páginas de dinero y guías a las que sirve esta calculadora (fase 11).
    $relatedTargets = [];
    foreach ($calculator['related'] ?? [] as $target) {
        $target = (string) $target;
        if (isset(data('categories')[$target]) || isset(data('materials')[$target])) {
            $relatedTargets[] = ['/materiales/' . $target . '/', (data('categories')[$target] ?? data('materials')[$target])['name']];
        } elseif (isset(data('guides')[$target]) && is_published(data('guides')[$target])) {
            $relatedTargets[] = ['/guias/' . $target . '/', data('guides')[$target]['name']];
        }
    }
    ?>
    <?php if ($relatedTargets !== []): ?>
    <h2>Páginas relacionadas</h2>
    <ul class="tile-grid">
      <?php foreach ($relatedTargets as [$targetPath, $targetName]): ?>
      <li>
        <a class="tile card--hair" href="<?= e($targetPath) ?>">
          <span><?= e($targetName) ?></span>
          <span class="tile__arrow" aria-hidden="true">→</span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <?php
    // El formulario cierra la página con el material de la calculadora preseleccionado;
    // calc.js le escribe la cantidad calculada apenas cambia un input.
    $formSlug   = (string) ($calculator['cta_material'] ?? '');
    $formOrigen = $canonical;
    $formTitle  = 'Pedí tu cotización';
    require PUBLIC_ROOT . '/partials/form.php';
    ?>
  </div>
</div>
<script src="/assets/js/calc.js" defer></script>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
