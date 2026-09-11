<?php
/**
 * 404.php — página de error. La sirve ErrorDocument (.htaccess) y también not_found() desde
 * los routers. Enlaza de vuelta a las páginas de dinero.
 */

declare(strict_types=1);

if (!defined('PUBLIC_ROOT')) {
    require __DIR__ . '/partials/init.php';
    require __DIR__ . '/partials/schema.php';
}

http_response_code(404);

page([
    'title'      => 'Página no encontrada | Materiales.com.py',
    'meta'       => 'No encontramos esa página. Mirá los materiales disponibles o pedí tu cotización.',
    'canonical'  => '/404',
    'noindex'    => true,
    'body_class' => 'page-404',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>No encontramos esa página</h1>
    <p class="lead">Puede que el enlace esté viejo o mal escrito. Probá desde acá:</p>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel">
    <ul class="tile-grid">
      <li><a class="tile card--hair" href="/materiales/"><span>Todos los materiales</span><span class="tile__arrow" aria-hidden="true">→</span></a></li>
      <li><a class="tile card--hair" href="/cotizar/"><span>Pedir cotización</span><span class="tile__arrow" aria-hidden="true">→</span></a></li>
      <li><a class="tile card--hair" href="/guias/"><span>Guías de obra</span><span class="tile__arrow" aria-hidden="true">→</span></a></li>
      <li><a class="tile card--hair" href="/contacto/"><span>Contacto</span><span class="tile__arrow" aria-hidden="true">→</span></a></li>
    </ul>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
