<?php
/**
 * como-trabajamos/index.php — metodología (improvement report #2, S16 / E-E-A-T).
 *
 * Todo lo que dice esta página ya está afirmado en otra parte del sitio o del repo: el flujo del
 * pedido (/gracias/, home), la verificación de proveedores (/proveedores/), la política de no
 * publicar precios (CONTENT-SPEC §0), el origen de los números de calculadoras y guías
 * (CONTENT-SPEC §12.1) y el tratamiento de datos (/politica-de-privacidad/). No agrega cifras,
 * plazos, cantidades de proveedores ni nombres que no estén cargados: si Anton suma un revisor
 * técnico con nombre (decisión D4), va acá.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$maxProv     = (int) site('max_proveedores', 3);
$breadcrumbs = [['Inicio', '/'], ['Cómo trabajamos', null]];

page([
    'title'       => 'Cómo trabajamos | Materiales.com.py',
    'meta'        => 'Qué pasa con tu pedido de cotización, cómo verificamos a los proveedores, por qué no publicamos precios y de dónde salen los números de las calculadoras.',
    'canonical'   => '/como-trabajamos/',
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs), schema_organization()],
    'body_class'  => 'page-como-trabajamos',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>Cómo trabajamos</h1>
    <p class="lead">
      Qué pasa con tu pedido, cómo elegimos a quién se lo pasamos y de dónde salen los números
      que publicamos.
    </p>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel">
    <div class="prose">
      <h2>Qué hacemos y qué no</h2>
      <p>
        <?= e((string) site('brand')) ?> junta pedidos de cotización de materiales de construcción
        y se los pasa a proveedores que venden ese rubro. No vendemos materiales, no participamos
        de la venta y no publicamos precios: el precio te lo pasa cada proveedor, directo, y
        cerrás con el que más te sirva. Para quien pide cotización el servicio es gratis.
      </p>

      <h2>Qué pasa con tu pedido</h2>
      <ol>
        <li>Cargás qué material necesitás, qué cantidad, en qué zona y tu WhatsApp. Si no tenés
          claro el material exacto, alcanza con contar qué estás haciendo.</li>
        <li>Le pasamos el pedido a proveedores que trabajan ese rubro y entregan en tu zona, hasta
          <?= $maxProv ?> en total. Nunca a proveedores de rubros que no pediste.</li>
        <li>Te escriben por WhatsApp con su precio, normalmente dentro del día.</li>
        <li>Comparás sobre la misma cantidad y la misma entrega, y cerrás directo con el
          proveedor.</li>
      </ol>

      <h2>Cómo verificamos a los proveedores</h2>
      <p>
        Un proveedor se suma desde <a href="/proveedores/">Para proveedores</a> con el nombre de su
        empresa, los rubros que vende y su zona, y lo llamamos para verificar que la empresa existe
        y que vende lo que dice. Sólo recibe pedidos de los rubros que cargó. El proveedor paga por
        pedido recibido: no se vende publicidad, posiciones destacadas ni visibilidad en el sitio.
      </p>

      <h2>Por qué no publicamos precios</h2>
      <p>
        Porque cambian con la cantidad que lleves, la medida, el flete hasta tu obra y el momento
        en que pidas. Un precio publicado casi nunca es el que te van a cobrar a vos. Por eso el
        pedido va directo a quien vende, y el número te lo pasa él, con esas condiciones ya
        incluidas.
      </p>

      <h2>De dónde salen los números de las guías y las calculadoras</h2>
      <p>
        Las <a href="/calculadoras/">calculadoras</a> usan las dosificaciones estándar en volumen
        de los manuales de obra (cemento : arena : ripio, o cemento : cal : arena) y las convierten
        a bolsas con dos datos que están escritos en cada página: la bolsa de cemento de 50 kg y un
        desperdicio del 10 % para mezclas, declarado como supuesto. Cada calculadora muestra la
        cuenta en palabras, un ejemplo resuelto y sus supuestos, para que cualquiera pueda rehacer
        el número. Es siempre una referencia: la cantidad final la confirma tu proveedor o quien
        dirige la obra.
      </p>
      <p>
        Las <a href="/guias/">guías</a> explican de qué depende cada cuenta y cómo pedir para que
        las cotizaciones sean comparables. No dan precios.
      </p>

      <h2>Tus datos</h2>
      <p>
        Tu pedido sólo se comparte, con tu consentimiento, con hasta <?= $maxProv ?> proveedores del
        rubro que pediste. No lo vendemos ni lo cedemos para ninguna otra finalidad. El detalle está
        en la <a href="/politica-de-privacidad/">política de privacidad</a>.
      </p>
    </div>
    <p class="card card--accent closing-cta">
      <a href="/cotizar/">Pedí tu cotización</a>
    </p>
  </div>
</div>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
