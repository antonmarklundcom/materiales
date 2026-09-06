<?php
/**
 * tools/generate-og-default.php — genera public_html/assets/img/og-default.jpg.
 *
 * Fallback "motivo de paleta" para og:image cuando no hay fotografía real (fase 8,
 * KNOWN-ISSUES: descarga de CDN de Higgsfield bloqueada en este entorno — 403 en
 * *.cloudfront.net). Sin rostros, sin fotos falsas de "nuestro trabajo": sólo los
 * tokens de marca (fondo oscuro, acento, tipografía) que ya usa el sitio.
 *
 * Uso: php tools/generate-og-default.php
 * No se ejecuta en request time ni en CI — es una utilidad de build, corrida una vez.
 */

declare(strict_types=1);

$width  = 1200;
$height = 630;

$img = imagecreatetruecolor($width, $height);

$base   = imagecolorallocate($img, 0x0e, 0x0e, 0x0f);
$ink    = imagecolorallocate($img, 0xf5, 0xf3, 0xf0);
$inkDim = imagecolorallocate($img, 0xb8, 0xb5, 0xb0);
$accent = imagecolorallocate($img, 0xe8, 0x56, 0x2a);

imagefilledrectangle($img, 0, 0, $width, $height, $base);

// Grano sutil (igual espíritu que .grain en el CSS): puntos oscuros/claros aleatorios
// de baja opacidad simulada por color-mix manual con el fondo.
mt_srand(20260906);
for ($i = 0; $i < 9000; $i++) {
    $x = mt_rand(0, $width - 1);
    $y = mt_rand(0, $height - 1);
    $delta = mt_rand(-10, 10);
    $c = imagecolorallocate(
        $img,
        max(0, min(255, 0x0e + $delta)),
        max(0, min(255, 0x0e + $delta)),
        max(0, min(255, 0x0f + $delta))
    );
    imagesetpixel($img, $x, $y, $c);
}

// Franja de acento a la izquierda, como el borde de acento de las tarjetas del sitio.
imagefilledrectangle($img, 0, 0, 14, $height, $accent);

// Bloque geométrico de acento en la esquina inferior derecha (motivo, no foto).
imagefilledpolygon($img, [
    $width, $height,
    $width - 340, $height,
    $width, $height - 340,
], $accent);
// Oscurece el bloque para que el texto de abajo no compita si algún día se agrega.
$overlay = imagecolorallocatealpha($img, 0x0e, 0x0e, 0x0f, 70);
imagefilledpolygon($img, [
    $width, $height,
    $width - 340, $height,
    $width, $height - 340,
], $overlay);

$fontBold = '/mnt/skills/examples/canvas-design/canvas-fonts/BricolageGrotesque-Bold.ttf';
$fontReg  = '/mnt/skills/examples/canvas-design/canvas-fonts/BricolageGrotesque-Regular.ttf';

if (is_file($fontBold) && is_file($fontReg)) {
    // Wordmark chico arriba.
    imagettftext($img, 22, 0, 80, 90, $inkDim, $fontReg, 'materiales.com.py');

    // Título en dos líneas, tamaño grande.
    imagettftext($img, 64, 0, 78, 260, $ink, $fontBold, 'Materiales de');
    imagettftext($img, 64, 0, 78, 340, $ink, $fontBold, 'construcción');

    // Bajada / CTA.
    imagettftext($img, 30, 0, 80, 420, $accent, $fontBold, 'Cotizá gratis');
    imagettftext($img, 22, 0, 80, 470, $inkDim, $fontReg, 'Hasta 3 proveedores verificados te escriben por WhatsApp');
} else {
    fwrite(STDERR, "Aviso: no se encontró la fuente Bricolage Grotesque, se usa la fuente interna de GD.\n");
    imagestring($img, 5, 80, 260, 'Materiales de construccion', $ink);
    imagestring($img, 5, 80, 300, 'Cotiza gratis', $accent);
}

$outDir = __DIR__ . '/../public_html/assets/img';
if (!is_dir($outDir)) {
    mkdir($outDir, 0775, true);
}
$outPath = $outDir . '/og-default.jpg';
imagejpeg($img, $outPath, 88);
imagedestroy($img);

echo "OK: {$outPath} (" . filesize($outPath) . " bytes)\n";
