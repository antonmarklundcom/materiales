<?php
/**
 * partials/category-tiles.php — grilla de rubros de la home y de /materiales/.
 *
 * S9: un rubro 'proxima' (noindex, nofollow, página casi vacía) ya no se enlaza: se muestra
 * como tile apagado "Próximamente", sin <a>. Enlazar desde las dos páginas más fuertes del
 * sitio a una página que le pide a Google que no la indexe sólo gasta rastreo y autoridad.
 *
 * Espera $categories (slug => entrada, ya ordenado) en el scope de quien lo incluye.
 */

declare(strict_types=1);
?>
<ul class="tile-grid tile-grid--3">
  <?php foreach ($categories as $categorySlug => $category): ?>
  <li>
    <?php if (is_published($category)): ?>
    <a class="tile card--hair tile--featured" href="/materiales/<?= e($categorySlug) ?>/">
      <span><?= e($category['name']) ?></span>
      <span class="tile__arrow" aria-hidden="true">→</span>
    </a>
    <?php else: ?>
    <span class="tile card--hair is-proxima">
      <span><?= e($category['name']) ?></span>
      <span class="tile__soon">Próximamente</span>
    </span>
    <?php endif; ?>
  </li>
  <?php endforeach; ?>
</ul>
