<?php
/**
 * content/calculadoras/ladrillos-por-m2.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * Medidas de cara y junta de mortero según el addendum de §12.1 ("Conteo de piezas de
 * mampostería, fase 13"): 22×11 / 25×12 / 28×14 cm para ladrillo común y 39×19 cm para bloque
 * de hormigón, con la junta configurable sumada a ambos lados y un 5 % de piezas extra por
 * roturas y cortes. Nada de esto se inventa acá.
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
  "tables": {
    "largo_cm": { "comun_22": 22, "comun_25": 25, "comun_28": 28, "bloque": 39 },
    "alto_cm":  { "comun_22": 11, "comun_25": 12, "comun_28": 14, "bloque": 19 }
  },
  "outputs": [
    {
      "id": "piezas",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "mul", "args": [{ "var": "m2" }, { "const": 1.05 }] },
            { "op": "mul", "args": [
              { "op": "div", "args": [
                { "op": "add", "args": [{ "table": "largo_cm", "key": { "var": "tipo" } }, { "var": "junta" }] },
                { "const": 100 }
              ]},
              { "op": "div", "args": [
                { "op": "add", "args": [{ "table": "alto_cm", "key": { "var": "tipo" } }, { "var": "junta" }] },
                { "const": 100 }
              ]}
            ]}
          ]
        }]
      }
    }
  ]
}
</script>

<h2>Qué mueve el número</h2>
<p>
  "Cuántos ladrillos o bloques por m²" depende sobre todo de la <strong>medida de cara</strong>
  de la pieza y de la <strong>junta de mortero</strong> que dejás entre una y otra. Un
  <a href="/materiales/ladrillo-comun/">ladrillo común</a> de 22 × 11 cm entra muchas más veces
  en un metro cuadrado que un <a href="/materiales/bloque-de-hormigon/">bloque de hormigón</a>
  de 39 × 19 cm, así que cambiar la pieza cambia el resultado en un factor de varias veces, no
  en un ajuste chico. La junta suma en el mismo sentido: cuanto más gruesa, más superficie
  ocupa el mortero y menos piezas entran, aunque la pared mida lo mismo.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  Primero se le suma la junta al largo y al alto de la pieza, porque en la pared cada unidad
  ocupa su medida real más el mortero que la rodea. Con esos dos valores en metros se calcula
  el área de cara que ocupa una pieza puesta. Los metros cuadrados de pared se dividen por esa
  área y así se obtiene cuántas piezas entran en total. A ese número se le suma un
  <strong>5 % extra por roturas y cortes</strong> —más bajo que el 10 % de una mezcla, porque
  acá no hay agua ni compactación de por medio— y el resultado se redondea siempre hacia
  arriba, porque las piezas se compran enteras.
</p>

<h2>Un ejemplo resuelto</h2>
<p>
  Para <strong>20 m² de pared</strong> con ladrillo común de 25 × 12 cm y una junta de 1,5 cm:
  el área de cara puesta es 0,265 × 0,135 = 0,035775 m², así que entran unas 27,95 piezas por
  m². Sobre 20 m² eso da 559 piezas, y con el 5 % extra sube a <strong>588 piezas</strong>. Si
  en lugar de ladrillo común elegís bloque de hormigón de 39 × 19 cm con la misma junta de
  1,5 cm, el área de cara puesta crece a 0,405 × 0,205 = 0,083025 m², bastante más grande, así
  que para los mismos 20 m² alcanza con <strong>253 piezas</strong>. Menos de la mitad, porque
  cada bloque cubre casi el triple de superficie que un ladrillo común.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Al pedir cotización, lo primero es aclarar si vas a comprar
  <a href="/materiales/ladrillo-comun/">ladrillo común</a> o
  <a href="/materiales/bloque-de-hormigon/">bloque de hormigón</a>, porque son piezas distintas
  y el precio se cotiza por separado para cada una; la página de
  <a href="/materiales/ladrillos-y-bloques/">ladrillos y bloques</a> compara las medidas y los
  usos de cada tipo si todavía estás eligiendo. Con la cantidad de piezas de esta calculadora
  ya podés pedir presupuesto, pero recordá que el cemento y la arena para asentarlas se
  cotizan aparte: la calculadora de
  <a href="/calculadoras/revoque-y-mortero/">cal, cemento y arena por m²</a> te ayuda a
  resolver esa parte de la mezcla antes de cerrar el pedido completo.
</p>
