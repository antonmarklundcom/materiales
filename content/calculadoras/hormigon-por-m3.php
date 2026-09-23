<?php
/**
 * content/calculadoras/hormigon-por-m3.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * Dosificaciones de la tabla de §12.1: estructural 1:2:3 (350 kg cemento, 0,50 arena,
 * 0,75 ripio, 175 L agua por m³) y contrapiso 1:3:5 (250 kg cemento, 0,50 arena, 0,85 ripio
 * por m³, sin agua declarada). Nada se inventa acá.
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
  "tables": {
    "cemento_kg_m3": { "estructural": 350, "contrapiso": 250 },
    "arena_m3_m3":   { "estructural": 0.50, "contrapiso": 0.50 },
    "ripio_m3_m3":   { "estructural": 0.75, "contrapiso": 0.85 },
    "agua_l_m3":     { "estructural": 175, "contrapiso": 0 }
  },
  "outputs": [
    {
      "id": "volumen",
      "expr": {
        "op": "div",
        "args": [
          { "op": "round", "args": [{ "op": "mul", "args": [
            { "var": "m3" }, { "const": 1.1 }, { "const": 100 }
          ]}]},
          { "const": 100 }
        ]
      }
    },
    {
      "id": "bolsas",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "mul", "args": [
              { "var": "m3" },
              { "const": 1.1 },
              { "table": "cemento_kg_m3", "key": { "var": "tipo" } }
            ]},
            { "const": 50 }
          ]
        }]
      }
    },
    {
      "id": "arena",
      "expr": {
        "op": "div",
        "args": [
          { "op": "round", "args": [{ "op": "mul", "args": [
            { "var": "m3" },
            { "const": 1.1 },
            { "table": "arena_m3_m3", "key": { "var": "tipo" } },
            { "const": 100 }
          ]}]},
          { "const": 100 }
        ]
      }
    },
    {
      "id": "ripio",
      "expr": {
        "op": "div",
        "args": [
          { "op": "round", "args": [{ "op": "mul", "args": [
            { "var": "m3" },
            { "const": 1.1 },
            { "table": "ripio_m3_m3", "key": { "var": "tipo" } },
            { "const": 100 }
          ]}]},
          { "const": 100 }
        ]
      }
    },
    {
      "id": "agua",
      "expr": {
        "op": "round",
        "args": [{ "op": "mul", "args": [
          { "var": "m3" },
          { "const": 1.1 },
          { "table": "agua_l_m3", "key": { "var": "tipo" } }
        ]}]
      }
    }
  ]
}
</script>

<h2>Qué mueve el número</h2>
<p>
  "Cuánto cemento por m³ de hormigón" cambia según para qué parte de la obra sea. Un
  <strong>hormigón estructural</strong> —el que va en columnas, vigas y losas— necesita una
  dosificación más rica en cemento y una cantidad de agua controlada, porque de eso depende su
  resistencia real. Un <strong>contrapiso</strong>, en cambio, es hormigón pobre: cumple la
  función de nivelar y rellenar, no de sostener cargas, así que lleva menos cemento por metro
  cúbico y no necesita el mismo control de agua. Elegir mal el tipo no es un detalle menor: usar
  la dosificación de contrapiso en una columna deja una estructura sub-resistente.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  Se parte de los metros cúbicos que cargaste y se les suma un 10 % de desperdicio, el mismo
  supuesto que usa el resto de las calculadoras del sitio: eso da el "volumen a pedir", que es
  el número que te conviene llevar si vas a comprar <a href="/materiales/hormigon-elaborado/">
  hormigón elaborado</a> ya mezclado. Sobre ese volumen se aplica la dosificación del tipo que
  elegiste: cada m³ de mezcla pide una cantidad fija de cemento en kilos, de arena, de ripio y,
  en el caso del hormigón estructural, de agua. El cemento se divide por 50 para pasarlo a
  bolsas y se redondea siempre hacia arriba, porque se compra por bolsa entera.
</p>

<h2>Un ejemplo resuelto</h2>
<p>
  Para <strong>5 m³ de hormigón estructural</strong> (una tanda típica de columnas y vigas de
  una vivienda chica): con el 10 % de desperdicio, el volumen a pedir sube a 5,5 m³. La
  dosificación 1:2:3 pide 350 kg de cemento por m³, así que 5,5 × 350 = 1.925 kg, que dividido
  por 50 da <strong>39 bolsas</strong>. La arena sale de 5,5 × 0,50 = 2,75 m³, el ripio de
  5,5 × 0,75 = 4,125 m³ (redondeado a 4,13 m³) y el agua de 5,5 × 175 = 962 litros. Si en lugar
  de columnas fuera un contrapiso con esos mismos 5 m³, el cemento baja a 5,5 × 250 = 1.375 kg,
  o sea <strong>28 bolsas</strong>, con la misma arena pero más ripio (4,68 m³) y sin agua
  declarada en la tabla.
</p>
<p>
  La dosificación de tu obra la define quien la calcula. Estas proporciones son las de manual;
  si tu maestro o tu calculista usa otra, cambiá la proporción y rehacé la cuenta con el mismo
  volumen.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Para hormigón estructural, lo más simple suele ser pedir directamente los m³ de
  <a href="/materiales/hormigon-elaborado/">hormigón elaborado</a> con la resistencia que pida
  tu calculista: el "volumen a pedir" de esta calculadora es justo ese número. Si vas a mezclar
  en obra, pedí las bolsas de cemento, la arena y el ripio juntos, y aclará que es para
  estructura (no para contrapiso), porque algunos proveedores cotizan distinto según el uso. Qué
  rol cumple cada componente de la mezcla lo explica la guía
  <a href="/guias/cuanta-arena-y-ripio-por-m3-de-hormigon/">cómo dosificar un m³ de
  hormigón</a>. El
  hierro de columnas, vigas y losas se calcula aparte: la guía
  <a href="/guias/cuanto-hierro-lleva-una-columna/">cuánto hierro lleva una columna</a> explica
  qué mirar antes de pedirlo.
</p>
