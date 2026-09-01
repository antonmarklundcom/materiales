<?php
/**
 * cotizar/index.php — página de cotización. El FORMULARIO y su handler (enviar.php) los
 * construye la fase 2 (plan §3): campos, honeypot, trampa de tiempo, consentimiento,
 * POST a VenderCRM y redirect 303 a /gracias/. Acá queda la página con su title/meta reales.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$preselected = (string) ($_GET['m'] ?? '');
$entry = data('categories')[$preselected] ?? data('materials')[$preselected] ?? null;

$breadcrumbs = [['Inicio', '/'], ['Pedir cotización', null]];

page([
    'title'       => 'Pedí cotización de materiales | Materiales.com.py',
    'meta'        => 'Cargá qué material necesitás, cuánto y en qué zona. Hasta ' . (int) site('max_proveedores', 3) . ' proveedores verificados te pasan precio por WhatsApp, normalmente en el día.',
    'canonical'   => '/cotizar/',
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs)],
    'body_class'  => 'page-cotizar',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<h1>Pedí tu cotización</h1>
<?php if ($entry !== null): ?>
<p>Estás pidiendo cotización de <strong><?= e($entry['name']) ?></strong>.</p>
<?php endif; ?>
<p>
  Contanos qué necesitás y hasta <?= (int) site('max_proveedores', 3) ?> proveedores
  verificados te escriben por WhatsApp con su precio. Es gratis y sin compromiso.
</p>
<p class="notice">El formulario se habilita en los próximos días.</p>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
