<?php
/**
 * tools/check-rewrites.php — R3: las reglas de bloqueo viven duplicadas en .htaccess
 * (producción) y tools/router-cli.php (dev/CI), y CI sólo ejercita la segunda. Este check
 * compara las listas que tienen que ser idénticas en los dos archivos:
 *
 *   - carpetas internas bloqueadas con [F] (data|content|config|…);
 *   - extensiones de respaldo/log bloqueadas en cualquier ruta (R4), también contra el
 *     <FilesMatch> de .htaccess;
 *   - prefijos con canonicalización a barra final (materiales|guias|calculadoras).
 *
 * Uso: php tools/check-rewrites.php   (exit 1 si difieren). Corre en CI.
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$htaccess = (string) file_get_contents($root . '/.htaccess');
$router   = (string) file_get_contents($root . '/tools/router-cli.php');
$errors   = [];

/** Primer grupo de alternativas que matchea $pattern, como lista ordenada. */
$list = static function (string $pattern, string $haystack, string $what) use (&$errors): array {
    if (preg_match($pattern, $haystack, $m) !== 1) {
        $errors[] = "no se encontró {$what}";
        return [];
    }
    $items = explode('|', $m[1]);
    sort($items);
    return $items;
};

$pairs = [
    'carpetas internas [F]' => [
        $list('#RewriteRule \^\(([a-z|]+)\)\(/\|\$\) - \[NC,F,L\]#', $htaccess, 'la regla de carpetas en .htaccess'),
        $list('#\^/\(([a-z|]+)\)\(/\|\$\)\#i#', $router, 'la regla de carpetas en router-cli.php'),
    ],
    'extensiones de respaldo (RewriteRule vs router)' => [
        $list('#RewriteRule \(\\\\\.\(([a-z|]+)\)\|~\)\$#', $htaccess, 'la regla de respaldos en .htaccess'),
        $list('#\(\\\\\.\(([a-z|]+)\)\|~\)\$\#i#', $router, 'la regla de respaldos en router-cli.php'),
    ],
    'extensiones de respaldo (RewriteRule vs FilesMatch)' => [
        $list('#RewriteRule \(\\\\\.\(([a-z|]+)\)\|~\)\$#', $htaccess, 'la regla de respaldos en .htaccess'),
        $list('#<FilesMatch "\(\?i\)\(\\\\\.\(([a-z|]+)\)\|~\)\$">#', $htaccess, 'el FilesMatch de respaldos en .htaccess'),
    ],
    'canonicalización a barra final' => [
        $list('#REQUEST_URI\} \^/\(([a-z|]+)\)/#', $htaccess, 'la regla de barra final en .htaccess'),
        $list('#\^/\(([a-z|]+)\)/\(\[a-z0-9-\]\+\)\$\##', $router, 'la regla de barra final en router-cli.php'),
    ],
];

foreach ($pairs as $what => [$a, $b]) {
    if ($a !== $b) {
        $errors[] = sprintf('%s difiere: .htaccess [%s] vs [%s]', $what, implode('|', $a), implode('|', $b));
    }
}

if ($errors !== []) {
    fwrite(STDERR, "REWRITES DIFIEREN:\n  " . implode("\n  ", $errors) . "\n");
    exit(1);
}
echo "REWRITES OK — .htaccess y tools/router-cli.php bloquean lo mismo\n";
