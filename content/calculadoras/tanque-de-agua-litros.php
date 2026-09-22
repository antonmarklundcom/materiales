<?php
/**
 * content/calculadoras/tanque-de-agua-litros.php — fórmula + prosa (CONTENT-SPEC §12).
 *
 * Qué mueve el litraje (personas, baños, reserva) y los litrajes de 500 y 1000 L son los que ya
 * publica content/materiales/tanque-de-agua.php. El consumo por persona por día, los días de
 * reserva y el margen extra son INPUTS del visitante; el default de 150 L/persona/día se
 * presenta como referencia de diseño de los manuales de instalaciones sanitarias, no como dato
 * del sitio. La cuenta multiplica por (100 + margen) y divide por 100 para no arrastrar error
 * de coma flotante antes del ceil. Nada de esto se inventa acá.
 */

declare(strict_types=1);
?>
<script type="application/json" data-calc>
{
  "outputs": [
    {
      "id": "litros",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "mul", "args": [
              { "var": "personas" },
              { "var": "consumo" },
              { "var": "dias" },
              { "op": "add", "args": [{ "const": 100 }, { "var": "margen" }] }
            ]},
            { "const": 100 }
          ]
        }]
      }
    },
    {
      "id": "tanques_1000",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "ceil", "args": [{
              "op": "div",
              "args": [
                { "op": "mul", "args": [
                  { "var": "personas" },
                  { "var": "consumo" },
                  { "var": "dias" },
                  { "op": "add", "args": [{ "const": 100 }, { "var": "margen" }] }
                ]},
                { "const": 100 }
              ]
            }]},
            { "const": 1000 }
          ]
        }]
      }
    },
    {
      "id": "tanques_500",
      "expr": {
        "op": "ceil",
        "args": [{
          "op": "div",
          "args": [
            { "op": "ceil", "args": [{
              "op": "div",
              "args": [
                { "op": "mul", "args": [
                  { "var": "personas" },
                  { "var": "consumo" },
                  { "var": "dias" },
                  { "op": "add", "args": [{ "const": 100 }, { "var": "margen" }] }
                ]},
                { "const": 100 }
              ]
            }]},
            { "const": 500 }
          ]
        }]
      }
    }
  ]
}
</script>

<h2>Qué mueve el número</h2>
<p>
  De cuántos litros tiene que ser el <a href="/materiales/tanque-de-agua/">tanque de agua</a>
  sale de tres cosas: cuánta gente vive en la casa, cuánta agua usa cada persona por día y
  cuántos días querés aguantar si se corta el servicio o falta presión de la red. Los baños
  entran a través del consumo: una casa con varios baños y muchas duchas diarias gasta más
  litros por persona que una con un solo baño, así que ahí es donde conviene ajustar. El
  consumo viene cargado en <strong>150 litros por persona por día</strong>, un valor de
  referencia habitual en los manuales de instalaciones sanitarias para vivienda; no es una
  medición de tu casa, y si sabés que usan más o menos, cambialo.
</p>

<h2>La cuenta, en palabras</h2>
<p>
  Se multiplican las personas por los litros por persona por día y por los días de reserva:
  eso es el agua que el tanque tiene que guardar. A ese número se le suma el
  <strong>margen extra</strong> que elegiste —viene en 10 % como ejemplo, para no dejar el
  litraje ajustado al mínimo— y se redondea hacia arriba al litro entero. Después esos litros
  se dividen por 1000 y por 500 para contar cuántos tanques de cada litraje suman esa reserva,
  siempre hacia arriba, porque los tanques se compran enteros. La calculadora no elige el
  tanque por vos: te dice cuántos litros necesitás y cómo se arman con tanques de 1000 o de
  500 litros.
</p>

<h2>Un ejemplo resuelto</h2>
<p>
  Para una casa de <strong>4 personas</strong> con 150 litros por persona por día y
  <strong>1 día de reserva</strong>: 4 × 150 × 1 = 600 litros. Con el 10 % de margen, 600 ×
  1,10 = <strong>660 litros</strong>. Eso entra en <strong>1 tanque de 1000 litros</strong>
  (660 ÷ 1000 = 0,66, que redondeado hacia arriba es 1), o en <strong>2 tanques de 500
  litros</strong> (660 ÷ 500 = 1,32, hacia arriba 2). Si la misma familia quiere cubrir
  <strong>2 días</strong> de corte, la cuenta se duplica: 1.320 litros, que son 2 tanques de
  1000 litros o 3 de 500. Como un litro de agua pesa alrededor de un kilo, esos 1.320 litros
  llenos son más de una tonelada sobre la base.
</p>

<h2>Qué pedirle al proveedor</h2>
<p>
  Con los litros de esta calculadora ya podés pedir cotización: decí cuántos litros de reserva
  necesitás y si preferís uno o varios tanques, y preguntá si el modelo es tricapa o bicapa,
  porque eso cambia cómo se comporta el agua al sol. Pedí en la misma consulta la base, el
  flotante, las llaves de paso y las conexiones, que se cotizan aparte; el
  <a href="/materiales/cano-de-agua/">caño de agua</a> que baja del tanque a cada artefacto y
  el resto de <a href="/materiales/canos-y-plomeria/">caños y plomería</a> también se pueden
  sumar al pedido. Si estás definiendo el tanque junto con toda la instalación, la guía
  <a href="/guias/como-hacer-un-computo-metrico/">cómo hacer un cómputo métrico</a> te ayuda a
  ordenar la lista antes de pedir presupuesto.
</p>
