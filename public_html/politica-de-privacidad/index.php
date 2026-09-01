<?php
/**
 * politica-de-privacidad/index.php — política de privacidad.
 *
 * El texto de abajo refleja las decisiones YA tomadas en el plan (§3, §6, §8.6): qué datos se
 * piden, para qué, con quién se comparten (hasta N proveedores del rubro), VenderCRM como
 * encargado de tratamiento, y los derechos del titular (Ley 6534 / 7593).
 * Falta ÚNICAMENTE la identificación del responsable (razón social, RUC, domicilio, contacto):
 * es input humano de la fase 3 y se renderiza desde data/site.php cuando exista. Revisión
 * legal antes del go-live (fase 6).
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$site        = site();
$maxProv     = (int) site('max_proveedores', 3);
$breadcrumbs = [['Inicio', '/'], ['Política de privacidad', null]];

page([
    'title'       => 'Política de privacidad | Materiales.com.py',
    'meta'        => 'Qué datos pedimos, para qué los usamos, con qué proveedores los compartimos y cómo pedir su baja o corrección.',
    'canonical'   => '/politica-de-privacidad/',
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs)],
    'body_class'  => 'page-legal',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<h1>Política de privacidad</h1>

<h2>Quién trata tus datos</h2>
<?php if (($site['legal_name'] ?? '') !== ''): ?>
<p>
  <?= e($site['legal_name']) ?><?= ($site['ruc'] ?? '') !== '' ? ', RUC ' . e($site['ruc']) : '' ?>,
  responsable del tratamiento de los datos que cargás en este sitio.
</p>
<?php else: ?>
<p>El responsable del tratamiento es el titular de <?= e($site['brand']) ?>.</p>
<?php endif; ?>

<h2>Qué datos pedimos y para qué</h2>
<p>
  En el formulario de cotización pedimos tu nombre, tu teléfono, el material y la cantidad que
  necesitás, tu ciudad o zona y, si querés, un mensaje. Los usamos con una sola finalidad:
  conseguir que proveedores del rubro te pasen su cotización.
</p>

<h2>Con quién los compartimos</h2>
<p>
  Si marcás la casilla de consentimiento, compartimos tus datos con hasta <?= $maxProv ?>
  proveedores del rubro que pediste, para que te contacten con su precio. No los vendemos ni
  los cedemos para ninguna otra finalidad, y no los compartimos con proveedores de rubros que
  no pediste.
</p>
<p>
  Usamos VenderCRM como sistema de gestión de contactos. Actúa como encargado de tratamiento:
  procesa los datos por cuenta nuestra y siguiendo nuestras instrucciones.
</p>

<h2>Consentimiento</h2>
<p>
  La casilla viene desmarcada y el envío del formulario requiere marcarla. Guardamos la
  versión del texto de consentimiento y la fecha y hora en que lo diste, como constancia.
  Podés retirar tu consentimiento cuando quieras.
</p>

<h2>Cookies</h2>
<p>
  Usamos cookies necesarias para que el sitio funcione. Las cookies de estadísticas y de
  marketing sólo se activan si las aceptás en el banner; podés cambiar tu elección en
  cualquier momento borrando los datos del sitio en tu navegador.
</p>

<h2>Tus derechos</h2>
<p>
  Podés pedir acceso, corrección, actualización o supresión de tus datos, y oponerte a que los
  sigamos compartiendo con proveedores.
  <?php if (($site['email'] ?? '') !== ''): ?>
  Escribinos a <a href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a>.
  <?php else: ?>
  Escribinos por los canales publicados en <a href="/contacto/">Contacto</a>.
  <?php endif; ?>
</p>

<h2>Conservación</h2>
<p>
  Conservamos los datos de cada pedido de cotización mientras sean útiles para la gestión
  comercial que los originó, y los eliminamos cuando nos lo pedís.
</p>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
