<?php
/**
 * tools/router-cli.php — router para el servidor embebido de PHP. SÓLO desarrollo y CI.
 *
 *   php -S 127.0.0.1:8080 -t . tools/router-cli.php
 *
 * Replica a mano lo que en producción hace .htaccess (docroot = repo root, ver DEPLOY.md) (Apache/LiteSpeed en
 * Hostinger). Si cambiás una regla de reescritura, cambiala en LOS DOS lugares.
 */

declare(strict_types=1);

$publicRoot = dirname(__DIR__);
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Nunca servir includes internos ni partials directamente (espejo del [F] del .htaccess).
if (preg_match('#^/(partials/|materiales/_index\.php$)#', $path)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

// Archivos estáticos existentes: los sirve el servidor embebido tal cual.
$candidate = $publicRoot . $path;
if ($path !== '/' && is_file($candidate) && !str_ends_with($path, '.php')) {
    return false;
}

$serve = static function (string $script, array $query = []) use ($publicRoot): bool {
    $_GET = $query + $_GET;
    require $publicRoot . '/' . ltrim($script, '/');
    return true;
};

if ($path === '/sitemap.xml') {
    return $serve('sitemap.php');
}

// Scripts propios que se sirven por su ruta real (el handler del formulario). En producción
// no hay regla de reescritura para ellos: Apache los sirve directamente.
if (str_ends_with($path, '.php') && is_file($publicRoot . $path)) {
    return $serve(ltrim($path, '/'));
}

// Canonicalización a barra final, igual que el 301 del .htaccess.
if (preg_match('#^/(materiales|guias)/([a-z0-9-]+)$#', $path, $matches)) {
    header('Location: ' . $path . '/', true, 301);
    return true;
}

if (preg_match('#^/(materiales|guias)/([a-z0-9-]+)/$#', $path, $matches)) {
    return $serve($matches[1] . '/index.php', ['slug' => $matches[2]]);
}

if ($path === '/materiales/' || $path === '/guias/') {
    return $serve(trim($path, '/') . '/index.php');
}

if ($path === '/') {
    return $serve('index.php');
}

if (preg_match('#^/([a-z0-9-]+)/$#', $path, $matches) && is_file($publicRoot . '/' . $matches[1] . '/index.php')) {
    return $serve($matches[1] . '/index.php');
}

http_response_code(404);
require $publicRoot . '/404.php';
return true;
