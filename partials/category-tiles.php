<?php
/**
 * partials/category-tiles.php — grilla de rubros de la home y de /materiales/.
 *
 * S9: un rubro 'proxima' (noindex, nofollow, página casi vacía) ya no se enlaza: se muestra
 * como tile apagado "Próximamente", sin <a>. Enlazar desde las dos páginas más fuertes del
 * sitio a una página que le pide a Google que no la indexe sólo gasta rastreo y autoridad.
 *
 * Rediseño (docs/design/REDESIGN.md §4): tarjeta con la foto del rubro (la misma del héroe de
 * su página, variante 640 px, lazy) y la cantidad de materiales activos, calculada desde los
 * datos. Sin foto (rubro recién promovido) queda la tarjeta de paleta con la inicial.
 *
 * Espera $categories (slug => entrada, ya ordenado) en el scope de quien lo incluye.
 */

declare(strict_types=1);
?>
<ul class="cat-grid">
  <?php foreach ($categories as $categorySlug => $category): ?>
  <li>
    <?php if (is_published($category)):
        $catImage = image_for($category, 'categoria');
        $catCount = count(array_filter(materials_in($categorySlug), 'is_published'));
    ?>
    <a class="cat-card" href="/materiales/<?= e($categorySlug) ?>/">
      <?php if ($catImage !== null): $catBase = '/' . ltrim($catImage, '/'); ?>
      <picture class="cat-card__media">
        <source type="image/avif" srcset="<?= e($catBase . '-640.avif') ?>">
        <img src="<?= e($catBase . '-640.webp') ?>" alt="" width="640" height="360" loading="lazy" decoding="async">
      </picture>
      <?php else: ?>
      <span class="cat-card__media cat-card__media--ph" aria-hidden="true"><?= e(mb_substr($category['name'], 0, 1)) ?></span>
      <?php endif; ?>
      <span class="cat-card__body">
        <span class="cat-card__name"><?= e($category['name']) ?></span>
        <?php if ($catCount > 0): ?><span class="cat-card__meta"><?= $catCount ?> <?= $catCount === 1 ? 'material' : 'materiales' ?></span><?php endif; ?>
      </span>
    </a>
    <?php else: ?>
    <span class="cat-card is-proxima">
      <span class="cat-card__media cat-card__media--ph" aria-hidden="true"><?= e(mb_substr($category['name'], 0, 1)) ?></span>
      <span class="cat-card__body">
        <span class="cat-card__name"><?= e($category['name']) ?></span>
        <span class="cat-card__meta">Próximamente</span>
      </span>
    </span>
    <?php endif; ?>
  </li>
  <?php endforeach; ?>
</ul>
