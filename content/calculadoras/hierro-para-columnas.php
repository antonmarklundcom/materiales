<?php
/**
 * content/calculadoras/hierro-para-columnas.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * Es un CONVERSOR de planilla, no un cálculo estructural: diámetros, barras, separación de
 * estribos, recubrimiento, ganchos y empalme son inputs del visitante (los defaults son un
 * ejemplo "del plano"). Constantes: kg/m = 7.850 kg/m³ × π × d² / 4 (≈ 0,00617 × d², tabla
 * `kg_m` redondeada a 3 decimales), barra comercial de 12 m (content/materiales/
 * varilla-de-hierro.php) y 10 % por cortes (supuesto declarado, CONTENT-SPEC §12.1).
 * Las cuentas van en centímetros enteros (× 11 / 12000 = × 1,1 / 100 / 12) para que ceil() no
 * salte por un error de coma flotante.
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
  "tables": { "kg_m": { "6": 0.222, "8": 0.395, "10": 0.617, "12": 0.888, "16": 1.578, "20": 2.466 } },
  "outputs": [
    {
      "id": "kg_long",
      "expr": { "op": "ceil", "args": [
        { "op": "div", "args": [
          { "op": "mul", "args": [
            { "op": "mul", "args": [
              { "var": "columnas" },
              { "var": "barras" },
              { "op": "round", "args": [
                { "op": "mul", "args": [
                  { "op": "add", "args": [{ "var": "altura" }, { "var": "empalme" }] },
                  { "const": 100 }
                ]}
              ]}
            ]},
            { "const": 11 },
            { "table": "kg_m", "key": { "var": "diam_long" } }
          ]},
          { "const": 1000 }
        ]}
      ]}
    },
    {
      "id": "barras_long",
      "expr": { "op": "ceil", "args": [
        { "op": "div", "args": [
          { "op": "mul", "args": [
            { "op": "mul", "args": [
              { "var": "columnas" },
              { "var": "barras" },
              { "op": "round", "args": [
                { "op": "mul", "args": [
                  { "op": "add", "args": [{ "var": "altura" }, { "var": "empalme" }] },
                  { "const": 100 }
                ]}
              ]}
            ]},
            { "const": 11 }
          ]},
          { "const": 12000 }
        ]}
      ]}
    },
    {
      "id": "estribos",
      "expr": { "op": "mul", "args": [
        { "var": "columnas" },
        { "op": "add", "args": [
          { "op": "ceil", "args": [
            { "op": "div", "args": [
              { "op": "round", "args": [
                { "op": "mul", "args": [{ "var": "altura" }, { "const": 100 }] }
              ]},
              { "var": "separacion" }
            ]}
          ]},
          { "const": 1 }
        ]}
      ]}
    },
    {
      "id": "largo_estribo",
      "expr": { "op": "sub", "args": [
        { "op": "add", "args": [
          { "op": "mul", "args": [{ "const": 2 }, { "var": "lado_a" }] },
          { "op": "mul", "args": [{ "const": 2 }, { "var": "lado_b" }] },
          { "var": "ganchos" }
        ]},
        { "op": "mul", "args": [{ "const": 8 }, { "var": "recubrimiento" }] }
      ]}
    },
    {
      "id": "barras_estribo",
      "expr": { "op": "ceil", "args": [
        { "op": "div", "args": [
          { "op": "mul", "args": [
            { "op": "mul", "args": [
              { "op": "mul", "args": [
                { "var": "columnas" },
                { "op": "add", "args": [
                  { "op": "ceil", "args": [
                    { "op": "div", "args": [
                      { "op": "round", "args": [
                        { "op": "mul", "args": [{ "var": "altura" }, { "const": 100 }] }
                      ]},
                      { "var": "separacion" }
                    ]}
                  ]},
                  { "const": 1 }
                ]}
              ]},
              { "op": "sub", "args": [
                { "op": "add", "args": [
                  { "op": "mul", "args": [{ "const": 2 }, { "var": "lado_a" }] },
                  { "op": "mul", "args": [{ "const": 2 }, { "var": "lado_b" }] },
                  { "var": "ganchos" }
                ]},
                { "op": "mul", "args": [{ "const": 8 }, { "var": "recubrimiento" }] }
              ]}
            ]},
            { "const": 11 }
          ]},
          { "const": 12000 }
        ]}
      ]}
    },
    {
      "id": "kg_estribos",
      "expr": { "op": "ceil", "args": [
        { "op": "div", "args": [
          { "op": "mul", "args": [
            { "op": "mul", "args": [
              { "op": "mul", "args": [
                { "var": "columnas" },
                { "op": "add", "args": [
                  { "op": "ceil", "args": [
                    { "op": "div", "args": [
                      { "op": "round", "args": [
                        { "op": "mul", "args": [{ "var": "altura" }, { "const": 100 }] }
                      ]},
                      { "var": "separacion" }
                    ]}
                  ]},
                  { "const": 1 }
                ]}
              ]},
              { "op": "sub", "args": [
                { "op": "add", "args": [
                  { "op": "mul", "args": [{ "const": 2 }, { "var": "lado_a" }] },
                  { "op": "mul", "args": [{ "const": 2 }, { "var": "lado_b" }] },
                  { "var": "ganchos" }
                ]},
                { "op": "mul", "args": [{ "const": 8 }, { "var": "recubrimiento" }] }
              ]}
            ]},
            { "const": 11 },
            { "table": "kg_m", "key": { "var": "diam_estribo" } }
          ]},
          { "const": 1000 }
        ]}
      ]}
    },
    {
      "id": "kg_total",
      "expr": { "op": "add", "args": [
        { "op": "ceil", "args": [
          { "op": "div", "args": [
            { "op": "mul", "args": [
              { "op": "mul", "args": [
                { "var": "columnas" },
                { "var": "barras" },
                { "op": "round", "args": [
                  { "op": "mul", "args": [
                    { "op": "add", "args": [{ "var": "altura" }, { "var": "empalme" }] },
                    { "const": 100 }
                  ]}
                ]}
              ]},
              { "const": 11 },
              { "table": "kg_m", "key": { "var": "diam_long" } }
            ]},
            { "const": 1000 }
          ]}
        ]},
        { "op": "ceil", "args": [
          { "op": "div", "args": [
            { "op": "mul", "args": [
              { "op": "mul", "args": [
                { "op": "mul", "args": [
                  { "var": "columnas" },
                  { "op": "add", "args": [
                    { "op": "ceil", "args": [
                      { "op": "div", "args": [
                        { "op": "round", "args": [
                          { "op": "mul", "args": [{ "var": "altura" }, { "const": 100 }] }
                        ]},
                        { "var": "separacion" }
                      ]}
                    ]},
                    { "const": 1 }
                  ]}
                ]},
                { "op": "sub", "args": [
                  { "op": "add", "args": [
                    { "op": "mul", "args": [{ "const": 2 }, { "var": "lado_a" }] },
                    { "op": "mul", "args": [{ "const": 2 }, { "var": "lado_b" }] },
                    { "var": "ganchos" }
                  ]},
                  { "op": "mul", "args": [{ "const": 8 }, { "var": "recubrimiento" }] }
                ]}
              ]},
              { "const": 11 },
              { "table": "kg_m", "key": { "var": "diam_estribo" } }
            ]},
            { "const": 1000 }
          ]}
        ]}
      ]}
    }
  ]
}
</script>

<h2>Qué mueve el número</h2>
<p>
  Esta calculadora no decide cuánto hierro necesita tu columna: eso lo define el cálculo
  estructural, como explica la guía
  <a href="/guias/cuanto-hierro-lleva-una-columna/">cuánto hierro lleva una columna</a>. Lo que
  hace es pasar los datos de tu plano a lo que se compra: <strong>barras de 12 m y kilos</strong>.
  En el hierro longitudinal pesa sobre todo el <strong>diámetro</strong>: el peso crece con su
  cuadrado, así que pasar de 8 a 12 mm más que duplica los kilos por metro. En los estribos
  mandan la <strong>separación</strong> —más juntos, más estribos— y la sección, que fija el
  largo de cada uno.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  Cada barra longitudinal mide la altura más el empalme o anclaje del plano, y se multiplica
  por las barras de cada columna y por la cantidad de columnas. Para los estribos, la altura se
  divide por la separación y se suma uno para cerrar el tramo; cada estribo mide el perímetro de la sección descontando el recubrimiento en cada cara, más los
  ganchos. A los dos largos totales se les suma un <strong>10 % por cortes</strong> —lo que
  sobra al cortar barras de 12 m en piezas que no entran justas— y se pasan a barras enteras,
  hacia arriba, y a kilos con el peso por metro de cada diámetro: densidad del acero
  (7.850 kg/m³) por área de la barra (π × d² / 4), unos <strong>0,00617 × d²</strong> kg por
  metro con d en milímetros.
</p>
<table>
  <thead>
    <tr><th>Diámetro</th><th>kg por metro</th><th>kg por barra de 12 m</th></tr>
  </thead>
  <tbody>
    <tr><td>6 mm</td><td>0,222</td><td>2,66</td></tr>
    <tr><td>8 mm</td><td>0,395</td><td>4,74</td></tr>
    <tr><td>10 mm</td><td>0,617</td><td>7,40</td></tr>
    <tr><td>12 mm</td><td>0,888</td><td>10,66</td></tr>
    <tr><td>16 mm</td><td>1,578</td><td>18,94</td></tr>
    <tr><td>20 mm</td><td>2,466</td><td>29,59</td></tr>
  </tbody>
</table>

<h2>Un ejemplo resuelto</h2>
<p>
  Con los datos precargados —<strong>8 columnas de 3 m</strong>, sección de 20 × 30 cm, 4 barras
  de 12 mm con 0,50 m de empalme, estribos de 6 mm cada 15 cm, 2,5 cm de recubrimiento y 10 cm
  de ganchos—: cada barra longitudinal mide 3,50 m, y 8 × 4 × 3,50 = 112 m, que con el 10 %
  quedan en 123,2 m. Son 123,2 / 12 = 10,3, o sea <strong>11 barras de 12 m</strong>, y
  123,2 × 0,888 = 109,4, o sea <strong>110 kg</strong>. En los estribos, 300 / 15 = 20, más uno:
  21 por columna, <strong>168 en total</strong>. Cada uno mide 2 × (20 − 5) + 2 × (30 − 5) + 10
  = <strong>90 cm</strong>; 168 × 0,90 = 151,2 m, con el 10 % 166,32 m: 13,9, o sea
  <strong>14 barras de 12 m</strong> de 6 mm y 166,32 × 0,222 = 36,9, o sea
  <strong>37 kg</strong>. En total, <strong>147 kg de hierro</strong>.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Pedí por diámetro y separá las dos partidas: "11 barras de 12 mm y 14 de 6 mm" se cotiza sin
  ida y vuelta. La <a href="/materiales/varilla-de-hierro/">varilla de hierro</a> también se
  vende por kilo; si te ofrecen corte y doblado, pasá el largo de corte del estribo y la cantidad.
  Sumá el <a href="/materiales/alambre-negro/">alambre negro</a> para atar, que esta cuenta no
  incluye, o mirá el rubro completo en <a href="/materiales/hierro/">hierro</a>. Si no sabés qué
  barra va en cada pieza, leé
  <a href="/guias/que-diametro-de-hierro-para-que-uso/">qué diámetro de hierro para qué uso</a>.
</p>
