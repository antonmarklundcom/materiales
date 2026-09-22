<?php
/**
 * content/calculadoras/membrana-por-m2.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * Sólo geometría: superficie + perímetro × subida, ancho del rollo sobre ancho útil (ancho
 * menos solape) y un margen de desperdicio. Los m² por rollo, el ancho, el solape y el
 * desperdicio son INPUTS con un valor de ejemplo — el sitio ya dice (membrana-asfaltica) que
 * los m² por rollo dependen del espesor y que se suma el solape, así que no se fija ninguno.
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
  "outputs": [
    {
      "id": "m2_cubrir",
      "expr": {
        "op": "div",
        "args": [
          { "op": "round", "args": [{ "op": "mul", "args": [
            { "op": "add", "args": [
              { "var": "m2" },
              { "op": "mul", "args": [{ "var": "perimetro" }, { "op": "div", "args": [{ "var": "subida" }, { "const": 100 }] }] }
            ]},
            { "const": 100 }
          ]}]},
          { "const": 100 }
        ]
      }
    },
    {
      "id": "m2_membrana",
      "expr": {
        "op": "div",
        "args": [
          { "op": "round", "args": [{ "op": "mul", "args": [
            { "op": "add", "args": [
              { "var": "m2" },
              { "op": "mul", "args": [{ "var": "perimetro" }, { "op": "div", "args": [{ "var": "subida" }, { "const": 100 }] }] }
            ]},
            { "op": "div", "args": [
              { "var": "ancho" },
              { "op": "sub", "args": [{ "var": "ancho" }, { "op": "div", "args": [{ "var": "solape" }, { "const": 100 }] }] }
            ]},
            { "op": "add", "args": [{ "const": 1 }, { "op": "div", "args": [{ "var": "desperdicio" }, { "const": 100 }] }] },
            { "const": 100 }
          ]}]},
          { "const": 100 }
        ]
      }
    },
    {
      "id": "rollos",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "mul", "args": [
              { "op": "add", "args": [
                { "var": "m2" },
                { "op": "mul", "args": [{ "var": "perimetro" }, { "op": "div", "args": [{ "var": "subida" }, { "const": 100 }] }] }
              ]},
              { "op": "div", "args": [
                { "var": "ancho" },
                { "op": "sub", "args": [{ "var": "ancho" }, { "op": "div", "args": [{ "var": "solape" }, { "const": 100 }] }] }
              ]},
              { "op": "add", "args": [{ "const": 1 }, { "op": "div", "args": [{ "var": "desperdicio" }, { "const": 100 }] }] }
            ]},
            { "var": "rollo_m2" }
          ]
        }]
      }
    }
  ]
}
</script>

<h2>Qué mueve el número</h2>
<p>
  "Cuántos rollos de membrana por m²" no se responde dividiendo la superficie de la losa por lo
  que trae un rollo. Pesan tres cosas más. La primera son las <strong>subidas</strong>: en los
  parapetos y muros la membrana no termina en el piso, sube unos centímetros por el borde, y
  esa franja también se cubre. La segunda es el <strong>solape entre paños</strong>: la
  <a href="/materiales/membrana-asfaltica/">membrana asfáltica</a> se superpone en los bordes,
  no se coloca a tope, así que cada paño tapa menos que su ancho. La tercera es lo que
  <strong>trae cada rollo</strong>, que depende del espesor: a mayor espesor, menos metros por
  rollo. Por eso los m² por rollo, el ancho y el solape son datos tuyos, de la etiqueta o del
  proveedor, y los que vienen cargados son sólo un ejemplo.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  Primero se arma la superficie a cubrir: los m² de la losa más el perímetro con subida
  multiplicado por la altura de esa subida en metros. Después se corrige por el solape: si el
  rollo mide 1 m de ancho y se superpone 10 cm con el siguiente, cada paño cubre sólo 0,90 m
  útiles, así que hace falta 1 ÷ 0,90 veces más membrana que superficie. A eso se le suma el
  margen de desperdicio por cortes, empalmes en las puntas y remates. Ese total de m² de
  membrana se divide por los m² que trae cada rollo y se redondea siempre hacia arriba, porque
  los rollos se compran enteros.
</p>

<h2>Un ejemplo resuelto</h2>
<p>
  Una losa de <strong>50 m²</strong> (5 × 10 m) con parapeto en todo el contorno tiene 30 m de
  perímetro. Con una subida de 20 cm, la franja suma 30 × 0,20 = 6 m², y la superficie a cubrir
  queda en <strong>56 m²</strong>. Con un rollo de ejemplo de 1 m de ancho y 10 cm de solape,
  el ancho útil es 0,90 m: 56 ÷ 0,90 = 62,22 m². Con un 5 % por cortes y remates, la membrana
  necesaria llega a <strong>65,33 m²</strong>. Si cada rollo trae 10 m², son 6,53 rollos, que
  se redondean a <strong>7 rollos</strong>. Sin contar subidas ni solape, la cuenta rápida de
  50 ÷ 10 habría dado 5 rollos: te faltarían dos.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Pasale los <strong>m² a cubrir con las subidas</strong> y el espesor que buscás, y pedile
  que te confirme cuántos m² trae el rollo de ese espesor, qué ancho tiene y qué solape
  recomienda: con esos tres datos rehacés la cuenta y comparás cotizaciones en igualdad. Si la
  losa tiene muchos quiebres, bajadas o caños que la atraviesan, preguntá por
  <a href="/materiales/membrana-liquida/">membrana líquida</a> para esos remates, que se
  cotiza aparte. Las demás opciones están en
  <a href="/materiales/impermeabilizantes/">impermeabilizantes</a>, y la guía
  <a href="/guias/como-impermeabilizar-una-losa/">cómo impermeabilizar una losa</a> explica
  cómo preparar la superficie antes de colocar el primer rollo.
</p>
