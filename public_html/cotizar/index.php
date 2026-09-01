<?php
/**
 * cotizar/index.php — página de cotización: el formulario completo (partials/form.php),
 * sin material preseleccionado salvo que se llegue con ?m={slug} desde una página de
 * material. El envío lo procesa cotizar/enviar.php (plan §3).
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';
require PUBLIC_ROOT . '/partials/lead.php';

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
<?php
$formSlug   = $preselected;
$formOrigen = '/cotizar/';
$formTitle  = 'Contanos qué necesitás';
require PUBLIC_ROOT . '/partials/form.php';
?>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
