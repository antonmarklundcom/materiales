<?php
/**
 * content/calculadoras/revoque-y-mortero.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * Dosificación de la tabla de §12.1: mortero con cal (revoque) 1:1:6 — 200 kg cemento y
 * 100 kg cal por m³ de mortero, 1,05 m³ de arena por m³, 10 % de desperdicio. Nada se
 * inventa acá.
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
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
              { "const": 200 }
            ]},
            { "const": 50 }
          ]
        }]
      }
    },
    {
      "id": "cal",
      "expr": {
        "op": "round",
        "args": [{ "op": "mul", "args": [
          { "var": "m2" },
          { "op": "div", "args": [{ "var": "espesor" }, { "const": 100 }] },
          { "const": 1.1 },
          { "const": 100 }
        ]}]
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
            { "const": 1.05 },
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
  Los dos datos que cargás definen un volumen: la superficie a revocar y el
  <strong>espesor</strong> de la capa, casi siempre fino (medio a dos centímetros) porque un
  revoque grueso se fisura y se cae. Superficie en m² por espesor en metros da el volumen
  teórico de mortero. Esta calculadora resuelve un <strong>mortero con cal</strong>, que lleva
  <a href="/materiales/cal-hidratada/">cal hidratada</a> además de
  <a href="/materiales/cemento/">cemento</a> y <a href="/materiales/arena-lavada/">arena
  lavada</a>. Es distinto del revoque de mezcla pobre, sólo cemento y arena en proporción 1:4,
  que resuelve <a href="/calculadoras/bolsas-de-cemento-por-m2/">bolsas de cemento por m²</a>: si
  tu maestro pidió esa mezcla, usá esa otra calculadora.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  Primero, m² × espesor en metros da el volumen teórico de mortero. A ese volumen se le suma un
  <strong>10 % de desperdicio</strong>, que cubre lo que queda pegado en la mezcladora, los
  ajustes de nivel y las pérdidas contra la pared. Sobre el volumen final se aplica la
  dosificación <strong>1:1:6</strong> (cemento:cal:arena): 200 kg de cemento y 100 kg de cal por
  cada m³ de mortero, más 1,05 m³ de arena por m³. El cemento se divide por 50 para pasarlo a
  bolsas y se redondea siempre hacia arriba, porque se compra por bolsa entera; la cal y la
  arena se redondean al número más práctico para pedir.
</p>

<h2>Un ejemplo resuelto</h2>
<p>
  Para <strong>30 m² con 1,5 cm de espesor</strong> (un revoque fino típico de interior): el
  volumen teórico es 30 × 0,015 = 0,45 m³; con el 10 % de desperdicio quedan 0,495 m³ de
  mortero. El cemento sale de 0,495 × 200 = 99 kg, que dividido por 50 y redondeado hacia arriba
  da <strong>2 bolsas</strong>. La cal sale de 0,495 × 100 = 49,5 kg, redondeado a
  <strong>50 kg</strong>. La arena sale de 0,495 × 1,05 = 0,51975 m³, redondeado a
  <strong>0,52 m³</strong>. Con 2 cm de espesor sobre los mismos 30 m², el volumen con
  desperdicio pasa a 0,66 m³ y ya son 3 bolsas en vez de 2: medio centímetro más alcanza para
  cruzar el umbral de la bolsa entera.
</p>
<p>
  La dosificación de tu obra la define quien la calcula. Estas proporciones son las de manual;
  si tu maestro o tu calculista usa otra, cambiá la proporción y rehacé la cuenta con el mismo
  volumen de mortero.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Conviene pedir la cal, el cemento y la arena en el mismo pedido, aclarando que es
  <strong>mortero con cal</strong> y no revoque de cemento solo, porque algunos corralones
  cotizan la cal aparte si no se la mencionás. La arena se pide por metro cúbico o por
  camionada, así que decir "0,52 m³ de arena lavada" evita idas y vueltas. Si antes de revocar
  todavía te falta el hierro o la losa, o si la pared va a quedar expuesta a humedad y pensás
  en una capa impermeable después del revoque, repasá la guía
  <a href="/guias/como-revocar-una-pared/">cómo revocar una pared</a> antes de cerrar el pedido:
  ahí se explica el orden de las capas y cuándo conviene esperar entre una y otra.
</p>
