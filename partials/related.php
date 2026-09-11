<?php
/**
 * partials/related.php — "Guías relacionadas" y "Calculadoras relacionadas" (fase 11,
 * decisión §1.19).
 *
 * Los enlaces se CALCULAN desde `related[]` de cada guía y cada calculadora: la página de
 * dinero no tipea ninguno. Así una guía nueva aparece sola en sus materiales el día que se
 * publica, y no quedan enlaces apuntando a páginas que todavía no existen.
 *
 * Variables que define la página que lo incluye:
 *   $relatedSlug      slug de la página actual (material o categoría)
 *   $relatedCategory  slug de su categoría ('' en una página de categoría)
 *   $relatedBlocks    qué bloques mostrar: ['guias', 'calculadoras'] por defecto
 *
 * Si no hay nada que mostrar no imprime absolutamente nada (ni el H2 ni la lista).
 */

declare(strict_types=1);

$relatedSlug     = isset($relatedSlug) ? (string) $relatedSlug : '';
$relatedCategory = isset($relatedCategory) ? (string) $relatedCategory : '';
$relatedBlocks   = isset($relatedBlocks) ? (array) $relatedBlocks : ['guias', 'calculadoras'];

$relatedLists = [];
if (in_array('guias', $relatedBlocks, true)) {
    $relatedLists[] = ['Guías relacionadas', '/guias/', guides_for($relatedSlug, $relatedCategory)];
}
if (in_array('calculadoras', $relatedBlocks, true)) {
    $relatedLists[] = ['Calculadoras relacionadas', '/calculadoras/', calculators_for($relatedSlug, $relatedCategory)];
}
?>
<?php foreach ($relatedLists as [$relatedTitle, $relatedBase, $relatedEntries]): ?>
<?php if ($relatedEntries !== []): ?>
<h2><?= e($relatedTitle) ?></h2>
<ul class="tile-grid">
  <?php foreach ($relatedEntries as $relatedEntrySlug => $relatedEntry): ?>
  <li>
    <a class="tile card--hair" href="<?= e($relatedBase . $relatedEntrySlug) ?>/">
      <span><?= e($relatedEntry['name']) ?></span>
      <span class="tile__arrow" aria-hidden="true">→</span>
    </a>
  </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
<?php endforeach; ?>
