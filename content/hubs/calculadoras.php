<?php
/**
 * content/hubs/calculadoras.php — intro de /calculadoras/ (improvement report #2, G8). Las
 * dosificaciones vienen de CONTENT-SPEC §12.1; acá no se agrega ninguna cifra nueva.
 */

declare(strict_types=1);
?>
<div class="prose">
<p>
  Cada calculadora resuelve una cuenta que se hace en toda obra: cuántas bolsas de cemento van
  por metro cuadrado de contrapiso o revoque, cuánto cemento, arena y ripio lleva un metro cúbico
  de hormigón, cuántos ladrillos o bloques entran en un metro cuadrado de pared y cuánta cal,
  cemento y arena necesita un revoque. Cargás tus medidas y el resultado se actualiza en el
  momento.
</p>
<p>
  Debajo de cada calculadora está la cuenta escrita en palabras, un ejemplo resuelto y los
  supuestos que usa (espesores, dosificaciones y desperdicio), para que sepas de dónde sale cada
  número. El resultado es una referencia: la cantidad final la confirma tu proveedor o tu maestro
  mayor de obras. Con un clic, esa cantidad pasa al formulario de la misma página y hasta
  <?= (int) site('max_proveedores', 3) ?> proveedores verificados te pasan precio por WhatsApp.
</p>
</div>
