<?php
/**
 * partials/header.php — <head> completo + cabecera visible. Espera que la página ya haya
 * llamado a page([...]) y a init.php.
 */

declare(strict_types=1);

$page = page();
$site = site();
$noindex = ($site['staging_noindex'] ?? false) === true || ($page['noindex'] ?? false) === true;
$canonical = url($page['canonical']);
// og:image por página (fase 11, decisión §1.20): la imagen declarada por la entrada si
// existe en disco, y si no el fallback de paleta sitewide. page()['image'] ya viene validada
// por image_for(), así que acá no hace falta volver a tocar el disco.
$pageImage = trim((string) ($page['image'] ?? ''));
if ($pageImage !== '') {
    $ogImage = url('/' . ltrim($pageImage, '/'));
} else {
    $ogImage = is_file(PUBLIC_ROOT . '/assets/img/og-default.jpg') ? url('/assets/img/og-default.jpg') : '';
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="<?= e($site['locale'] ?? 'es-PY') ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page['title']) ?></title>
<meta name="description" content="<?= e($page['meta']) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<?php if ($noindex): ?>
<?php /* staging: el flag se apaga en la fase 6 */ ?>
<meta name="robots" content="noindex, nofollow">
<?php endif; ?>
<meta property="og:type" content="website">
<meta property="og:locale" content="es_PY">
<meta property="og:site_name" content="<?= e($site['brand']) ?>">
<meta property="og:title" content="<?= e($page['title']) ?>">
<meta property="og:description" content="<?= e($page['meta']) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<?php if ($ogImage !== ''): ?>
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600&family=Inter:wght@400;500;600&display=swap">
<link rel="stylesheet" href="/assets/css/site.css">
<script src="/assets/js/consent.js" defer></script>
<?php require PUBLIC_ROOT . '/partials/analytics.php'; ?>
<script src="/assets/js/events.js" defer></script>
<script src="/assets/js/motion.js" defer></script>
<?php schema_render($page['schema']); ?>
</head>
<body class="<?= e($page['body_class']) ?>">
<a class="skip-link" href="#contenido">Ir al contenido</a>
<header class="site-header band--dark grain" data-sticky-header>
  <div class="wrap site-header__row">
    <a class="site-header__brand" href="/"><?= e($site['brand']) ?></a>
    <nav class="site-nav" aria-label="Principal">
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
<main id="contenido">
