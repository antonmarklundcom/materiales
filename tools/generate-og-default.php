<?php
/**
 * tools/generate-og-default.php — genera assets/img/og-default.jpg.
 *
 * Fallback "motivo de paleta" para og:image cuando no hay fotografía real (fase 8,
 * KNOWN-ISSUES: descarga de CDN de Higgsfield bloqueada en este entorno — 403 en
 * *.cloudfront.net). Sin rostros, sin fotos falsas de "nuestro trabajo": sólo los
 * tokens de marca (track CORRALÓN: papel, tinta negra, amarillo de seguridad) que ya
 * usa el sitio.
 *
 * Uso: php tools/generate-og-default.php
 * No se ejecuta en request time ni en CI — es una utilidad de build, corrida una vez.
 */

declare(strict_types=1);

$width  = 1200;
$height = 630;

$img = imagecreatetruecolor($width, $height);

$paper  = imagecolorallocate($img, 0xf4, 0xf0, 0xe6);
$ink    = imagecolorallocate($img, 0x17, 0x17, 0x0f);
$inkDim = imagecolorallocate($img, 0x5a, 0x56, 0x47);
$accent = imagecolorallocate($img, 0xf2, 0xc3, 0x18);

imagefilledrectangle($img, 0, 0, $width, $height, $paper);

// Franja de peligro (amarillo/negro) arriba y abajo, como .hazard-bar en el CSS.
imagefilledrectangle($img, 0, 0, $width, 18, $accent);
imagefilledrectangle($img, 0, 18, $width, 20, $ink);
imagefilledrectangle($img, 0, $height - 20, $width, $height - 18, $ink);
imagefilledrectangle($img, 0, $height - 18, $width, $height, $accent);

// Borde de tinta alrededor del cartel, como los bordes sólidos de las tarjetas del sitio.
imagerectangle($img, 30, 30, $width - 30, $height - 30, $ink);

$fontBold = '/mnt/skills/examples/canvas-design/canvas-fonts/WorkSans-Bold.ttf';
$fontMono = '/mnt/skills/examples/canvas-design/canvas-fonts/IBMPlexMono-Regular.ttf';

if (is_file($fontBold) && is_file($fontMono)) {
    // Wordmark chico arriba, en mono (como el eyebrow del sitio).
    imagettftext($img, 20, 0, 80, 100, $inkDim, $fontMono, 'MATERIALES.COM.PY');

    // Título en dos líneas, tamaño grande.
    imagettftext($img, 62, 0, 78, 270, $ink, $fontBold, 'Materiales de');
    imagettftext($img, 62, 0, 78, 350, $ink, $fontBold, 'construcción');

    // Bajada / CTA, sobre un bloque amarillo (como el panel de cotización del sitio).
    imagefilledrectangle($img, 68, 400, 680, 470, $accent);
    imagettftext($img, 26, 0, 84, 445, $ink, $fontBold, 'Cotizá gratis');
    imagettftext($img, 18, 0, 84, 500, $inkDim, $fontMono, 'Hasta 3 proveedores verificados te escriben por WhatsApp');
} else {
    fwrite(STDERR, "Aviso: no se encontraron las fuentes, se usa la fuente interna de GD.\n");
    imagestring($img, 5, 80, 260, 'Materiales de construccion', $ink);
    imagestring($img, 5, 80, 300, 'Cotiza gratis', $ink);
}

$outDir = __DIR__ . '/../assets/img';
if (!is_dir($outDir)) {
    mkdir($outDir, 0775, true);
}
$outPath = $outDir . '/og-default.jpg';
imagejpeg($img, $outPath, 88);
imagedestroy($img);

echo "OK: {$outPath} (" . filesize($outPath) . " bytes)\n";
