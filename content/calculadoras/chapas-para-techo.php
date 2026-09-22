<?php
/**
 * content/calculadoras/chapas-para-techo.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * No hay ninguna medida de chapa fija: el ancho útil lo carga el visitante (el que le da el
 * proveedor para su perfil) y el largo de cada chapa es el largo del faldón, como piden
 * content/materiales/chapa-de-zinc.php y chapa-trapezoidal.php. El único supuesto propio es el
 * margen de chapas de más (5 % por defecto, editable). El redondeo interno (× 1000) sólo
 * limpia el ruido de coma flotante antes del `ceil` (8,5 / 0,85 tiene que dar 10, no 11).
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
  "tables": {
    "faldones": { "una": 1, "dos": 2 }
  },
  "outputs": [
    {
      "id": "por_faldon",
      "expr": { "op": "ceil", "args": [
        { "op": "div", "args": [
          { "op": "round", "args": [
            { "op": "mul", "args": [{ "op": "div", "args": [{ "var": "ancho" }, { "var": "ancho_util" }] }, { "const": 1000 }] }
          ]},
          { "const": 1000 }
        ]}
      ]}
    },
    {
      "id": "chapas",
      "expr": { "op": "ceil", "args": [
        { "op": "div", "args": [
          { "op": "mul", "args": [
            { "op": "ceil", "args": [
              { "op": "div", "args": [
                { "op": "round", "args": [
                  { "op": "mul", "args": [{ "op": "div", "args": [{ "var": "ancho" }, { "var": "ancho_util" }] }, { "const": 1000 }] }
                ]},
                { "const": 1000 }
              ]}
            ]},
            { "table": "faldones", "key": { "var": "faldones" } },
            { "op": "add", "args": [{ "const": 100 }, { "var": "desperdicio" }] }
          ]},
          { "const": 100 }
        ]}
      ]}
    },
    {
      "id": "largo_chapa",
      "expr": { "var": "largo" }
    },
    {
      "id": "metros",
      "expr": { "op": "mul", "args": [
        { "op": "ceil", "args": [
          { "op": "div", "args": [
            { "op": "mul", "args": [
              { "op": "ceil", "args": [
                { "op": "div", "args": [
                  { "op": "round", "args": [
                    { "op": "mul", "args": [{ "op": "div", "args": [{ "var": "ancho" }, { "var": "ancho_util" }] }, { "const": 1000 }] }
                  ]},
                  { "const": 1000 }
                ]}
              ]},
              { "table": "faldones", "key": { "var": "faldones" } },
              { "op": "add", "args": [{ "const": 100 }, { "var": "desperdicio" }] }
            ]},
            { "const": 100 }
          ]}
        ]},
        { "var": "largo" }
      ]}
    },
    {
      "id": "m2",
      "expr": { "op": "mul", "args": [
        { "var": "largo" },
        { "var": "ancho" },
        { "table": "faldones", "key": { "var": "faldones" } }
      ]}
    }
  ]
}
</script>

<h2>Qué mueve el número</h2>
<p>
  "Cuántas chapas para un techo" no se resuelve con los metros cuadrados solos. Las chapas se
  cotizan por chapa o por metro lineal, y para pedirlas bien hacen falta dos medidas de cada
  faldón: el <strong>largo de cumbrera a alero</strong>, que es el largo de cada chapa, y el
  <strong>ancho a lo largo del alero</strong>, que define cuántas chapas van una al lado de la
  otra. El tercer dato es el <strong>ancho útil</strong> de la chapa: lo que cubre una vez
  colocada, descontado el solape con la vecina. Ese ancho cambia con el perfil, así que es el
  número que más mueve el resultado y el que tenés que pedirle a tu proveedor.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  El ancho del faldón se divide por el ancho útil de la chapa y el resultado se redondea hacia
  arriba, porque una chapa cortada a lo largo no se compra: son las chapas por faldón. Eso se
  multiplica por la cantidad de faldones (uno en un techo a 1 agua, dos en uno a 2 aguas). Al
  total se le suma un <strong>margen de chapas de más</strong> —5 % por defecto, por golpes en
  el manejo y cortes— y se vuelve a redondear hacia arriba. Como cada chapa va entera de
  cumbrera a alero, los metros lineales son las chapas por el largo del faldón. Los m² de techo
  salen de largo × ancho × faldones y sirven para comparar con lo que te cotizan.
</p>

<h2>Un ejemplo resuelto</h2>
<p>
  Un techo a <strong>2 aguas</strong> con faldones de <strong>4,5 m</strong> de cumbrera a
  alero y <strong>8,5 m</strong> a lo largo del alero, con una chapa de <strong>1 m de ancho
  útil</strong> (un valor de ejemplo): 8,5 ÷ 1 = 8,5, que se redondea a <strong>9 chapas por
  faldón</strong>. Por dos faldones son 18; con el 5 % de margen da 18,9, que se redondea a
  <strong>19 chapas</strong> de 4,5 m. Eso son 19 × 4,5 = <strong>85,5 metros lineales</strong>
  para <strong>76,5 m²</strong> de techo. Si el perfil que te ofrecen cubriera 0,8 m, el mismo
  techo pediría 11 chapas por faldón y 24 en total, o sea 108 metros lineales: por eso conviene
  pedir el ancho útil antes de comparar cotizaciones.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Pasale el largo de cada chapa, la cantidad y el calibre, y preguntale el ancho útil del perfil
  que te ofrece y si le conviene cotizar por chapa o por metro lineal. Si el techo es de vivienda
  con estructura liviana, la <a href="/materiales/chapa-de-zinc/">chapa de zinc</a> acanalada
  suele bastar; con correas separadas, mirá la
  <a href="/materiales/chapa-trapezoidal/">chapa trapezoidal</a>, y si abajo va un ambiente
  habitado, la <a href="/materiales/chapa-termoacustica/">chapa termoacústica</a>. La guía
  <a href="/guias/que-chapa-conviene-para-techo/">qué chapa conviene para tu techo</a> compara
  las tres. Cumbreras, babetas y tornillería autoperforante se cotizan aparte: pedílas junto con
  las chapas para no fraccionar el flete.
</p>
