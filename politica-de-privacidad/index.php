<?php
/**
 * politica-de-privacidad/index.php — política de privacidad.
 *
 * El texto de abajo refleja las decisiones YA tomadas en el plan (§3, §6, §8.6): qué datos se
 * piden, para qué, con quién se comparten (hasta N proveedores del rubro), VenderCRM como
 * encargado de tratamiento, las cookies por nombre (vc_attr, GA4, Meta Pixel) y los derechos
 * del titular bajo la Ley 7593 de protección de datos personales y la Ley 6534.
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
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>Política de privacidad</h1>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel prose">

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

<h2>Cookies y tecnologías de medición</h2>
<p>
  Usamos cookies necesarias para que el sitio funcione. Las de estadísticas y las de marketing
  sólo se activan si las aceptás en el banner: hasta que las aceptes no descargamos ni
  ejecutamos ninguno de esos scripts. Podés cambiar tu elección en cualquier momento borrando
  los datos del sitio en tu navegador.
</p>
<ul>
  <li>
    <strong>Necesarias.</strong> La cookie <code>vc_attr</code>, de nuestro propio dominio,
    guarda hasta 90 días por qué canal llegaste la primera vez (campaña, buscador o enlace).
    La adjuntamos al pedido de cotización que vos enviás, para saber qué canales traen
    pedidos reales. No contiene tu nombre ni tu teléfono y no se comparte con los proveedores.
    Tu elección de cookies se guarda en el almacenamiento local de tu navegador, no en un
    servidor nuestro.
  </li>
  <li>
    <strong>Estadísticas.</strong> Google Analytics 4 (Google LLC), para medir cuántas
    personas visitan cada página y cuántas piden cotización. Se carga sólo si aceptás
    “Estadísticas”, y con la IP anonimizada.
  </li>
  <li>
    <strong>Marketing.</strong> Meta Pixel (Meta Platforms, Inc.), para medir el resultado de
    la publicidad. Se carga sólo si aceptás “Marketing”.
  </li>
</ul>
<p>
  Google y Meta procesan esos datos en sus propios servidores, fuera del Paraguay, como
  responsables independientes de su tratamiento.
</p>

<h2>Tus derechos (Ley 7593)</h2>
<p>
  La Ley N.º 7593 de Protección de Datos Personales del Paraguay te reconoce el derecho a acceder a
  tus datos, a corregirlos o actualizarlos, a pedir su supresión, a oponerte a que los
  sigamos compartiendo con proveedores y a retirar tu consentimiento en cualquier momento.
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
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
