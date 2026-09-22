<?php
/**
 * content/hubs/materiales.php — intro de /materiales/ (improvement report #2, S4).
 *
 * El catálogo tiene su propio ángulo ("qué hay y cómo se pide cada cosa"), distinto del de la
 * home ("por qué cotizar acá"). Sin precios, sin marcas, sin cifras que no salgan de los
 * archivos de datos: los conteos los calcula _index.php.
 */

declare(strict_types=1);
?>
<h2>Cómo usar este catálogo</h2>

<p>
  Acá están todos los materiales que podés cotizar, ordenados por rubro: <?= count($materials) ?>
  materiales de obra gruesa y de terminación, del hierro y el cemento a los pisos, las aberturas
  y los sanitarios. Cada rubro tiene su página con lo que conviene saber antes de pedir, y cada
  material la suya: qué es, para qué se usa, cómo se vende en plaza y las preguntas que más se
  repiten.
</p>

<p>
  La unidad de venta cambia mucho de un material a otro, y es lo primero que conviene tener
  claro para que las cotizaciones sean comparables. La arena, el ripio y la piedra se piden por
  metro cúbico o por camionada; el cemento y la cal, por bolsa; los ladrillos, por millar; el
  hierro, por barra y diámetro; los pisos, por metro cuadrado en caja cerrada. Cada página de
  material dice cómo se vende ese material en particular.
</p>

<p>
  Si todavía no sabés cuánto necesitás, empezá por las <a href="/calculadoras/">calculadoras</a>
  o por las <a href="/guias/">guías</a>, y después volvé al material. Cuando lo tengas, el
  formulario de cada página ya viene con ese material elegido: cargás la cantidad y la zona, y
  hasta <?= $maxProv ?> proveedores verificados te escriben por WhatsApp con su precio. No
  publicamos precios porque dependen de la cantidad, la medida, el flete hasta tu obra y el
  momento en que pidas.
</p>
