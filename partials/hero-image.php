<?php
/**
 * partials/hero-image.php — foto del héroe (fase 11, decisión §1.20).
 *
 * Se incluye DENTRO de `.page-hero`; si la entrada no declara imagen o el archivo todavía no
 * está en disco (`image_for()` devuelve null), la página no cambia en nada respecto de hoy:
 * héroe de paleta y `og-default.jpg`. Los archivos los sube Anton (§7) y los cablea la
 * fase 15.
 *
 * Variables que define la página que lo incluye:
 *   $heroImage  ruta relativa devuelta por image_for() — null/'' ⇒ no imprime nada
 *   $heroAlt    texto alternativo en es-PY
 *
 * `<picture>` sin `<source>` alternativo a propósito: hoy sólo hay JPG 1200×630. El día que
 * haya WebP se agrega acá y ninguna plantilla se entera.
 */

declare(strict_types=1);

$heroImage = isset($heroImage) ? (string) $heroImage : '';
$heroAlt   = isset($heroAlt) ? (string) $heroAlt : '';

if ($heroImage === '') {
    return;
}
?>
<picture class="page-hero__media">
  <img src="/<?= e(ltrim($heroImage, '/')) ?>" alt="<?= e($heroAlt) ?>"
       width="1200" height="630" loading="eager" decoding="async">
</picture>
