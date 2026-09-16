<?php
/**
 * partials/hero-image.php — foto responsive del héroe.
 *
 * Se incluye DENTRO de `.page-hero`; si image_for() devuelve null o la ruta base
 * está vacía, no imprime nada y se conserva el héroe de paleta.
 *
 * Variables que define la página que lo incluye:
 *   $heroImage  ruta base relativa sin extensión ni ancho, devuelta por image_for()
 *   $heroAlt    texto alternativo en es-PY
 *
 * Cada base dispone de AVIF y WebP en anchos 640, 1280 y 1920, con proporción 16:9.
 * El fallback es WebP 1280×720; la carga eager es deliberada para el héroe (LCP).
 */

declare(strict_types=1);

$heroImage = isset($heroImage) ? (string) $heroImage : '';
$heroAlt   = isset($heroAlt) ? (string) $heroAlt : '';

if ($heroImage === '') {
    return;
}
?>
<picture class="page-hero__media">
  <source type="image/avif" srcset="<?= e('/' . ltrim($heroImage, '/') . '-640.avif 640w, /' . ltrim($heroImage, '/') . '-1280.avif 1280w, /' . ltrim($heroImage, '/') . '-1920.avif 1920w') ?>">
  <source type="image/webp" srcset="<?= e('/' . ltrim($heroImage, '/') . '-640.webp 640w, /' . ltrim($heroImage, '/') . '-1280.webp 1280w, /' . ltrim($heroImage, '/') . '-1920.webp 1920w') ?>">
  <img src="/<?= e(ltrim($heroImage, '/') . '-1280.webp') ?>" alt="<?= e($heroAlt) ?>"
       width="1280" height="720" loading="eager" decoding="async">
</picture>
