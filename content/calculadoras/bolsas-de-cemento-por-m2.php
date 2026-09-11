<?php
/**
 * content/calculadoras/bolsas-de-cemento-por-m2.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * El bloque JSON es el árbol de expresiones que evalúa assets/js/calc.js (§12.3). Las
 * dosificaciones son las de la tabla de §12.1; acá no se inventa ningún número nuevo.
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
  "tables": {
    "cemento_kg_m3": { "contrapiso": 250, "revoque": 350, "carpeta": 450 },
    "arena_m3_m3":   { "contrapiso": 0.5, "revoque": 1.05, "carpeta": 1.0 },
    "ripio_m3_m3":   { "contrapiso": 0.85, "revoque": 0, "carpeta": 0 }
  },
  "outputs": [
    {
      "id": "bolsas",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "mul", "args": [
              { "var": "m2" },
              { "op": "div", "args": [{ "var": "espesor" }, { "const": 100 }] },
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
            { "var": "m2" },
            { "op": "div", "args": [{ "var": "espesor" }, { "const": 100 }] },
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
            { "var": "m2" },
            { "op": "div", "args": [{ "var": "espesor" }, { "const": 100 }] },
            { "const": 1.1 },
            { "table": "ripio_m3_m3", "key": { "var": "tipo" } },
            { "const": 100 }
          ]}]},
          { "const": 100 }
        ]
      }
    }
  ]
}
</script>

<h2>Qué mueve el número</h2>
<p>
  "Cuántas bolsas de cemento por m²" no tiene una respuesta única, y por eso esta calculadora
  pide tres datos en vez de uno. El primero es la superficie. El segundo, y el que más pesa, es
  el <strong>espesor</strong>: un contrapiso de 10 cm lleva más del triple de mezcla que uno de
  3 cm sobre los mismos metros cuadrados, porque lo que se calcula en realidad es un volumen.
  El tercero es el <strong>tipo de trabajo</strong>, que define la dosificación: un contrapiso
  se hace con una mezcla pobre en cemento y con ripio adentro, mientras que una carpeta de
  terminación lleva bastante más <a href="/materiales/cemento/">cemento en bolsa</a> por metro
  cúbico y no lleva ripio.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  Primero se pasa el espesor a metros y se multiplica por la superficie: eso da el volumen
  teórico de mezcla. A ese volumen se le suma un <strong>10 % de desperdicio</strong>, que es lo
  que se pierde entre irregularidades del sustrato, restos en la mezcladora y ajustes de nivel.
  Recién entonces se aplica la dosificación: cada metro cúbico de mezcla pide una cantidad de
  cemento en kilos, que se divide por 50 para pasarla a bolsas y se redondea siempre hacia
  arriba, porque el cemento se compra por bolsa entera. Con la misma lógica se calcula la
  <a href="/materiales/arena-lavada/">arena lavada</a> y, cuando corresponde, el
  <a href="/materiales/ripio/">ripio</a>.
</p>

<h2>Un ejemplo resuelto</h2>
<p>
  Supongamos un contrapiso de <strong>50 m² con 8 cm de espesor</strong>. El volumen teórico es
  50 × 0,08 = 4 m³; con el 10 % de desperdicio quedan 4,4 m³ de mezcla. La dosificación 1:3:5
  del contrapiso pide unos 250 kg de cemento por m³, así que 4,4 × 250 = 1.100 kg, que dividido
  por 50 da <strong>22 bolsas</strong>. Con los mismos 4,4 m³, la arena sale de multiplicar por
  0,5 m³/m³ (2,2 m³) y el ripio por 0,85 m³/m³ (unos 3,74 m³). Si en lugar de un contrapiso
  fuera una carpeta de 3 cm sobre esos mismos 50 m², el volumen bajaría a 1,65 m³ con
  desperdicio, pero la dosificación 1:3 es más rica: 1,65 × 450 = 742 kg, o sea 15 bolsas.
  Menos volumen y, aun así, casi la misma cantidad de bolsas por metro cuadrado.
</p>
<p>
  La dosificación de tu obra la define quien la calcula. Estas proporciones son las de manual;
  si tu maestro o tu calculista usa otra, cambiá la proporción y rehacé la cuenta con el mismo
  volumen de mezcla.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Al pedir cotización conviene mandar las tres cosas juntas: las bolsas de cemento, los metros
  cúbicos de arena y, si va contrapiso, los de ripio. Los áridos se venden por metro cúbico o
  por camionada, así que decir "2,2 m³ de arena lavada" evita la ida y vuelta de "¿cuántos
  metros son?". Aclarás la zona de entrega y con eso el proveedor ya puede cerrar el flete, que
  se cotiza aparte y suele pesar más de lo que uno espera cuando la obra está lejos del
  corralón. Si querés repasar el criterio antes de comprar, la guía
  <a href="/guias/cuantas-bolsas-de-cemento-por-m2/">cuántas bolsas de cemento por m²</a> entra
  en el detalle de contrapiso, revoque y mampostería.
</p>
