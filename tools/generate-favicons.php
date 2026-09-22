<?php
/**
 * tools/generate-favicons.php — genera los íconos del sitio (improvement report #2, S2).
 *
 * Una sola figura, dibujada acá con GD y repetida a mano en /favicon.svg: una "M" negra
 * (--base #17170f) sobre un cuadrado redondeado amarillo de seguridad (--accent #f2c318), los
 * dos tokens de assets/css/site.css. Sin tipografía (a 16 px una fuente no se lee): la M es
 * un polígono en una grilla de 64 unidades, el mismo que usa favicon.svg.
 *
 * Salida (en la raíz del sitio, donde los buscan navegadores y buscadores):
 *   favicon.ico            16 + 32 + 48 px (entradas PNG dentro del ICO)
 *   favicon-48.png         el que se declara con <link rel="icon" sizes="48x48">
 *   apple-touch-icon.png   180 px, sin transparencia (iOS pone negro detrás)
 *
 * Uso: php tools/generate-favicons.php — utilidad de build, no corre en CI ni en request.
 */

declare(strict_types=1);

$root = dirname(__DIR__);

/** M en grilla 64×64 (idéntica a favicon.svg). */
const FAVICON_M = [12, 50, 12, 14, 22, 14, 32, 30, 42, 14, 52, 14, 52, 50, 43, 50, 43, 29, 35, 42, 29, 42, 21, 29, 21, 50];

function favicon_png(int $size, bool $rounded): string
{
    $scale = 8; // supersampling: se dibuja grande y se reduce con resample = bordes suaves
    $big = $size * $scale;
    $img = imagecreatetruecolor($big, $big);
    imagesavealpha($img, true);
    imagealphablending($img, false);
    imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
    imagealphablending($img, true);

    $yellow = imagecolorallocate($img, 0xf2, 0xc3, 0x18);
    $black  = imagecolorallocate($img, 0x17, 0x17, 0x0f);
    $u = $big / 64;

    if ($rounded) {
        $r = (int) round(12 * $u);
        imagefilledrectangle($img, $r, 0, $big - 1 - $r, $big - 1, $yellow);
        imagefilledrectangle($img, 0, $r, $big - 1, $big - 1 - $r, $yellow);
        foreach ([[$r, $r], [$big - 1 - $r, $r], [$r, $big - 1 - $r], [$big - 1 - $r, $big - 1 - $r]] as [$cx, $cy]) {
            imagefilledellipse($img, $cx, $cy, 2 * $r, 2 * $r, $yellow);
        }
    } else {
        imagefilledrectangle($img, 0, 0, $big - 1, $big - 1, $yellow);
    }

    $points = array_map(static fn (int $p): int => (int) round($p * $u), FAVICON_M);
    imagefilledpolygon($img, $points, $black);

    $out = imagecreatetruecolor($size, $size);
    imagesavealpha($out, true);
    imagealphablending($out, false);
    imagecopyresampled($out, $img, 0, 0, 0, 0, $size, $size, $big, $big);
    ob_start();
    imagepng($out, null, 9);
    return (string) ob_get_clean();
}

// ICO con entradas PNG (soportado por todo navegador desde hace años).
$sizes = [16, 32, 48];
$pngs = array_map(static fn (int $s): string => favicon_png($s, true), $sizes);
$ico = pack('vvv', 0, 1, count($sizes));
$offset = 6 + 16 * count($sizes);
foreach ($sizes as $i => $s) {
    $ico .= pack('CCCCvvVV', $s, $s, 0, 0, 1, 32, strlen($pngs[$i]), $offset);
    $offset += strlen($pngs[$i]);
}
$ico .= implode('', $pngs);

file_put_contents($root . '/favicon.ico', $ico);
file_put_contents($root . '/favicon-48.png', $pngs[2]);
file_put_contents($root . '/apple-touch-icon.png', favicon_png(180, false));
echo "favicon.ico, favicon-48.png, apple-touch-icon.png generados.\n";
