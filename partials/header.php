<?php
/**
 * partials/header.php — <head> completo + cabecera visible. Espera que la página ya haya
 * llamado a page([...]) y a init.php.
 */

declare(strict_types=1);

$page = page();
$site = site();
$noindex = ($site['staging_noindex'] ?? false) === true || ($page['noindex'] ?? false) === true;
// canonical vacío (fase 14, plan §11.6): la página no declara una URL canónica propia — hoy
// sólo el 404, que no debería apuntar a ninguna URL como si fuera la "real".
$canonical = $page['canonical'] !== '' ? url($page['canonical']) : '';
// og:image por página (fase 11, decisión §1.20): la imagen declarada por la entrada si
// existe en disco, y si no el fallback de paleta sitewide. page()['image'] ya viene validada
// por image_for(), así que acá no hace falta volver a tocar el disco.
$pageImage = trim((string) ($page['image'] ?? ''));
if ($pageImage !== '') {
    $ogImage = url('/' . ltrim($pageImage, '/'));
} else {
    $ogImage = is_file(PUBLIC_ROOT . '/assets/img/og-default.jpg') ? url('/assets/img/og-default.jpg') : '';
}

// og:type=article en las páginas de detalle de guía y calculadora (fase 14, plan §11.6):
// se deduce del body_class ya asignado por esas plantillas, sin agregar una clave nueva a
// page().
$ogType = in_array($page['body_class'], ['page-guia', 'page-calculadora'], true) ? 'article' : 'website';

header('Content-Type: text/html; charset=utf-8');
// Cache-Control de las respuestas PHP: va acá, no en .htaccess (fase 14, plan §11.6) — el
// .htaccess sólo puede fijar expiración por tipo de archivo estático, no por respuesta
// dinámica. no-cache (no "no-store"): el navegador puede guardarla pero siempre revalida.
header('Cache-Control: no-cache');
?>
<!doctype html>
<html lang="<?= e($site['locale'] ?? 'es-PY') ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page['title']) ?></title>
<meta name="description" content="<?= e($page['meta']) ?>">
<?php if ($canonical !== ''): ?>
<link rel="canonical" href="<?= e($canonical) ?>">
<?php endif; ?>
<?php if ($noindex): ?>
<?php /* staging: el flag se apaga en la fase 6 */ ?>
<meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<meta property="og:type" content="<?= e($ogType) ?>">
<meta property="og:locale" content="es_PY">
<meta property="og:site_name" content="<?= e($site['brand']) ?>">
<meta property="og:title" content="<?= e($page['title']) ?>">
<meta property="og:description" content="<?= e($page['meta']) ?>">
<?php if ($canonical !== ''): ?>
<meta property="og:url" content="<?= e($canonical) ?>">
<?php endif; ?>
<?php if ($ogImage !== ''): ?>
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<?php endif; ?>
<link rel="preload" href="/assets/fonts/bricolage-grotesque-latin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/assets/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="/assets/css/site.css">
<script src="/assets/js/consent.js" defer></script>
<?php require PUBLIC_ROOT . '/partials/analytics.php'; ?>
<script src="/assets/js/events.js" defer></script>
<script src="/assets/js/motion.js" defer></script>
<?php schema_render($page['schema']); ?>
</head>
<body class="<?= e($page['body_class']) ?>">
<a class="skip-link" href="#contenido">Ir al contenido</a>
<?php require PUBLIC_ROOT . '/partials/cookie-banner.php'; ?>
<header class="site-header band--dark grain" data-sticky-header>
  <div class="wrap site-header__row">
    <a class="site-header__brand" href="/"><?= e($site['brand']) ?></a>
    <button type="button" class="nav-toggle" data-nav-toggle aria-expanded="false" aria-controls="site-nav">
      <span class="nav-toggle__box" aria-hidden="true"><span class="nav-toggle__bar"></span></span>
      <span class="sr-only">Abrir menú</span>
    </button>
    <nav class="site-nav" id="site-nav" aria-label="Principal">
      <a href="/materiales/">Materiales</a>
      <a href="/guias/">Guías</a>
      <a href="/calculadoras/">Calculadoras</a>
      <a href="/cotizar/">Cotizar</a>
      <a href="/proveedores/">Para proveedores</a>
      <a href="/contacto/">Contacto</a>
    </nav>
  </div>
</header>
<?php if ($page['breadcrumbs'] !== []): ?>
<nav class="breadcrumbs wrap" aria-label="Miga de pan">
  <ol>
    <?php foreach ($page['breadcrumbs'] as [$crumbName, $crumbPath]): ?>
    <li><?php if ($crumbPath !== null): ?><a href="<?= e($crumbPath) ?>"><?= e($crumbName) ?></a><?php else: ?><span aria-current="page"><?= e($crumbName) ?></span><?php endif; ?></li>
    <?php endforeach; ?>
  </ol>
</nav>
<?php endif; ?>
<main id="contenido" tabindex="-1">
