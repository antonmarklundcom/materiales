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
        'meta'        => 'Comparativas, procesos y cálculos para comprar bien: ladrillo común o hueco, qué piedra va en cimientos, qué chapa conviene y más. Cotizá en un paso.',
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
    // G8: el hub tenía 83 palabras; la intro dice qué tipo de guía hay y cómo se usa.
    require CONTENT_DIR . '/hubs/guias.php';
    echo "<ul class=\"tile-grid\">\n";
    foreach ($guides as $guideSlug => $guide) {
        printf(
            "  <li><a class=\"tile card--hair%s\" href=\"/guias/%s/\"><span>%s</span><span class=\"tile__arrow\" aria-hidden=\"true\">→</span></a></li>\n",
            is_published($guide) ? '' : ' is-proxima',
            e($guideSlug),
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

// Imagen del héroe: una guía hereda la de su primera página de dinero (decisión §1.20).
$heroImage = image_for($guide, 'guia');
$heroAlt   = $guide['name'] . ' — materiales de construcción en Paraguay';

// C9: la guía no tiene formulario propio, pero sí sabe de qué material habla: su primer
// related[] válido va preseleccionado en /cotizar/?m= en vez de mandar a un formulario vacío.
$related = array_values(array_filter(
    $guide['related'] ?? [],
    static fn(string $target): bool => isset(data('categories')[$target]) || isset(data('materials')[$target])
));
$ctaTarget = $related[0] ?? '';
$ctaEntry  = $ctaTarget !== '' ? (data('categories')[$ctaTarget] ?? data('materials')[$ctaTarget]) : null;
$ctaHref   = $ctaTarget !== '' ? '/cotizar/?m=' . rawurlencode($ctaTarget) : '/cotizar/';

page([
    'title'       => $guide['title'],
    'meta'        => $guide['meta'],
    'canonical'   => '/guias/' . $slug . '/',
    'noindex'     => !is_published($guide),
    'h1'          => $guide['name'],
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs)],
    'body_class'  => 'page-guia',
    'image'       => (string) $heroImage,
    'wa_subject'  => $ctaEntry !== null ? (string) $ctaEntry['name'] : '',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap<?= $heroImage !== null ? ' page-hero__grid page-hero__grid--split' : '' ?>">
    <div>
    <h1><?= e($guide['name']) ?></h1>
    <?php // Fase 9: la guía no lleva formulario propio, así que el CTA va a /cotizar/ (C9: con
          // el material de la guía ya elegido). ?>
    <p><a class="btn btn--primary" href="<?= e($ctaHref) ?>" data-ev="cta_click" data-ev-loc="hero-guia-<?= e($slug) ?>">Pedí tu cotización</a></p>
    </div>
    <?php require PUBLIC_ROOT . '/partials/hero-image.php'; ?>
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
  <?php if ($ctaEntry !== null): ?>
  <div class="card card--accent guide-cta">
    <p class="guide-cta__text"><strong>¿Ya tenés la cuenta?</strong> Pasale la cantidad a hasta
      <?= (int) site('max_proveedores', 3) ?> proveedores verificados y compará precios. Gratis.</p>
    <p><a class="btn btn--primary" href="<?= e($ctaHref) ?>" data-ev="cta_click" data-ev-loc="guia-cierre-<?= e($slug) ?>">Cotizá <?= e(mb_strtolower($ctaEntry['name'])) ?></a></p>
  </div>
  <?php endif; ?>
  <?php if ($related !== []): ?>
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

  <?php
  // Fase 11: las calculadoras que sirven a esta guía, calculadas desde data/calculators.php
  // (la fase 12 crea el archivo; hasta entonces esto no imprime nada).
  $relatedSlug     = $slug;
  $relatedCategory = '';
  $relatedBlocks   = ['calculadoras'];
  require PUBLIC_ROOT . '/partials/related.php';
  ?>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
