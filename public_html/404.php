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
<h1>No encontramos esa página</h1>
<p>Puede que el enlace esté viejo o mal escrito. Probá desde acá:</p>
<ul class="card-list">
  <li><a href="/materiales/">Todos los materiales</a></li>
  <li><a href="/cotizar/">Pedir cotización</a></li>
  <li><a href="/guias/">Guías de obra</a></li>
  <li><a href="/contacto/">Contacto</a></li>
</ul>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
