<?php
/**
 * materiales/index.php — router de /materiales/ y /materiales/{slug}/.
 *
 * El namespace de slugs es plano y compartido (plan §2): un mismo slug sirve una CATEGORÍA
 * o un MATERIAL. Se buscan primero las categorías, después los materiales; la unicidad la
 * garantiza tools/smoke.php en CI. La jerarquía vive en la miga de pan y en los enlaces
 * internos, nunca en la URL — así un material se recategoriza sin redirección.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';
require PUBLIC_ROOT . '/partials/lead.php';

$slug = (string) ($_GET['slug'] ?? '');

if ($slug === '') {
    require __DIR__ . '/_index.php';
    exit;
}

if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    not_found();
}

$categories = data('categories');
$materials  = data('materials');
if (isset($categories[$slug])) {
    $entry = $categories[$slug];
    $type  = 'categoria';
} else {
    if (!isset($materials[$slug])) {
        not_found();
    }
    $entry = $materials[$slug];
    $type  = 'material';
}

$canonical  = '/materiales/' . $slug . '/';
$published  = is_published($entry);
$contentFile = CONTENT_DIR . '/' . ($type === 'categoria' ? 'categorias' : 'materiales') . '/' . $slug . '.php';

$breadcrumbs = [['Inicio', '/'], ['Materiales', '/materiales/']];
if ($type === 'material') {
    $categorySlug = $entry['category'];
    $breadcrumbs[] = [$categories[$categorySlug]['name'], '/materiales/' . $categorySlug . '/'];
}
$breadcrumbs[] = [$entry['name'], null];

$schema = [schema_breadcrumbs($breadcrumbs)];
if ($type === 'categoria') {
    $items = [];
    foreach (materials_in($slug) as $materialSlug => $material) {
        $items[] = [$material['name'], '/materiales/' . $materialSlug . '/'];
    }
    if ($items !== []) {
        $schema[] = schema_item_list($entry['name'], $canonical, $items);
    }
} else {
    $schema[] = schema_product($slug, $entry);
}
if (($entry['faq'] ?? []) !== []) {
    $schema[] = schema_faq($entry['faq']);
}

page([
    'title'       => $entry['title'],
    'meta'        => $entry['meta'],
    'canonical'   => $canonical,
    'noindex'     => !$published,
    'h1'          => $entry['name'],
    'breadcrumbs' => $breadcrumbs,
    'schema'      => $schema,
    'body_class'  => 'page-' . $type,
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero bleed">
  <div class="wrap">
    <h1><?= e($entry['name']) ?></h1>

    <?php if (($entry['intro'] ?? '') !== ''): ?>
    <p class="lead"><?= e($entry['intro']) ?></p>
    <?php endif; ?>

    <?php if ($type === 'material' && ($entry['sale_unit'] ?? '') !== ''): ?>
    <p class="sale-unit">Se vende por: <strong><?= e($entry['sale_unit']) ?></strong></p>
    <?php endif; ?>
  </div>
</div>

<div class="field wrap">
  <div class="field__panel">

  <?php if (is_file($contentFile)): ?>
  <div class="prose">
  <?php require $contentFile; ?>
  </div>
  <?php else: ?>
  <p class="notice">
    <?php if ($published): ?>
      Estamos publicando el contenido de esta página. Mientras tanto,
      <a href="/cotizar/?m=<?= e($slug) ?>">pedí tu cotización</a> y te contactan hasta
      <?= (int) site('max_proveedores', 3) ?> proveedores verificados.
    <?php else: ?>
      Próximamente vamos a publicar esta página. Si ya necesitás este material,
      <a href="/cotizar/?m=<?= e($slug) ?>">pedí tu cotización</a> igual.
    <?php endif; ?>
  </p>
  <?php endif; ?>

  <?php if ($type === 'categoria'): ?>
  <?php $children = materials_in($slug); ?>
  <?php if ($children !== []): ?>
  <h2>Materiales de <?= e($entry['name']) ?></h2>
  <ul class="tile-grid">
    <?php foreach ($children as $materialSlug => $material): ?>
    <li>
      <a class="tile card--hair<?= is_published($material) ? '' : ' is-proxima' ?>"
         href="/materiales/<?= e($materialSlug) ?>/"<?= is_published($material) ? '' : ' aria-disabled="true"' ?>>
        <span><?= e($material['name']) ?></span>
        <span class="tile__arrow" aria-hidden="true">→</span>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
  <?php else: ?>
  <p class="card card--accent closing-cta">
    <a href="/materiales/<?= e($entry['category']) ?>/">Ver toda la categoría <?= e($categories[$entry['category']]['name']) ?></a>
  </p>
  <?php endif; ?>

  <?php $faq = $entry['faq'] ?? []; ?>
  <?php if ($faq !== []): ?>
  <?php // Las FAQ se muestran SIEMPRE que se emita FAQPage (plan §6): marcado sin contenido
        // visible es marcado inexacto. ?>
  <section class="faq">
    <h2>Preguntas frecuentes<?= $type === 'material' ? ' sobre ' . e(mb_strtolower($entry['name'])) : '' ?></h2>
    <?php foreach ($faq as $item): ?>
    <details>
      <summary><?= e($item['q']) ?></summary>
      <p><?= e($item['a']) ?></p>
    </details>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>

  <?php if ($type === 'material'):
      $related = [];
      foreach ($entry['related'] ?? [] as $target) {
          $candidate = $materials[$target] ?? $categories[$target] ?? null;
          if ($candidate !== null && is_published($candidate)) {
              $related[$target] = $candidate;
          }
      }
  ?>
  <?php if ($related !== []): ?>
  <h2>También te puede servir</h2>
  <ul class="tile-grid">
    <?php foreach ($related as $relatedSlug => $relatedEntry): ?>
    <li>
      <a class="tile card--hair" href="/materiales/<?= e($relatedSlug) ?>/">
        <span><?= e($relatedEntry['name']) ?></span>
        <span class="tile__arrow" aria-hidden="true">→</span>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
  <?php endif; ?>

  <?php
  // El formulario va en TODA página de categoría y de material, con el slug ya preseleccionado
  // (plan §3): el visitante no tiene que volver a elegir lo que la página ya dice.
  $formSlug   = $slug;
  $formOrigen = $canonical;
  $formTitle  = 'Cotizá ' . $entry['name'];
  require PUBLIC_ROOT . '/partials/form.php';
  ?>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
