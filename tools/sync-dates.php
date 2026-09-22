<?php
/**
 * tools/sync-dates.php — S16: escribe 'published' y 'updated' (YYYY-MM-DD) en cada entrada de
 * data/{categories,materials,guides,calculators}.php A PARTIR DE LA HISTORIA DE GIT de su
 * archivo de contenido (content/{tipo}/{slug}.php). Nunca se tipean a mano ni se inventan:
 *   published = fecha del primer commit que creó el archivo de contenido;
 *   updated   = fecha del último commit que lo tocó.
 * Una entrada sin archivo de contenido (rubro 'proxima') queda sin fechas.
 *
 * Correrlo después de mergear cambios de contenido (necesita la historia completa, no un
 * clone superficial):
 *   php tools/sync-dates.php           # reescribe los archivos de datos
 *   php tools/sync-dates.php --check   # sólo compara; exit 1 si hay diferencias
 *
 * De 'updated' salen el <lastmod> del sitemap, el "Actualizado" visible y el dateModified del
 * JSON-LD Article de guías y calculadoras.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

$root  = dirname(__DIR__);
$check = in_array('--check', $argv, true);
$map   = ['categories' => 'categorias', 'materials' => 'materiales', 'guides' => 'guias', 'calculators' => 'calculadoras'];

$shallow = trim((string) shell_exec('git -C ' . escapeshellarg($root) . ' rev-parse --is-shallow-repository 2>/dev/null'));
if ($shallow === 'true') {
    fwrite(STDERR, "Clone superficial: las fechas de git no son confiables. Corré `git fetch --unshallow` antes.\n");
    exit(2);
}

/** [primera, última] fecha de commit de un archivo, o null si git no lo conoce. */
function git_dates(string $root, string $relative): ?array
{
    $out = (string) shell_exec(sprintf(
        'git -C %s log --follow --format=%%as -- %s 2>/dev/null',
        escapeshellarg($root),
        escapeshellarg($relative)
    ));
    $dates = array_values(array_filter(array_map('trim', explode("\n", $out))));
    return $dates === [] ? null : [end($dates), $dates[0]];
}

$diffs = 0;
foreach ($map as $dataName => $contentDir) {
    $file = $root . '/data/' . $dataName . '.php';
    $source = (string) file_get_contents($file);
    $entries = require $file;
    foreach ($entries as $slug => $entry) {
        $relative = 'content/' . $contentDir . '/' . $slug . '.php';
        $dates = is_file($root . '/' . $relative) ? git_dates($root, $relative) : null;

        // Bloque de la entrada: desde "    'slug' => [" hasta la próxima entrada de primer nivel.
        if (preg_match("/\n    '" . preg_quote((string) $slug, '/') . "'\s*=> \[/", $source, $m, PREG_OFFSET_CAPTURE) !== 1) {
            fwrite(STDERR, "No encontré la entrada '{$slug}' en data/{$dataName}.php\n");
            exit(1);
        }
        $start = $m[0][1];
        $next  = preg_match("/\n    '[a-z0-9-]+'\s*=> \[/", $source, $n, PREG_OFFSET_CAPTURE, $start + strlen($m[0][0]));
        $end   = $next === 1 ? $n[0][1] : strlen($source);
        $block = substr($source, $start, $end - $start);

        $new = preg_replace("/\n\s*'(published|updated)'\s*=> '[^']*',/", '', $block);
        if ($dates !== null) {
            $new = preg_replace_callback(
                "/(\n(\s*)'status'\s*=> '[^']*',)/",
                static fn (array $s): string => $s[1]
                    . "\n" . $s[2] . "'published' => '" . $dates[0] . "',"
                    . "\n" . $s[2] . "'updated'   => '" . $dates[1] . "',",
                (string) $new,
                1
            );
        }
        if ($new !== $block) {
            $diffs++;
            $source = substr($source, 0, $start) . $new . substr($source, $end);
            if ($check) {
                fwrite(STDOUT, "desactualizado: data/{$dataName}.php[{$slug}]\n");
            }
        }
    }
    if (!$check) {
        file_put_contents($file, $source);
    }
}

fwrite(STDOUT, $check
    ? ($diffs === 0 ? "sync-dates: todo al día.\n" : "sync-dates: {$diffs} entradas desactualizadas.\n")
    : "sync-dates: {$diffs} entradas actualizadas.\n");
exit($check && $diffs > 0 ? 1 : 0);
