<?php
/**
 * tools/generate-og-images.php — genera un JPG 1200×630 para og:image por cada foto de héroe
 * (assets/img/hero-*-1920.webp → assets/img/hero-*-og.jpg).
 *
 * Por qué JPG y no el WebP del héroe: el og:image es lo que muestra WhatsApp/Facebook al
 * compartir un enlace — el canal principal en Paraguay — y el JPG 1200×630 (1.91:1) es el
 * formato que todas las vistas previas aceptan sin recortes raros.
 *
 * Uso: php tools/generate-og-images.php   (utilidad de build: se corre al sumar una foto
 * nueva, nunca en request time ni en CI). Idempotente: regenera todos.
 */

declare(strict_types=1);

$dir = dirname(__DIR__) . '/assets/img';
$targetW = 1200;
$targetH = 630;

foreach (glob($dir . '/hero-*-1920.webp') ?: [] as $source) {
    $src = @imagecreatefromwebp($source);
    if ($src === false) {
        fwrite(STDERR, "no se pudo leer {$source}\n");
        exit(1);
    }
    $srcW = imagesx($src);
    $srcH = imagesy($src);

    // Recorte centrado a 1.91:1 y escalado a 1200×630.
    $cropH = (int) round($srcW * $targetH / $targetW);
    $cropW = $srcW;
    if ($cropH > $srcH) {
        $cropH = $srcH;
        $cropW = (int) round($srcH * $targetW / $targetH);
    }
    $srcX = (int) (($srcW - $cropW) / 2);
    $srcY = (int) (($srcH - $cropH) / 2);

    $dst = imagecreatetruecolor($targetW, $targetH);
    imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $targetW, $targetH, $cropW, $cropH);

    $out = substr($source, 0, -strlen('-1920.webp')) . '-og.jpg';
    imageinterlace($dst, true);
    imagejpeg($dst, $out, 82);
    imagedestroy($dst);
    imagedestroy($src);
    echo basename($out), ' ', (int) (filesize($out) / 1024), " KB\n";
}
