<?php
/**
 * guias/index.php — router de /guias/ y /guias/{slug}/ (contenido informativo, plan §5).
 * Cada guía enlaza a sus páginas de dinero; la prosa llega en content/guias/{slug}.php.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$guides = data('guides');
$slug   = (string) ($_GET['slug'] ?? '');

if ($slug === '') {
    $breadcrumbs = [['Inicio', '/'], ['Guías', null]];
    $items = [];
    foreach ($guides as $guideSlug => $guide) {
        $items[] = [$guide['name'], '/guias/' . $guideSlug . '/'];
    }

    page([
        'title'       => 'Guías de obra y materiales en Paraguay',
        'meta'        => 'Cálculos y comparativas para comprar bien: bolsas de cemento por m², qué piedra va en cimientos, qué chapa conviene y más. Con cotización en un paso.',
        'canonical'   => '/guias/',
        'breadcrumbs' => $breadcrumbs,
        'schema'      => [
            schema_breadcrumbs($breadcrumbs),
            schema_item_list('Guías de obra y materiales', '/guias/', $items),
        ],
        'body_class'  => 'page-guias',
    ]);

    require PUBLIC_ROOT . '/partials/header.php';
    echo "<div class=\"page-hero band--dark grain bleed\"><div class=\"wrap\">\n";
    echo "<h1>Guías de obra y materiales</h1>\n";
    echo "</div></div>\n";
    echo "<div class=\"field wrap\"><div class=\"field__panel\">\n";
    echo "<ul class=\"tile-grid\">\n";
    foreach ($guides as $guideSlug => $guide) {
        printf(
            "  <li><a class=\"tile card--hair%s\" href=\"/guias/%s/\"%s><span>%s</span><span class=\"tile__arrow\" aria-hidden=\"true\">→</span></a></li>\n",
            is_published($guide) ? '' : ' is-proxima',
            e($guideSlug),
            is_published($guide) ? '' : ' aria-disabled="true"',
            e($guide['name'])
        );
    }
    echo "</ul>\n";
    echo "</div></div>\n";
    require PUBLIC_ROOT . '/partials/footer.php';
    exit;
}

if (!preg_match('/^[a-z0-9-]+$/', $slug) || !isset($guides[$slug])) {
    not_found();
}

$guide       = $guides[$slug];
$breadcrumbs = [['Inicio', '/'], ['Guías', '/guias/'], [$guide['name'], null]];
$contentFile = CONTENT_DIR . '/guias/' . $slug . '.php';

page([
    'title'       => $guide['title'],
    'meta'        => $guide['meta'],
    'canonical'   => '/guias/' . $slug . '/',
    'noindex'     => !is_published($guide),
    'h1'          => $guide['name'],
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs)],
    'body_class'  => 'page-guia',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1><?= e($guide['name']) ?></h1>
    <?php // Fase 9: la guía no lleva formulario propio, así que el CTA va a /cotizar/. ?>
    <p><a class="btn btn--primary" href="/cotizar/" data-ev="form_submit" data-ev-loc="hero-guia-<?= e($slug) ?>">Pedí tu cotización</a></p>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel">
  <?php if (is_file($contentFile)): ?>
  <div class="prose">
  <?php require $contentFile; ?>
  </div>
  <?php else: ?>
  <p class="notice">Estamos escribiendo esta guía. Mientras tanto, <a href="/cotizar/">pedí tu cotización</a>.</p>
  <?php endif; ?>
  <?php
  $related = array_filter(
      $guide['related'] ?? [],
      static fn(string $target): bool => isset(data('categories')[$target]) || isset(data('materials')[$target])
  );
  if ($related !== []):
  ?>
  <h2>Páginas relacionadas</h2>
  <ul class="tile-grid">
    <?php foreach ($related as $target): ?>
    <?php $entry = data('categories')[$target] ?? data('materials')[$target]; ?>
    <li>
      <a class="tile card--hair" href="/materiales/<?= e($target) ?>/">
        <span><?= e($entry['name']) ?></span>
        <span class="tile__arrow" aria-hidden="true">→</span>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
