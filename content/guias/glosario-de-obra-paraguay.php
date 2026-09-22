<?php
/**
 * content/guias/glosario-de-obra-paraguay.php — glosario de obra y de corralón (improvement
 * report #2, G6).
 *
 * Reglas de este archivo:
 *  - Cada definición se sostiene con lo que el sitio YA dice en content/ y data/ (regla
 *    anti-fabricación): sin precios, sin cifras nuevas, sin plazos, sin marcas fuera de la
 *    lista cerrada de CONTENT-SPEC §11.2 (acá aparecen durlock, isopanel, blindex y syopar,
 *    siempre en minúscula y como nombre genérico).
 *  - El glosario DEFINE y ENLAZA a la página dueña de cada término (CONTENT-SPEC §11.1): los H2
 *    son letras, nunca un término de cabecera de otra página.
 *  - Las definiciones viven una sola vez, en $glosario: de ahí salen la <dl> visible y el
 *    JSON-LD DefinedTermSet, así el schema nunca dice algo que la página no muestra.
 *
 * Todo corre dentro de una función para no pisar variables del router de guías.
 */

declare(strict_types=1);

(static function (): void {
    // letra => [[ancla, término, definición (texto plano), enlace al dueño (HTML)], ...], en
    // orden alfabético. El schema usa sólo la definición; la <dd> muestra las dos.
    $glosario = [
        'A' => [
            ['alambre-negro', 'alambre negro (recocido o dulce)',
                'El alambre blando que ata la armadura: une cada cruce de varillas y estribos antes de hormigonar. Se vende por rollo o por kilo; el galvanizado o el de fardo no cumplen la misma función.',
                'Mirá el <a href="/materiales/alambre-negro/">alambre negro para atar hierro</a>.'],
            ['alero', 'alero',
                'El borde bajo del techo, donde termina el faldón. A lo largo del alero corre la canaleta, que junta el agua de lluvia y la entrega al caño de bajada.',
                'Mirá las <a href="/materiales/canaletas/">canaletas para techo</a>.'],
            ['arena-gorda', 'arena gorda',
                'La arena de grano grueso —también arena gruesa o de construcción— para contrapisos, hormigón y bases, donde importa más la resistencia que la terminación.',
                'Para revoques y trabajos finos se pide <a href="/materiales/arena-lavada/">arena lavada</a>; la gruesa está en <a href="/materiales/arena-gorda/">arena gorda</a>.'],
        ],
        'B' => [
            ['blindex', 'blindex',
                'Así se pide acá al vidrio templado: un vidrio de seguridad para mamparas de baño, frentes, puertas y barandas que, si se rompe, se desgrana en pedacitos sin filo.',
                'Mirá el <a href="/materiales/vidrio-templado/">vidrio templado</a>.'],
        ],
        'C' => [
            ['camionada', 'camionada (volquete)',
                'Una carga de camión de áridos: arena, ripio, piedra o tierra. Junto con el metro cúbico es la unidad en la que se venden, y antes de descargar conviene chequear que el volumen cargado corresponda a lo pedido.',
                'Mirá los <a href="/materiales/aridos/">áridos para construcción</a>.'],
            ['canto-rodado', 'canto rodado',
                'La piedra redondeada de origen natural que en obra se pide como ripio o grava. Va en bases compactadas, drenajes y hormigón; para hormigón estructural traba mejor la piedra triturada, de bordes filosos.',
                'Mirá el <a href="/materiales/ripio/">canto rodado y ripio</a>.'],
            ['carpeta', 'carpeta',
                'La capa fina de terminación o alisado, hecha con cemento y arena, sin ripio. Lleva bastante más cemento por metro cúbico que un contrapiso: la dosificación de manual es 1:3 y el espesor típico, de 2 a 3 cm.',
                'Hacé la cuenta en la <a href="/calculadoras/bolsas-de-cemento-por-m2/">calculadora de bolsas de cemento por m²</a>.'],
            ['chapa-trapezoidal', 'chapa trapezoidal',
                'La chapa de techo con nervios altos en forma de trapecio, más rígida que la chapa acanalada de zinc. Es la que se elige para galpones y techos con mucha luz entre correas.',
                'Mirá la <a href="/materiales/chapa-trapezoidal/">chapa trapezoidal</a>.'],
            ['computo-metrico', 'cómputo métrico',
                'La lista de cantidades que sale de pasar el plano —o las medidas de tu obra— a metros cúbicos de hormigón, ladrillos, bolsas de cemento y demás materiales. Es la base para pedir cotización.',
                'Leé <a href="/guias/como-hacer-un-computo-metrico/">cómo hacer un cómputo métrico</a>.'],
            ['contrapiso', 'contrapiso',
                'La base de hormigón pobre —cemento, arena y ripio— que se hace sobre el terreno ya nivelado y que después recibe el piso. La dosificación de manual es 1:3:5 y el espesor típico, de 8 a 10 cm; cuando lleva armado, la malla electrosoldada reemplaza al hierro atado.',
                'Calculá las bolsas en la <a href="/calculadoras/bolsas-de-cemento-por-m2/">calculadora de cemento por m²</a>.'],
            ['corralon', 'corralón',
                'El comercio de venta de materiales de construcción. Hay corralones con stock propio en depósito y otros que trabajan por encargo, y no todos incluyen el flete en la cotización.',
                'Leé <a href="/guias/como-elegir-un-corralon/">cómo elegir un corralón</a>.'],
            ['cumbrera', 'cumbrera',
                'El extremo alto del faldón y la pieza que cierra ese encuentro. Las cumbreras, igual que las babetas y la tornillería autoperforante, se cotizan aparte de la chapa: conviene pedirlas en la misma consulta.',
                'Mirá <a href="/materiales/chapas-y-techos/">chapas y techos</a>.'],
        ],
        'D' => [
            ['dosificacion', 'dosificación',
                'La proporción en volumen de los componentes de una mezcla: 1:3:5 quiere decir una parte de cemento, tres de arena y cinco de ripio, la de manual para contrapiso. No es una regla fija: la dosificación de tu obra la define quien la calcula.',
                'Leé <a href="/guias/cuanta-arena-y-ripio-por-m3-de-hormigon/">cómo dosificar un m³ de hormigón</a>.'],
            ['durlock', 'durlock',
                'Así llaman todos a la placa de yeso, que se atornilla sobre perfiles para armar tabiques y cielorrasos en seco. Hay placa común, verde para zonas húmedas y rosa resistente al fuego.',
                'Mirá la <a href="/materiales/placa-de-yeso/">placa de yeso</a>.'],
        ],
        'E' => [
            ['encofrado', 'encofrado',
                'El molde provisorio de tablas o terciada, sostenido por puntales, que le da forma al hormigón fresco de losas, vigas y columnas hasta que endurece. El desencofrado respeta los tiempos que indique el calculista, no un calendario fijo.',
                'Leé <a href="/guias/losa-de-hormigon-encofrado-y-hierro/">losa de hormigón: encofrado y hierro</a>.'],
            ['estribo', 'estribo',
                'La varilla más fina que envuelve en anillo a las barras longitudinales de una columna o una viga. Cuántos van y cada cuánto lo fija el cálculo estructural.',
                'Mirá la <a href="/materiales/varilla-de-hierro/">varilla de hierro</a>.'],
        ],
        'F' => [
            ['faldon', 'faldón',
                'Cada plano inclinado del techo. Su largo, de cumbrera a alero, es el dato que pide el proveedor para cortar la chapa a medida.',
                'Mirá la <a href="/materiales/chapa-de-zinc/">chapa de zinc</a>.'],
            ['fenolico', 'fenólico',
                'Así se pide en obra a la terciada fenólica: una placa de capas de madera encoladas en cruz, con un tratamiento que resiste mejor la humedad del hormigón fresco y aguanta más usos como encofrado. La terciada común rinde en carpintería y ambientes secos.',
                'Mirá la <a href="/materiales/terciada/">terciada</a>.'],
            ['flete', 'flete',
                'El transporte del material hasta tu obra. Casi siempre se cotiza aparte y pesa sobre todo en áridos y ladrillos, así que al comparar presupuestos fijate cuál lo incluye y avisá si el acceso a la obra es complicado.',
                'Leé <a href="/guias/como-elegir-un-corralon/">qué mirar antes de elegir un corralón</a>.'],
        ],
        'H' => [
            ['hidrofugo', 'hidrófugo',
                'El aditivo que se mezcla con el mortero o el hormigón para impermeabilizar desde adentro. Su uso típico es la capa aisladora que separa el cimiento de la mampostería, donde arranca la humedad que después se ve como salitre.',
                'Mirá el <a href="/materiales/hidrofugo/">hidrófugo</a>.'],
            ['hierro-de-8', 'hierro de 8 (de 6, de 10, de 12)',
                'Así se pide la varilla por su diámetro. Como uso típico, el de 6 y el de 8 van en estribos, el de 10 en losas y el de 12 en columnas y vigas principales de vivienda, pero el diámetro de cada pieza lo define el cálculo estructural.',
                'Leé <a href="/guias/que-diametro-de-hierro-para-que-uso/">qué diámetro de hierro para qué uso</a>.'],
            ['hormigon-armado', 'hormigón armado',
                'Hormigón con una armadura de hierro adentro: el hormigón resiste la compresión y el hierro aporta la resistencia a tracción que al hormigón le falta. Es el de losas, vigas, columnas y zapatas.',
                'Leé <a href="/guias/losa-de-hormigon-encofrado-y-hierro/">cómo se arma una losa de hormigón</a>.'],
            ['hormigon-elaborado', 'hormigón elaborado',
                'El hormigón que llega ya mezclado en un mixer, listo para volcar; también se pide como premezclado o concreto. Se vende por metro cúbico y se usa mucho en losas, contrapisos grandes y zapatas.',
                'Mirá el <a href="/materiales/hormigon-elaborado/">hormigón elaborado</a>.'],
        ],
        'I' => [
            ['isopanel', 'isopanel (panel sándwich)',
                'Así se pide la chapa termoacústica: dos caras metálicas con un núcleo aislante en el medio que corta el calor y el ruido de la lluvia. Va cuando abajo del techo hay un ambiente habitado.',
                'Mirá la <a href="/materiales/chapa-termoacustica/">chapa termoacústica</a>.'],
        ],
        'L' => [
            ['ladrillo-de-8', 'ladrillo de 8 (de 12, de 18)',
                'Así se pide el ladrillo hueco según su espesor: el de 8 va en tabiques internos livianos, el de 12 en paredes internas de más uso o con instalaciones y el de 18 en cerramientos exteriores sin función portante.',
                'Mirá el <a href="/materiales/ladrillo-hueco/">ladrillo hueco</a>.'],
            ['ladrillo-de-campo', 'ladrillo de campo',
                'Otro nombre del ladrillo común macizo, también llamado ladrillo colorado: el de la mampostería de todos los días, en paredes portantes y muros que después se revocan.',
                'Mirá el <a href="/materiales/ladrillo-comun/">ladrillo común</a>.'],
            ['ladrillo-sapo', 'ladrillo sapo',
                'Un bloque de relleno para losa alivianada que va entre viguetas, en el sistema de vigueta y ladrillo. Hay una variante cerámica y otra de telgopor, y no es lo mismo que el tejuelón: son productos distintos.',
                'Mirá el <a href="/materiales/ladrillo-sapo/">ladrillo sapo</a>.'],
            ['ladrillo-visto', 'ladrillo visto',
                'El ladrillo prensado, de caras parejas y aristas definidas, que se usa cuando la pared queda sin revocar. Se vende por millar.',
                'Mirá el <a href="/materiales/ladrillo-prensado/">ladrillo visto (prensado)</a>.'],
        ],
        'M' => [
            ['machimbre', 'machimbre',
                'La tabla angosta con macho y hembra en los cantos, que encastra pieza con pieza para formar una superficie continua en cielorrasos y techos a la vista. El machimbre de PVC es otro material.',
                'Mirá el <a href="/materiales/machimbre/">machimbre de madera</a>.'],
            ['mamposteria', 'mampostería',
                'Las paredes y muros levantados con ladrillos o bloques asentados con mortero.',
                'Para saber cuántas piezas entran por metro cuadrado usá la <a href="/calculadoras/ladrillos-por-m2/">calculadora de ladrillos por m²</a>, y para elegir la pieza mirá <a href="/materiales/ladrillos-y-bloques/">ladrillos y bloques</a>.'],
            ['metro-cubico', 'metro cúbico (m³)',
                'La unidad de volumen con la que se venden los áridos y el hormigón elaborado. Sale de multiplicar la superficie por la altura o el espesor.',
                'Para el hormigón, usá la <a href="/calculadoras/hormigon-por-m3/">calculadora de hormigón por m³</a>.'],
            ['millar', 'millar',
                'Mil unidades: así se venden los ladrillos y el tejuelón. Antes de comparar cotizaciones confirmá si el precio es por millar o por unidad, porque los dos formatos conviven en plaza.',
                'Mirá <a href="/materiales/ladrillos-y-bloques/">ladrillos y bloques</a>.'],
            ['montante-y-solera', 'montante y solera',
                'Los dos perfiles del durlock: el montante va vertical y forma la estructura del tabique; la solera se fija arriba y abajo y recibe a los montantes.',
                'Mirá los <a href="/materiales/perfiles-para-durlock/">perfiles para durlock</a>.'],
            ['mortero', 'mortero',
                'La mezcla de cemento y arena, a veces con cal, que asienta ladrillos y piedras y forma el revoque. También viene premezclado en bolsa, con la arena incluida.',
                'Mirá el <a href="/materiales/cemento/">cemento y el mortero premezclado</a>.'],
        ],
        'O' => [
            ['oleria', 'olería',
                'La fábrica de donde sale el ladrillo cerámico. La medida exacta del ladrillo varía según la olería, así que conviene confirmarla antes de calcular cuántos entran por metro cuadrado.',
                'Mirá las <a href="/materiales/ladrillo-comun/">medidas del ladrillo común</a>.'],
        ],
        'P' => [
            ['palet', 'palet',
                'La unidad de compra grande del cemento —un número fijo de bolsas—, que conviene cuando la obra tiene un consumo alto y constante. El bloque de hormigón también se cotiza por unidad o por palet.',
                'Mirá el <a href="/materiales/cemento/">cemento en bolsa</a>.'],
            ['piedra-4ta-5ta-6ta', 'piedra 4ta, 5ta y 6ta',
                'Los tamaños de la piedra triturada de basalto: la sexta es la más fina, la quinta intermedia y la cuarta la más gruesa. Se elige según el elemento y la separación entre hierros.',
                'Mirá la <a href="/materiales/piedra-triturada/">piedra triturada</a>.'],
            ['piedra-bruta', 'piedra bruta',
                'La piedra sin procesar, de forma irregular, que se asienta con mortero en cimientos corridos y muros de contención; también se la pide como piedra para cimiento.',
                'Mirá la <a href="/materiales/piedra-bruta/">piedra bruta</a>.'],
            ['puntal', 'puntal',
                'La pieza vertical que sostiene el encofrado de una losa o una viga mientras el hormigón toma resistencia. En obra chica es de madera; los metálicos regulables se suelen alquilar para obras grandes.',
                'Mirá los <a href="/materiales/puntales/">puntales</a>.'],
        ],
        'R' => [
            ['revoque-grueso-y-fino', 'revoque grueso y revoque fino',
                'Las dos capas del revoque tradicional: el grueso empareja las irregularidades de la mampostería y da el espesor base; el fino va sobre el grueso ya firme y deja la superficie lisa para pintar.',
                'Leé <a href="/guias/como-revocar-una-pared/">cómo revocar una pared</a>.'],
        ],
        'S' => [
            ['syopar', 'syopar',
                'Así se pide en Paraguay al tanque de agua en general, tenga o no ese origen: una marca tan instalada que terminó nombrando al producto. Lo que importa al pedir es el litraje.',
                'Mirá el <a href="/materiales/tanque-de-agua/">tanque de agua</a>.'],
        ],
        'T' => [
            ['tejuelon', 'tejuelón',
                'La pieza cerámica que se apoya entre viguetas premoldeadas para armar losas de piso y de techo, con una capa de compresión de hormigón encima; también queda como terminación a la vista bajo el techo. Se vende por millar.',
                'Mirá el <a href="/materiales/tejuelon/">tejuelón</a>.'],
            ['tierra-colorada', 'tierra colorada (tierra gorda, tosca)',
                'La tierra de relleno y de nivelación de terreno, que compacta bien bajo contrapisos y veredas. No es lo mismo que la tierra negra, que tiene materia orgánica y no sirve para rellenar antes de construir.',
                'Mirá la <a href="/materiales/tierra-gorda/">tierra colorada</a>.'],
            ['tirante', 'tirante',
                'La pieza de madera principal de la estructura del techo: apoya sobre muros o soleras y sostiene las correas, el machimbre o la cubierta. Se pide por escuadría, según la luz y la carga.',
                'Mirá los <a href="/materiales/tirantes/">tirantes</a>.'],
        ],
        'V' => [
            ['varilla-conformada', 'varilla conformada (hierro nervurado)',
                'La varilla de hierro con nervaduras que va dentro del hormigón armado: las nervaduras le dan mejor adherencia que una barra lisa. Se vende por barra de 12 metros, por kilo o por tonelada.',
                'Mirá la <a href="/materiales/varilla-de-hierro/">varilla de hierro</a>.'],
        ],
    ];

    $path = '/guias/glosario-de-obra-paraguay/';
    ?>
<p>
  En obra y en el corralón se habla un idioma propio, y entenderlo ahorra errores al pedir: el
  proveedor cotiza por millar o por camionada, el maestro mayor de obra te pide hierro de 8 o
  piedra 6ta, y el presupuesto del techo habla de faldón y cumbrera. Este glosario junta esas
  palabras con una definición corta y te manda a la página donde cada una se explica a fondo.
</p>
<p>
  Si lo que buscás es la cantidad, las <a href="/calculadoras/">calculadoras</a> hacen la cuenta
  con tus medidas; si querés ver todo lo que se puede cotizar, está el
  <a href="/materiales/">catálogo de materiales</a>.
</p>
<p class="glossary-index">
<?php foreach (array_keys($glosario) as $letra): ?>
  <a href="#letra-<?= e(strtolower($letra)) ?>"><?= e($letra) ?></a>
<?php endforeach; ?>
</p>
<div class="glossary" id="glosario">
<?php foreach ($glosario as $letra => $terminos): ?>
<h2 id="letra-<?= e(strtolower($letra)) ?>"><?= e($letra) ?></h2>
<dl>
<?php foreach ($terminos as [$ancla, $termino, $definicion, $enlace]): ?>
  <dt id="<?= e($ancla) ?>"><dfn><?= e($termino) ?></dfn></dt>
  <dd><?= e($definicion) ?> <?= $enlace /* HTML propio, escrito en este archivo */ ?></dd>
<?php endforeach; ?>
</dl>
<?php endforeach; ?>
</div>

<h2>Del glosario al pedido</h2>
<p>
  Con el nombre correcto, la cotización llega más rápido y es comparable: decí la unidad
  —millar, camionada, bolsa o barra—, la medida y tu zona, y preguntá si el flete está incluido.
  Los materiales que más se piden con estas palabras están en
  <a href="/materiales/ladrillos-y-bloques/">ladrillos y bloques</a>,
  <a href="/materiales/aridos/">áridos</a>, <a href="/materiales/hierro/">hierro de construcción</a>
  y <a href="/materiales/chapas-y-techos/">chapas y techos</a>.
  <a href="/cotizar/">Pedí tu cotización</a> y hasta <?= (int) site('max_proveedores', 3) ?>
  proveedores verificados te pasan su precio por WhatsApp.
</p>
<?php
    // G6: DefinedTermSet con las mismas definiciones que se ven arriba.
    $terms = [];
    foreach ($glosario as $terminos) {
        foreach ($terminos as [$ancla, $termino, $definicion]) {
            $terms[] = ['id' => $ancla, 'name' => $termino, 'description' => $definicion];
        }
    }
    schema_render([schema_defined_term_set((string) (data('guides')['glosario-de-obra-paraguay']['name'] ?? 'Glosario de obra'), $path, $terms)]);
})();
