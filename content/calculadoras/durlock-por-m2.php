<?php
/**
 * content/calculadoras/durlock-por-m2.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * Tabique en seco: placas y perfiles. Ninguna constante se inventa acá:
 *   - 1,20 m de ancho de placa: publicado en content/materiales/placa-de-yeso.php.
 *   - Montantes cada 40 o 60 cm y solera arriba y abajo: publicado en
 *     content/materiales/perfiles-para-durlock.php.
 *   - Un montante más que espacios (el de cierre): geometría.
 *   - Largo de placa, largo de barra y desperdicio: inputs del visitante, con un valor de
 *     ejemplo que se presenta como tal.
 *   - 100: conversión m → cm (y de placas a centésimas de placa, para que un error de coma
 *     flotante no sume una placa de más al redondear hacia arriba).
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
  "tables": {
    "caras": { "una": 1, "dos": 2 },
    "separacion_cm": { "40": 40, "60": 60 }
  },
  "outputs": [
    {
      "id": "placas",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "round", "args": [{
              "op": "div",
              "args": [
                { "op": "mul", "args": [
                  { "var": "largo" },
                  { "var": "alto" },
                  { "table": "caras", "key": { "var": "caras" } },
                  { "op": "add", "args": [{ "const": 1 }, { "op": "div", "args": [{ "var": "desperdicio" }, { "const": 100 }] }] },
                  { "const": 100 }
                ]},
                { "op": "mul", "args": [{ "const": 1.2 }, { "var": "largo_placa" }] }
              ]
            }]},
            { "const": 100 }
          ]
        }]
      }
    },
    {
      "id": "m2",
      "expr": { "op": "mul", "args": [
        { "var": "largo" },
        { "var": "alto" },
        { "table": "caras", "key": { "var": "caras" } }
      ]}
    },
    {
      "id": "montantes",
      "expr": { "op": "add", "args": [
        { "op": "ceil", "args": [{ "op": "div", "args": [
          { "op": "round", "args": [{ "op": "mul", "args": [{ "var": "largo" }, { "const": 100 }] }] },
          { "table": "separacion_cm", "key": { "var": "separacion" } }
        ]}]},
        { "const": 1 }
      ]}
    },
    {
      "id": "barras_montante",
      "expr": { "op": "mul", "args": [
        { "op": "add", "args": [
          { "op": "ceil", "args": [{ "op": "div", "args": [
            { "op": "round", "args": [{ "op": "mul", "args": [{ "var": "largo" }, { "const": 100 }] }] },
            { "table": "separacion_cm", "key": { "var": "separacion" } }
          ]}]},
          { "const": 1 }
        ]},
        { "op": "ceil", "args": [{ "op": "div", "args": [
          { "op": "round", "args": [{ "op": "mul", "args": [{ "var": "alto" }, { "const": 100 }] }] },
          { "op": "round", "args": [{ "op": "mul", "args": [{ "var": "largo_barra" }, { "const": 100 }] }] }
        ]}]}
      ]}
    },
    {
      "id": "barras_solera",
      "expr": { "op": "mul", "args": [
        { "const": 2 },
        { "op": "ceil", "args": [{ "op": "div", "args": [
          { "op": "round", "args": [{ "op": "mul", "args": [{ "var": "largo" }, { "const": 100 }] }] },
          { "op": "round", "args": [{ "op": "mul", "args": [{ "var": "largo_barra" }, { "const": 100 }] }] }
        ]}]}
      ]}
    }
  ]
}
</script>

<h2>Qué mueve el número</h2>
<p>
  Cuántas placas de durlock y cuántos perfiles lleva un tabique depende de tres cosas: la
  <strong>superficie</strong> (largo por alto, por una o por dos caras), la <strong>separación
  entre montantes</strong> y los <strong>largos</strong> que vende tu proveedor. La
  <a href="/materiales/placa-de-yeso/">placa de yeso</a> viene de 1,20 m de ancho, pero el largo
  cambia según lo que haya en plaza, y con él cambia cuántas placas entran. En los
  <a href="/materiales/perfiles-para-durlock/">perfiles para durlock</a> pasa lo mismo: montantes
  cada 40 cm piden más barras que cada 60 cm, y si el tabique es más alto que la barra, cada
  montante lleva más de una barra empalmada.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  Para las placas se multiplica el largo por el alto y por la cantidad de caras: esos son los
  m² a cubrir. Se les suma el desperdicio por cortes y se dividen por la superficie de una placa
  (1,20 m por su largo); el resultado se redondea hacia arriba, porque la placa se compra entera.
  Para los montantes, el largo del tabique se reparte en espacios de 40 o 60 cm y se agrega uno
  más, el de cierre en el otro extremo. Cada montante lleva tantas barras como hagan falta para
  cubrir el alto. La solera va arriba y abajo a lo largo de todo el tabique: son dos líneas del
  largo del tabique, cada una con sus barras enteras.
</p>

<h2>Un ejemplo resuelto</h2>
<p>
  Un tabique divisorio de <strong>4 m de largo por 2,60 m de alto</strong>, con placa de los dos
  lados, montantes cada 40 cm, placas de 1,20 × 2,40 m, barras de 2,60 m y 10 % de desperdicio.
  La superficie es 4 × 2,60 × 2 = <strong>20,8 m²</strong>; con el 10 % sube a 22,88 m². Cada
  placa cubre 1,20 × 2,40 = 2,88 m², así que 22,88 ÷ 2,88 = 7,94, que se redondea a
  <strong>8 placas</strong>. Los 4 m divididos cada 40 cm dan 10 espacios, o sea
  <strong>11 montantes</strong>; como el alto no pasa el largo de la barra, son
  <strong>11 barras de montante</strong>. Cada línea de solera mide 4 m y pide 2 barras de
  2,60 m: arriba y abajo suman <strong>4 barras de solera</strong>. Con montantes cada 60 cm, los
  4 m dan 6,67 espacios, que se redondean a 7, y alcanzan 8 montantes.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Pasale las placas y las barras de esta cuenta, y aclarale el destino: en baños y cocinas va la
  placa resistente a la humedad; la página de
  <a href="/materiales/yeso-y-durlock/">yeso y durlock</a> muestra todo el sistema. Confirmá el
  largo de placa y de barra que tiene: si es otro, la cantidad cambia. Los tornillos, la masilla
  y la cinta de juntas no salen de esta calculadora: pedíselos al proveedor con los m² de placa. Si el tabique lleva puertas o ventanas, avisá
  aparte por los refuerzos de vano. Y si estás armando el presupuesto de toda la obra, la guía
  <a href="/guias/como-hacer-un-computo-metrico/">cómo hacer un cómputo métrico</a> te ayuda a
  ordenar cada rubro.
</p>
