<?php
/**
 * partials/how-it-works.php — "Cómo funciona" en tres pasos + señales de confianza (rediseño,
 * docs/design/REDESIGN.md §4).
 *
 * Una sola fuente para los tres pasos que antes sólo vivían en la home. Todo lo que dice ya lo
 * dice el sitio en otra parte (CONTENT-SPEC §1, §7, /como-trabajamos/): la única cifra es
 * max_proveedores. Nada de plazos, reseñas ni cantidades inventadas.
 *
 * Variables opcionales:
 *   $howVariant  'full' (home: H2 + pasos con título y texto) | 'compact' (páginas de dinero y
 *                /cotizar/: rótulo, pasos de una línea y la lista de confianza)
 */

declare(strict_types=1);

$howVariant = isset($howVariant) ? (string) $howVariant : 'full';
$howMax     = (int) site('max_proveedores', 3);
?>
<?php if ($howVariant === 'full'): ?>
<h2>Cómo funciona</h2>
<ol class="steps">
  <li class="steps__item card card--hair">
    <span class="steps__n" aria-hidden="true">1</span>
    <h3>Contás qué necesitás</h3>
    <p>Material, cantidad y la zona donde lo querés. Un minuto, sin registrarte.</p>
  </li>
  <li class="steps__item card card--hair">
    <span class="steps__n" aria-hidden="true">2</span>
    <h3>Hasta <?= $howMax ?> proveedores verificados te escriben</h3>
    <p>Les llega tu pedido con los datos que necesitan para cotizar, así que te contestan con un precio, no con una pregunta.</p>
  </li>
  <li class="steps__item card card--hair">
    <span class="steps__n" aria-hidden="true">3</span>
    <h3>Elegís el mejor precio</h3>
    <p>Comparás sobre la misma cantidad y la misma entrega, y cerrás directo con el proveedor.</p>
  </li>
</ol>
<?php else: ?>
<div class="how">
  <p class="how__label">Cómo funciona</p>
  <ol class="how__steps">
    <li><span class="how__n" aria-hidden="true">1</span> Contás qué material, cuánto y para qué zona.</li>
    <li><span class="how__n" aria-hidden="true">2</span> Hasta <?= $howMax ?> proveedores verificados te escriben por WhatsApp.</li>
    <li><span class="how__n" aria-hidden="true">3</span> Comparás y cerrás directo con el que te sirva.</li>
  </ol>
  <ul class="trust-list">
    <li>Gratis y sin compromiso</li>
    <li>No publicamos tu teléfono</li>
    <li><a href="/como-trabajamos/">Cómo verificamos a los proveedores</a></li>
  </ul>
</div>
<?php endif; ?>
