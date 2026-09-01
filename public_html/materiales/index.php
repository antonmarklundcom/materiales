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

$slug = (string) ($_GET['slug'] ?? '');

if ($slug === '') {
    require __DIR__ . '/_index.php';
    exit;
}

if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    not_found();
}

$categories = data('categories');
if (isset($categories[$slug])) {
    $entry = $categories[$slug];
    $type  = 'categoria';
} else {
    $materials = data('materials');
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
<h1><?= e($entry['name']) ?></h1>

<?php if (is_file($contentFile)): ?>
<?php require $contentFile; ?>
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
<ul class="card-list">
  <?php foreach ($children as $materialSlug => $material): ?>
  <li><a href="/materiales/<?= e($materialSlug) ?>/"<?= is_published($material) ? '' : ' class="is-proxima"' ?>><?= e($material['name']) ?></a></li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
<?php else: ?>
<p><a href="/materiales/<?= e($entry['category']) ?>/">Ver toda la categoría <?= e($categories[$entry['category']]['name']) ?></a></p>
<?php endif; ?>

<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
