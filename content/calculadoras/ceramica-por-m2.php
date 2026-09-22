<?php
/**
 * content/calculadoras/ceramica-por-m2.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * Cajas cerradas y redondeo hacia arriba: lo que ya publican ceramica-para-piso.php y
 * porcelanato.php. Los m² por caja, el rendimiento del adhesivo y el peso de la bolsa son
 * INPUTS con un valor de ejemplo (figuran en la caja y en la bolsa). El único supuesto propio
 * es el extra por cortes: 10 % en colocación recta, 15 % en diagonal o espiga (§12.1).
 * Los m² se redondean a 0,01 antes de dividir por la caja (enteros ÷ enteros), para que un
 * resultado exacto no suba una caja por error de coma flotante.
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
  "tables": {
    "factor_desperdicio": { "recta": 1.10, "diagonal": 1.15 }
  },
  "outputs": [
    {
      "id": "m2_compra",
      "expr": {
        "op": "div",
        "args": [
          { "op": "round", "args": [{ "op": "mul", "args": [
            { "var": "m2" },
            { "table": "factor_desperdicio", "key": { "var": "colocacion" } },
            { "const": 100 }
          ]}]},
          { "const": 100 }
        ]
      }
    },
    {
      "id": "cajas",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "round", "args": [{ "op": "mul", "args": [
              { "var": "m2" },
              { "table": "factor_desperdicio", "key": { "var": "colocacion" } },
              { "const": 100 }
            ]}]},
            { "op": "round", "args": [{ "op": "mul", "args": [{ "var": "m2_caja" }, { "const": 100 }] }] }
          ]
        }]
      }
    },
    {
      "id": "bolsas",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "mul", "args": [{ "var": "m2" }, { "var": "adhesivo_kg_m2" }] },
            { "var": "bolsa_kg" }
          ]
        }]
      }
    }
  ]
}
</script>

<h2>Qué mueve el número</h2>
<p>
  "Cuántas cajas de cerámica por m²" depende de tres datos. El primero es la superficie del
  ambiente; si tiene forma de L, partila en rectángulos y sumá los m² de cada uno. El segundo
  son los <strong>m² que trae cada caja</strong>, que no es un número fijo: cambia con el
  formato y con el fabricante, y está impreso en la caja. El tercero es la
  <strong>colocación</strong>. En recta sólo se corta la última hilera contra cada pared; en
  diagonal o en espiga casi toda pieza del borde se corta en ángulo y buena parte del recorte
  no se aprovecha. Por eso la
  <a href="/materiales/ceramica-para-piso/">cerámica para piso</a> y el
  <a href="/materiales/porcelanato/">porcelanato</a> se piden con un extra que depende de cómo
  se colocan.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  Los m² del ambiente se multiplican por el extra de la colocación: <strong>10 %</strong> en
  recta y <strong>15 %</strong> en diagonal o espiga, un supuesto que cubre cortes, roturas y
  alguna pieza de repuesto. Esos m² se dividen por los m² de una caja y el resultado se
  redondea siempre hacia arriba, porque el proveedor vende la caja cerrada y no la fracciona.
  El adhesivo va aparte: los m² del ambiente —sin el extra, porque las piezas que sobran no se
  pegan— se multiplican por el rendimiento en kg/m² que figura en la bolsa, se dividen por los
  kilos de la bolsa y también se redondean a bolsa entera.
</p>

<h2>Un ejemplo resuelto</h2>
<p>
  Un dormitorio de <strong>18 m²</strong> con colocación recta y cajas de 1,44 m² (por ejemplo,
  4 piezas de 60 × 60 cm): 18 × 1,10 = 19,8 m² a comprar; 19,8 ÷ 1,44 = 13,75, que sube a
  <strong>14 cajas</strong>, o sea 20,16 m² en cajas cerradas. Con un adhesivo que rinde
  5 kg/m² en bolsas de 25 kg: 18 × 5 = 90 kg, y 90 ÷ 25 = 3,6, que sube a
  <strong>4 bolsas</strong>. Si ese mismo piso va en diagonal, el extra pasa al 15 %:
  18 × 1,15 = 20,7 m², y 20,7 ÷ 1,44 = 14,38, que sube a <strong>15 cajas</strong>. Una caja
  más sólo por cambiar el sentido de la colocación; el adhesivo no cambia, porque la superficie
  a pegar es la misma.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Mandá las cajas y los m² juntos, con el formato y el modelo: así el proveedor confirma que
  esa caja trae los m² que cargaste. Pedí todas las cajas del mismo lote, porque el tono puede
  variar entre partidas, y guardá alguna pieza de repuesto. El
  <a href="/materiales/adhesivo-para-ceramica/">adhesivo</a> va en el mismo pedido: para
  porcelanato tiene que ser el reforzado, con su propio rendimiento. La pastina pedíla con los
  m² del ambiente y el color de junta. Si es para la pared de un baño, la misma cuenta sirve
  para <a href="/materiales/azulejos/">azulejos</a>; el resto de las opciones está en
  <a href="/materiales/pisos-y-revestimientos/">pisos y revestimientos</a>, y la guía
  <a href="/guias/como-hacer-un-computo-metrico/">cómo hacer un cómputo métrico</a> te ayuda a
  medir bien los m² antes de cotizar.
</p>
