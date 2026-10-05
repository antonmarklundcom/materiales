<?php
/** Preparation from existing sale units/related calculators; no product specs invented. */
declare(strict_types=1);
$preparationCalcs = array_slice(calculators_for($slug, $type === 'material' ? (string) $entry['category'] : ''), 0, 2, true);
?>
<section class="material-preparation" aria-label="Prepará tu consulta">
  <div>
    <p class="eyebrow">Antes de pedir precio</p>
    <h2>Prepará una consulta clara</h2>
    <p><?php if ($type === 'material' && ($entry['sale_unit'] ?? '') !== ''): ?>Anotá la cantidad en <strong><?= e($entry['sale_unit']) ?></strong>, la medida y la zona.<?php else: ?>Elegí el material y anotá su cantidad, unidad y zona de entrega.<?php endif; ?> El precio, el stock y el flete se confirman al cotizar.</p>
  </div>
  <?php if ($preparationCalcs !== []): ?>
  <div class="material-preparation__tools"><p>¿Todavía no tenés la cantidad?</p>
    <?php foreach ($preparationCalcs as $prepSlug => $prepCalc): ?>
    <a href="/calculadoras/<?= e($prepSlug) ?>/"><?= e($prepCalc['name']) ?> <span aria-hidden="true">→</span></a>
    <?php endforeach; ?>
    <small>Estimaciones para el pedido; no reemplazan el proyecto ni el cálculo de la obra.</small>
  </div>
  <?php endif; ?>
</section>
