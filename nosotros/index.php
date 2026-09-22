<?php
/**
 * nosotros/index.php — quiénes somos (improvement report #2, S16 / E-E-A-T).
 *
 * Sólo datos que ya existen en data/site.php y en los archivos de datos. Razón social, RUC,
 * dirección, horarios y una persona con nombre (decisión D4) todavía no están cargados: los
 * bloques de abajo aparecen solos cuando Anton los complete en data/site.php. Nada se inventa.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$site        = site();
$address     = $site['address'] ?? [];
$materials   = array_filter(data('materials'), 'is_published');
$categories  = array_filter(data('categories'), 'is_published');
$guides      = array_filter(data('guides'), 'is_published');
$calculators = array_filter(data('calculators'), 'is_published');
$breadcrumbs = [['Inicio', '/'], ['Nosotros', null]];
$waUrl       = wa_url();

page([
    'title'       => 'Sobre Materiales.com.py | Cotizá materiales en Paraguay',
    'meta'        => 'Qué es Materiales.com.py, a quién le sirve y cómo contactarnos: cotización de materiales de construcción con proveedores verificados de Gran Asunción.',
    'canonical'   => '/nosotros/',
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs), schema_organization()],
    'body_class'  => 'page-nosotros',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>Sobre <?= e($site['brand']) ?></h1>
    <p class="lead"><?= e((string) ($site['tagline'] ?? '')) ?></p>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel">
    <div class="prose">
      <h2>Qué es</h2>
      <p>
        <?= e($site['brand']) ?> es un servicio de cotización de materiales de construcción para
        <?= e(implode(', ', array_slice($site['area_served'] ?? [], 0, 6))) ?> y el resto de Gran
        Asunción<?= ($address['locality'] ?? '') !== '' ? ', con base en ' . e($address['locality']) . ', Paraguay' : '' ?>.
        Cargás un pedido una sola vez y hasta <?= (int) site('max_proveedores', 3) ?> proveedores
        verificados del rubro te pasan su precio por WhatsApp.
      </p>

      <h2>Por qué existe</h2>
      <p>
        Pedir precio de materiales todavía funciona a fuerza de llamadas: un corralón, otro, un
        tercero, cada uno contesta cuando puede y rara vez sobre la misma cantidad y la misma
        entrega. Acá el pedido sale una vez, con los datos que un proveedor necesita para cotizar,
        y las respuestas se pueden comparar.
      </p>

      <h2>Qué vas a encontrar</h2>
      <ul>
        <li><a href="/materiales/"><?= count($materials) ?> materiales en <?= count($categories) ?> rubros</a>, cada uno con cómo se vende y las preguntas que más se repiten.</li>
        <li><a href="/guias/"><?= count($guides) ?> guías</a> para elegir y calcular antes de comprar.</li>
        <li><a href="/calculadoras/"><?= count($calculators) ?> calculadoras</a> con la cuenta explicada y los supuestos a la vista.</li>
        <li><a href="/proveedores/">Un canal para proveedores</a> que quieren recibir pedidos de su rubro.</li>
      </ul>
      <p>
        Cómo se procesa cada pedido, cómo verificamos a los proveedores y de dónde salen los
        números de las calculadoras está en <a href="/como-trabajamos/">Cómo trabajamos</a>.
      </p>

      <?php if (($site['legal_name'] ?? '') !== '' || ($site['ruc'] ?? '') !== '' || ($address['street'] ?? '') !== '' || ($site['horarios'] ?? '') !== ''): ?>
      <h2>Datos de la empresa</h2>
      <ul>
        <?php if (($site['legal_name'] ?? '') !== ''): ?><li>Razón social: <?= e($site['legal_name']) ?></li><?php endif; ?>
        <?php if (($site['ruc'] ?? '') !== ''): ?><li>RUC: <?= e($site['ruc']) ?></li><?php endif; ?>
        <?php if (($address['street'] ?? '') !== ''): ?><li>Dirección: <?= e($address['street']) ?>, <?= e((string) ($address['locality'] ?? '')) ?></li><?php endif; ?>
        <?php if (($site['horarios'] ?? '') !== ''): ?><li>Horario de atención: <?= e($site['horarios']) ?></li><?php endif; ?>
      </ul>
      <?php endif; ?>

      <h2>Contacto</h2>
      <p>
        <?php if ($waUrl !== ''): ?>Escribinos por <a href="<?= e($waUrl) ?>" data-ev="whatsapp_click" data-ev-loc="nosotros">WhatsApp</a> o mirá<?php else: ?>Mirá<?php endif; ?>
        todos los canales en <a href="/contacto/">Contacto</a>. Para precios,
        <a href="/cotizar/">pedí tu cotización</a>: así te responden directamente los proveedores.
      </p>
    </div>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
