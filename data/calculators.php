<?php
/**
 * data/calculators.php — calculadoras servidas en /calculadoras/{slug}/ (plan §11.4,
 * decisión §1.21). Namespace de slugs PROPIO, único dentro de este archivo.
 *
 * Contrato de una entrada (claves en inglés, valores en español):
 *
 *   name, status ('activa'|'proxima'), order, title (≤ 60), meta (≤ 155), keyword,
 *   intro                     bajada de una línea
 *   inputs[]                  { id, label, unit, type ('number'|'select'),
 *                               min, max, step, default, options[] { value, label } }
 *   outputs[]                 { id, label, unit }
 *   formula_note              UNA oración visible que explica la cuenta
 *   assumptions[]             supuestos visibles (dosificación, desperdicio, espesores)
 *   related[]                 slugs de materiales, categorías o guías a los que sirve
 *   faq[]                     3 a 5; la última es SIEMPRE la de precio (CONTENT-SPEC §11.3)
 *   cta_material              slug preseleccionado en el formulario
 *   cta_quantity_template     'texto con {id_de_salida}' para precargar `cantidad`
 *   cta_button                (opcional) texto del botón bajo el resultado, con {id_de_salida}
 *                             (C2); vacío = "Cotizá estas cantidades →"
 *
 * La FÓRMULA no vive acá: es el bloque `<script type="application/json" data-calc>` del
 * archivo de contenido, un árbol de expresiones que evalúa assets/js/calc.js sin `eval`
 * (CONTENT-SPEC §12.3). Las dosificaciones salen de la tabla de CONTENT-SPEC §12.1: una
 * calculadora nueva que necesite otra la agrega ALLÁ en el mismo PR, nunca suelta en el código.
 *
 * Clave OPCIONAL `updated` (YYYY-MM-DD): la usa el sitemap para `lastmod` (decisión §1.25).
 */

declare(strict_types=1);

return [
    'bolsas-de-cemento-por-m2' => [
        'keyword' => 'cuántas bolsas de cemento por m2',
        'name'    => 'Cuántas bolsas de cemento por m²',
        'status'  => 'activa',
        'published' => '2026-09-11',
        'updated'   => '2026-09-15',
        'order'   => 1,
        'title'   => 'Calculadora de bolsas de cemento por m² | Paraguay',
        'meta'    => 'Calculá cuántas bolsas de cemento de 50 kg y cuánta arena lleva tu contrapiso, revoque o carpeta por m². Con la cuenta explicada y cotización en un paso.',
        'intro'   => 'Cargá los metros cuadrados, el espesor y el tipo de trabajo, y mirá cuántas bolsas de cemento de 50 kg y cuánta arena necesitás.',

        'inputs' => [
            [
                'id'      => 'm2',
                'label'   => '¿Cuántos metros cuadrados?',
                'unit'    => 'm²',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 10000,
                'step'    => 1,
                'default' => 50,
            ],
            [
                'id'      => 'espesor',
                'label'   => '¿Qué espesor de capa?',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 0.5,
                'max'     => 30,
                'step'    => 0.5,
                'default' => 8,
            ],
            [
                'id'      => 'tipo',
                'label'   => '¿Qué vas a hacer?',
                'unit'    => '',
                'type'    => 'select',
                'default' => 'contrapiso',
                'options' => [
                    ['value' => 'contrapiso', 'label' => 'Contrapiso (1:3:5, espesor típico 8 a 10 cm)'],
                    ['value' => 'revoque',    'label' => 'Revoque grueso o mampostería (1:4, espesor típico 1,5 a 2 cm)'],
                    ['value' => 'carpeta',    'label' => 'Carpeta o alisado (1:3, espesor típico 2 a 3 cm)'],
                ],
            ],
        ],

        'outputs' => [
            ['id' => 'bolsas', 'label' => 'Bolsas de cemento de 50 kg', 'unit' => 'bolsas'],
            ['id' => 'arena',  'label' => 'Arena',                      'unit' => 'm³'],
            ['id' => 'ripio',  'label' => 'Ripio',                      'unit' => 'm³'],
        ],

        'formula_note' => 'Metros cuadrados × espesor en metros = volumen de mezcla; ese volumen, más un 10 % de desperdicio, se multiplica por el cemento y los áridos que pide la dosificación de cada trabajo.',

        'assumptions' => [
            'Bolsa de cemento de 50 kg (≈ 0,035 m³ suelta).',
            'Desperdicio del 10 % sobre el volumen teórico.',
            'Contrapiso 1:3:5 (250 kg de cemento por m³), revoque y mampostería 1:4 (350 kg por m³), carpeta 1:3 (450 kg por m³) — CONTENT-SPEC §12.1.',
            'El ripio sólo entra en el contrapiso: en revoque y carpeta el resultado es 0.',
            'La dosificación de tu obra la define quien la calcula: si usás otra proporción, la cuenta cambia.',
        ],

        'related' => [
            'cemento',
            'arena-lavada',
            'ripio',
            'cemento-y-cal',
            'cuantas-bolsas-de-cemento-por-m2',
        ],

        'faq' => [
            [
                'q' => '¿Sirve para cualquier dosificación?',
                'a' => 'Sirve para las tres dosificaciones de manual que están en el selector: contrapiso 1:3:5, revoque y mampostería 1:4 y carpeta 1:3. Si tu maestro o tu calculista usa otra proporción, tomá el volumen de mezcla que sale acá y aplicale la dosificación de tu obra.',
            ],
            [
                'q' => '¿Por qué suma 10 % de desperdicio?',
                'a' => 'Porque entre el relleno de irregularidades, lo que queda en la mezcladora y el ajuste de niveles, el volumen real que se prepara siempre es mayor que el teórico. Es un supuesto, no una ley: si tu obra es muy pareja podés pedir menos, y si el sustrato está muy irregular conviene más.',
            ],
            [
                'q' => '¿Incluye el agua y la cal?',
                'a' => 'No. El agua se ajusta en obra según la trabajabilidad que busques, y la cal aparece sólo si usás un mortero con cal (1:1:6). Si vas a revocar con cal, pedí también cal hidratada al cotizar y aclaralo en el mensaje.',
            ],
            [
                'q' => '¿Cuánto cuesta el cemento que necesito?',
                'a' => 'No publicamos precios porque cambian con la cantidad de bolsas que lleves, la marca que consiga el proveedor, el flete hasta tu zona y el momento en que pidas. Cargá las bolsas que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'cemento',
        'cta_quantity_template' => '{bolsas} bolsas de cemento de 50 kg',
        'cta_button'            => 'Cotizá estas {bolsas} bolsas →',
    ],

    'hormigon-por-m3' => [
        'keyword' => 'cuánto cemento arena y ripio por m3 de hormigón',
        'name'    => 'Cuánto cemento, arena y ripio por m³ de hormigón',
        'status'  => 'activa',
        'published' => '2026-09-13',
        'updated'   => '2026-09-13',
        'order'   => 2,
        'title'   => 'Calculadora de hormigón por m³ en Paraguay',
        'meta'    => 'Calculá cuántas bolsas de cemento, m³ de arena, m³ de ripio y litros de agua lleva tu hormigón por m³, para columnas, vigas, losas o contrapiso.',
        'intro'   => 'Cargá los m³ de hormigón que necesitás y el tipo de estructura, y mirá cuántas bolsas de cemento, arena, ripio y agua entran en la mezcla.',

        'inputs' => [
            [
                'id'      => 'm3',
                'label'   => '¿Cuántos metros cúbicos de hormigón?',
                'unit'    => 'm³',
                'type'    => 'number',
                'min'     => 0.1,
                'max'     => 500,
                'step'    => 0.1,
                'default' => 5,
            ],
            [
                'id'      => 'tipo',
                'label'   => '¿Para qué parte de la obra?',
                'unit'    => '',
                'type'    => 'select',
                'default' => 'estructural',
                'options' => [
                    ['value' => 'estructural', 'label' => 'Columnas, vigas o losas (1:2:3, hormigón estructural)'],
                    ['value' => 'contrapiso',  'label' => 'Contrapiso o piso (1:3:5, hormigón pobre)'],
                ],
            ],
        ],

        'outputs' => [
            ['id' => 'volumen', 'label' => 'Volumen a pedir (con desperdicio)', 'unit' => 'm³'],
            ['id' => 'bolsas',  'label' => 'Bolsas de cemento de 50 kg',        'unit' => 'bolsas'],
            ['id' => 'arena',   'label' => 'Arena',                             'unit' => 'm³'],
            ['id' => 'ripio',   'label' => 'Ripio',                             'unit' => 'm³'],
            ['id' => 'agua',    'label' => 'Agua',                              'unit' => 'litros'],
        ],

        'formula_note' => 'Los m³ que pediste, más un 10 % de desperdicio, se multiplican por el cemento, los áridos y el agua que pide la dosificación de cada tipo de estructura (CONTENT-SPEC §12.1).',

        'assumptions' => [
            'Bolsa de cemento de 50 kg.',
            'Desperdicio del 10 % sobre el volumen teórico.',
            'Estructural 1:2:3: 350 kg de cemento, 0,50 m³ de arena, 0,75 m³ de ripio y ~175 L de agua por m³.',
            'Contrapiso 1:3:5: 250 kg de cemento, 0,50 m³ de arena y 0,85 m³ de ripio por m³; la tabla de §12.1 no declara agua para este tipo, se ajusta en obra.',
            'La dosificación de tu obra la define quien la calcula. Estas proporciones son las de manual; si tu maestro o tu calculista usa otra, cambiá la proporción y rehacé la cuenta.',
        ],

        'related' => [
            'hormigon-elaborado',
            'cemento',
            'arena-lavada',
            'ripio',
            'cuanta-arena-y-ripio-por-m3-de-hormigon',
            'cuanto-hierro-lleva-una-columna',
        ],

        'faq' => [
            [
                'q' => '¿Sirve para hormigón armado, con hierro adentro?',
                'a' => 'Sí: esta calculadora sólo resuelve la mezcla (cemento, áridos y agua). El hierro de columnas, vigas y losas se calcula aparte, según los planos o el cálculo estructural de tu obra.',
            ],
            [
                'q' => '¿Por qué el contrapiso no lleva litros de agua?',
                'a' => 'Porque la tabla de dosificaciones estándar sólo trae el dato de agua para el hormigón estructural, que necesita una relación agua-cemento más controlada. Para contrapiso, el agua se ajusta en obra según la trabajabilidad que busque el maestro.',
            ],
            [
                'q' => '¿Puedo pedir hormigón elaborado en vez de mezclar en obra?',
                'a' => 'Sí, y para volúmenes grandes suele convenir: el "volumen a pedir" que te da la calculadora es directamente los m³ de hormigón elaborado que pedís, ya con el desperdicio sumado. Las bolsas, la arena y el ripio son la referencia si preferís mezclarlo vos.',
            ],
            [
                'q' => '¿Cuánto cuesta el hormigón que necesito?',
                'a' => 'No publicamos precios porque cambian según si es hormigón elaborado o mezclado en obra, la cantidad de m³, el flete hasta tu zona y el momento en que pidas. Cargá el volumen que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'hormigon-elaborado',
        'cta_quantity_template' => '{volumen} m³ de hormigón, o {bolsas} bolsas de cemento si lo mezclás en obra',
        'cta_button'            => 'Cotizá estos {volumen} m³ de hormigón →',
    ],

    'ladrillos-por-m2' => [
        'keyword' => 'cuántos ladrillos por m2',
        'name'    => 'Cuántos ladrillos o bloques por m²',
        'status'  => 'activa',
        'published' => '2026-09-13',
        'updated'   => '2026-09-13',
        'order'   => 3,
        'title'   => 'Calculadora de ladrillos y bloques por m² | Paraguay',
        'meta'    => 'Calculá cuántos ladrillos comunes o bloques de hormigón necesitás por m² de pared, según la medida y la junta de mortero.',
        'intro'   => 'Elegí la medida del ladrillo o bloque, cargá la junta de mortero y los m² de pared, y mirá cuántas piezas necesitás.',

        'inputs' => [
            [
                'id'      => 'm2',
                'label'   => '¿Cuántos metros cuadrados de pared?',
                'unit'    => 'm²',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 5000,
                'step'    => 1,
                'default' => 20,
            ],
            [
                'id'      => 'tipo',
                'label'   => '¿Qué pieza vas a usar?',
                'unit'    => '',
                'type'    => 'select',
                'default' => 'comun_25',
                'options' => [
                    ['value' => 'comun_22', 'label' => 'Ladrillo común 22 × 11 cm (pared simple)'],
                    ['value' => 'comun_25', 'label' => 'Ladrillo común 25 × 12 cm (mampostería estándar)'],
                    ['value' => 'comun_28', 'label' => 'Ladrillo común 28 × 14 cm (muro grueso)'],
                    ['value' => 'bloque',   'label' => 'Bloque de hormigón 39 × 19 cm (estándar)'],
                ],
            ],
            [
                'id'      => 'junta',
                'label'   => '¿Qué junta de mortero usás?',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 2,
                'step'    => 0.5,
                'default' => 1.5,
            ],
        ],

        'outputs' => [
            ['id' => 'piezas', 'label' => 'Piezas', 'unit' => 'unidades'],
        ],

        'formula_note' => 'Con la medida de cara de la pieza y la junta de mortero se calcula cuántas piezas entran por m²; ese número se multiplica por los m² de pared con un 5 % extra por roturas y cortes (CONTENT-SPEC §12.1).',

        'assumptions' => [
            'Medidas de cara: ladrillo común 22 × 11 / 25 × 12 / 28 × 14 cm (las mismas de la página de cada material); bloque de hormigón, medida estándar de manual 39 × 19 cm.',
            'La junta de mortero se suma a lo ancho y a lo alto de cada pieza antes de calcular cuántas entran por m².',
            '5 % de piezas extra por roturas y cortes.',
            'La medida exacta varía según la olería o el fabricante: confirmá con tu proveedor antes de comprar.',
        ],

        'related' => [
            'ladrillo-comun',
            'ladrillo-hueco',
            'bloque-de-hormigon',
            'ladrillos-y-bloques',
            'ladrillo-comun-vs-hueco',
            'como-hacer-un-computo-metrico',
        ],

        'faq' => [
            [
                'q' => '¿Por qué pide la junta de mortero?',
                'a' => 'Porque la junta suma tamaño a cada pieza: con una junta de 1,5 cm, un ladrillo de 25 × 12 cm en realidad ocupa 26,5 × 13,5 cm en la pared. Cuanto más gruesa la junta, menos piezas entran por m².',
            ],
            [
                'q' => '¿Sirve para ladrillo hueco?',
                'a' => 'Todavía no: las medidas de cara del ladrillo hueco varían más entre olerías que las del ladrillo común, y no tenemos una medida de referencia publicada para no inventar un número. Si sabés la medida exacta de tu proveedor, escribinos la cantidad estimada en el formulario y lo verificamos con él.',
            ],
            [
                'q' => '¿El 5 % de roturas alcanza para cualquier obra?',
                'a' => 'Es un supuesto de manual, más bajo que el 10 % de una mezcla porque acá no hay agua ni compactación de por medio. Si tu obra tiene muchos cortes (vanos, esquinas, instalaciones embutidas), conviene pedir un margen mayor.',
            ],
            [
                'q' => '¿Cuánto cuesta el ladrillo o el bloque que necesito?',
                'a' => 'No publicamos precios porque cambian con la cantidad de piezas, si es ladrillo común o bloque, el flete hasta tu zona y el momento en que pidas. Cargá las piezas que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'ladrillo-comun',
        'cta_quantity_template' => '{piezas} piezas',
        'cta_button'            => 'Cotizá estas {piezas} piezas →',
    ],

    'revoque-y-mortero' => [
        'keyword' => 'cuánta cal cemento y arena por m2 de revoque',
        'name'    => 'Cuánta cal, cemento y arena por m² de revoque',
        'status'  => 'activa',
        'published' => '2026-09-13',
        'updated'   => '2026-09-13',
        'order'   => 4,
        'title'   => 'Calculadora de revoque: cal, cemento y arena | Paraguay',
        'meta'    => 'Calculá cuánta cal hidratada, cuántas bolsas de cemento y cuánta arena lleva tu revoque con mortero de cal, por m² y espesor.',
        'intro'   => 'Cargá los m² a revocar y el espesor, y mirá cuánta cal hidratada, cemento y arena necesitás para un mortero de cal.',

        'inputs' => [
            [
                'id'      => 'm2',
                'label'   => '¿Cuántos metros cuadrados vas a revocar?',
                'unit'    => 'm²',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 5000,
                'step'    => 1,
                'default' => 30,
            ],
            [
                'id'      => 'espesor',
                'label'   => '¿Qué espesor de revoque?',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 0.5,
                'max'     => 5,
                'step'    => 0.5,
                'default' => 1.5,
            ],
        ],

        'outputs' => [
            ['id' => 'bolsas', 'label' => 'Bolsas de cemento de 50 kg', 'unit' => 'bolsas'],
            ['id' => 'cal',    'label' => 'Cal hidratada',              'unit' => 'kg'],
            ['id' => 'arena',  'label' => 'Arena',                      'unit' => 'm³'],
        ],

        'formula_note' => 'Metros cuadrados × espesor en metros da el volumen de mortero; con un 10 % de desperdicio se aplica la dosificación 1:1:6 (cemento:cal:arena) de CONTENT-SPEC §12.1.',

        'assumptions' => [
            'Mortero con cal 1:1:6 (cemento:cal:arena): 200 kg de cemento y 100 kg de cal por m³ de mortero.',
            '1,05 m³ de arena por m³ de mortero.',
            'Desperdicio del 10 % sobre el volumen teórico.',
            'La dosificación de tu obra la define quien la calcula. Estas proporciones son las de manual; si tu maestro o tu calculista usa otra, cambiá la proporción y rehacé la cuenta.',
        ],

        'related' => [
            'cal-hidratada',
            'cemento',
            'arena-lavada',
            'cemento-y-cal',
            'como-revocar-una-pared',
        ],

        'faq' => [
            [
                'q' => '¿Por qué lleva cal además de cemento?',
                'a' => 'La cal le da plasticidad y trabajabilidad al mortero, y reduce la fisuración del revoque cuando se seca. Un mortero sin cal (1:4, sólo cemento y arena) también sirve para revocar, pero es más rígido: esa variante la resuelve la calculadora "bolsas de cemento por m²".',
            ],
            [
                'q' => '¿Qué espesor es normal para un revoque?',
                'a' => 'El revoque grueso suele ir de 1,5 a 2 cm; encima puede ir una capa fina de terminación de pocos milímetros, que esta calculadora no separa. Si tu obra lleva dos manos bien diferenciadas, sumá el espesor total de ambas.',
            ],
            [
                'q' => '¿Sirve también para asentar mampostería?',
                'a' => 'La misma dosificación 1:1:6 se usa para asentar ladrillos y bloques, pero ahí el volumen no se calcula por m² de pared sino por el espesor real de la junta entre piezas — para eso conviene la calculadora "ladrillos o bloques por m²" y el criterio de tu maestro de obra.',
            ],
            [
                'q' => '¿Cuánto cuesta la cal y el cemento que necesito?',
                'a' => 'No publicamos precios porque cambian con la cantidad de bolsas y kilos, el flete hasta tu zona y el momento en que pidas. Cargá la cal, el cemento y la arena que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'cal-hidratada',
        'cta_quantity_template' => '{cal} kg de cal hidratada, {bolsas} bolsas de cemento y {arena} m³ de arena',
        'cta_button'            => 'Cotizá estos materiales →',
    ],

    'durlock-por-m2' => [
        'keyword' => 'cuántas placas de durlock por m2',
        'name'    => 'Cuántas placas de yeso y perfiles por m²',
        'status'  => 'activa',
        'published' => '2026-09-22',
        'updated'   => '2026-09-22',
        'order'   => 6,
        'title'   => 'Calculadora de placas de yeso y perfiles | Paraguay',
        'meta'    => 'Calculá cuántas placas de yeso, montantes y soleras lleva tu tabique en seco según el largo, el alto y la separación de perfiles, con la cuenta explicada.',
        'intro'   => 'Cargá el largo y el alto del tabique, si lleva placa de un lado o de los dos y la separación entre montantes, y mirá cuántas placas y barras de perfil necesitás.',

        'inputs' => [
            [
                'id'      => 'largo',
                'label'   => '¿Cuánto mide el tabique de largo?',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 0.5,
                'max'     => 200,
                'step'    => 0.01,
                'default' => 4,
            ],
            [
                'id'      => 'alto',
                'label'   => '¿Qué alto tiene?',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 0.5,
                'max'     => 10,
                'step'    => 0.01,
                'default' => 2.6,
            ],
            [
                'id'      => 'caras',
                'label'   => '¿Lleva placa de un lado o de los dos?',
                'unit'    => '',
                'type'    => 'select',
                'default' => 'dos',
                'options' => [
                    ['value' => 'una', 'label' => 'Una cara (revestimiento de un solo lado)'],
                    ['value' => 'dos', 'label' => 'Dos caras (tabique divisorio, placa de los dos lados)'],
                ],
            ],
            [
                'id'      => 'separacion',
                'label'   => '¿Cada cuánto van los montantes?',
                'unit'    => '',
                'type'    => 'select',
                'default' => '40',
                'options' => [
                    ['value' => '40', 'label' => 'Cada 40 cm (tabique más firme)'],
                    ['value' => '60', 'label' => 'Cada 60 cm'],
                ],
            ],
            [
                'id'      => 'largo_placa',
                'label'   => 'Largo de la placa que te ofrece el proveedor (el ancho es 1,20 m)',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 5,
                'step'    => 0.01,
                'default' => 2.4,
            ],
            [
                'id'      => 'largo_barra',
                'label'   => 'Largo de barra de perfil que vende tu proveedor',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 8,
                'step'    => 0.01,
                'default' => 2.6,
            ],
            [
                'id'      => 'desperdicio',
                'label'   => '¿Cuánto desperdicio de placa por cortes?',
                'unit'    => '%',
                'type'    => 'number',
                'min'     => 0,
                'max'     => 30,
                'step'    => 1,
                'default' => 10,
            ],
        ],

        'outputs' => [
            ['id' => 'placas',          'label' => 'Placas de yeso de 1,20 m de ancho', 'unit' => 'placas'],
            ['id' => 'm2',              'label' => 'Superficie a cubrir con placa (sin desperdicio)', 'unit' => 'm²'],
            ['id' => 'montantes',       'label' => 'Montantes',                          'unit' => 'unidades'],
            ['id' => 'barras_montante', 'label' => 'Barras de montante',                 'unit' => 'barras'],
            ['id' => 'barras_solera',   'label' => 'Barras de solera (arriba y abajo)',  'unit' => 'barras'],
        ],

        'formula_note' => 'Largo × alto × caras da los m² de placa; con el desperdicio se dividen por la superficie de una placa (1,20 m × su largo), y los montantes salen de repartir el largo cada 40 o 60 cm, más uno de cierre, con dos líneas de solera a lo largo del tabique.',

        'assumptions' => [
            'Placa de 1,20 m de ancho (la medida estándar publicada en la página de placa de yeso); el largo es el que te ofrece tu proveedor.',
            'Una sola capa de placa por cara.',
            'Desperdicio de placa del 10 % por cortes y encuentros: es un ejemplo, cambialo si tu tabique tiene muchos vanos o casi ninguno.',
            'Un montante al principio del tabique y después uno cada 40 o 60 cm, siempre con uno de cierre en el otro extremo.',
            'Si el alto supera el largo de la barra, cada montante se arma con más de una barra empalmada.',
            'Solera arriba y abajo a lo largo de todo el tabique; los refuerzos de vanos de puertas y ventanas no están incluidos.',
            'Tornillos, masilla y cinta de juntas no se calculan acá: pedíselos al proveedor con los m² de placa.',
        ],

        'related' => [
            'placa-de-yeso',
            'perfiles-para-durlock',
            'yeso-y-durlock',
            'como-hacer-un-computo-metrico',
            'de-que-depende-el-costo-de-construir-en-paraguay',
        ],

        'faq' => [
            [
                'q' => '¿Sirve para calcular un cielorraso de durlock?',
                'a' => 'Las placas sí, si cargás el largo y el ancho del ambiente como largo y alto y elegís una cara. Los perfiles no: el cielorraso lleva otra estructura (perímetro, vigas y velas colgadas), que esta calculadora no resuelve. Para esa parte pasale al proveedor los m² y el perímetro del ambiente.',
            ],
            [
                'q' => '¿40 o 60 cm entre montantes?',
                'a' => 'Las dos separaciones son habituales. Con montantes cada 40 cm el tabique queda más firme y suena menos hueco; cada 60 cm lleva menos perfiles. Si vas a colgar muebles o el tabique es alto, conviene la separación más cerrada: confirmalo con quien arma el tabique.',
            ],
            [
                'q' => '¿Por qué no calcula tornillos, masilla ni cinta?',
                'a' => 'Porque el rendimiento de cada uno cambia según el producto y la forma de trabajar del colocador, y no vamos a inventar un número. Pedíselos al proveedor con los m² de placa que te da la calculadora: con ese dato te arma la tornillería y la masilla que corresponden.',
            ],
            [
                'q' => '¿Cuánto cuesta el durlock que necesito?',
                'a' => 'No publicamos precios porque cambian con la cantidad de placas y de barras que lleves, el tipo de placa (común, resistente a la humedad o al fuego), el espesor, el flete hasta tu zona y el momento en que pidas. Cargá las placas y los perfiles que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'placa-de-yeso',
        'cta_quantity_template' => '{placas} placas de yeso ({m2} m² de tabique), {barras_montante} barras de montante y {barras_solera} barras de solera',
        'cta_button'            => 'Cotizá estas {placas} placas y perfiles →',
    ],


    'membrana-por-m2' => [
        'keyword' => 'cuántos rollos de membrana por m2',
        'name'    => 'Cuántos rollos de membrana por m²',
        'status'  => 'activa',
        'published' => '2026-09-22',
        'updated'   => '2026-09-22',
        'order'   => 7,
        'title'   => 'Calculadora de rollos de membrana por m² | Paraguay',
        'meta'    => 'Calculá cuántos rollos de membrana asfáltica necesitás para tu losa: m² a cubrir con las subidas por los bordes, solape entre paños y desperdicio.',
        'intro'   => 'Cargá los m² de la losa, el perímetro con subida, el solape y lo que cubre cada rollo según su etiqueta, y mirá cuántos rollos de membrana necesitás.',

        'inputs' => [
            [
                'id'      => 'm2',
                'label'   => '¿Cuántos metros cuadrados tiene la losa o el techo?',
                'unit'    => 'm²',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 5000,
                'step'    => 1,
                'default' => 50,
            ],
            [
                'id'      => 'perimetro',
                'label'   => '¿Cuántos metros de borde donde la membrana sube (parapetos, muros)?',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 0,
                'max'     => 2000,
                'step'    => 1,
                'default' => 30,
            ],
            [
                'id'      => 'subida',
                'label'   => '¿Cuánto sube la membrana por esos bordes (babeta)?',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 0,
                'max'     => 100,
                'step'    => 5,
                'default' => 20,
            ],
            [
                'id'      => 'rollo_m2',
                'label'   => '¿Cuántos m² trae cada rollo? (figura en la etiqueta)',
                'unit'    => 'm²',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 100,
                'step'    => 0.5,
                'default' => 10,
            ],
            [
                'id'      => 'ancho',
                'label'   => '¿Qué ancho tiene el rollo? (figura en la etiqueta)',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 0.5,
                'max'     => 2,
                'step'    => 0.05,
                'default' => 1,
            ],
            [
                'id'      => 'solape',
                'label'   => '¿Cuánto se superpone un paño con el siguiente?',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 0,
                'max'     => 30,
                'step'    => 1,
                'default' => 10,
            ],
            [
                'id'      => 'desperdicio',
                'label'   => '¿Qué margen sumás por cortes y remates?',
                'unit'    => '%',
                'type'    => 'number',
                'min'     => 0,
                'max'     => 30,
                'step'    => 1,
                'default' => 5,
            ],
        ],

        'outputs' => [
            ['id' => 'm2_cubrir',   'label' => 'Superficie a cubrir (con subidas)',               'unit' => 'm²'],
            ['id' => 'm2_membrana', 'label' => 'Membrana necesaria (con solape y desperdicio)',   'unit' => 'm²'],
            ['id' => 'rollos',      'label' => 'Rollos de membrana',                              'unit' => 'rollos'],
        ],

        'formula_note' => 'A los m² de la losa se les suma el perímetro por la altura de la subida; ese total se multiplica por el ancho del rollo dividido por su ancho útil (ancho menos solape) y por el margen de desperdicio, y se divide por los m² de cada rollo, redondeando hacia arriba.',

        'assumptions' => [
            'Los m² por rollo, el ancho del rollo y el solape vienen cargados como ejemplo: los reales dependen del espesor y del fabricante, y figuran en la etiqueta o te los da el proveedor.',
            'Los m² por rollo son la superficie bruta del rollo (ancho × largo), sin descontar el solape: el solape se calcula aparte.',
            'El solape que se cuenta es el lateral, entre un paño y el siguiente; los empalmes en las puntas de cada rollo y los recortes de los remates entran en el margen de desperdicio (5 % de ejemplo).',
            'La subida por los bordes se suma como una franja de membrana del largo del perímetro y del alto que cargás.',
        ],

        'related' => [
            'membrana-asfaltica',
            'membrana-liquida',
            'impermeabilizantes',
            'como-impermeabilizar-una-losa',
        ],

        'faq' => [
            [
                'q' => '¿Por qué no alcanza con dividir los m² de la losa por lo que trae el rollo?',
                'a' => 'Porque la membrana no se coloca a tope: cada paño se superpone con el siguiente, y en los bordes sube por el parapeto o el muro. Esas dos cosas agregan metros que no están en la superficie de la losa, y si no las contás te faltan rollos a mitad de obra.',
            ],
            [
                'q' => '¿De dónde saco los m² por rollo y el ancho?',
                'a' => 'De la etiqueta del rollo o de la ficha que te pase el proveedor. Dependen del espesor: a mayor espesor, menos metros por rollo. Los valores que trae cargados la calculadora son un ejemplo para que veas la cuenta, no un dato de ninguna membrana en particular.',
            ],
            [
                'q' => '¿Y si la etiqueta ya da un rendimiento con el solape descontado?',
                'a' => 'Entonces poné el solape en 0, para no contarlo dos veces. La calculadora espera la superficie bruta del rollo y le suma el solape aparte; si el dato ya viene neto, el solape ya está adentro.',
            ],
            [
                'q' => '¿Cuánto cuesta la membrana para mi losa?',
                'a' => 'No publicamos precios porque cambian con el espesor, los metros cuadrados por rollo, la cantidad de rollos con el solape incluido, el flete hasta tu obra y el momento en que pidas. Cargá los m² a cubrir y los rollos que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'membrana-asfaltica',
        'cta_quantity_template' => '{m2_cubrir} m² a cubrir con subidas, unos {rollos} rollos de membrana',
        'cta_button'            => 'Cotizá estos {rollos} rollos →',
    ],


    'ceramica-por-m2' => [
        'keyword' => 'cuántas cajas de cerámica por m2',
        'name'    => 'Cuántas cajas de cerámica o porcelanato por m²',
        'status'  => 'activa',
        'published' => '2026-09-22',
        'updated'   => '2026-09-22',
        'order'   => 8,
        'title'   => 'Calculadora de cajas de cerámica y porcelanato | Paraguay',
        'meta'    => 'Calculá cuántas cajas de cerámica o porcelanato y cuántas bolsas de adhesivo necesitás según los m², los m² por caja y el tipo de colocación.',
        'intro'   => 'Cargá los m² del ambiente, los m² que trae cada caja y cómo vas a colocar, y mirá cuántas cajas y cuántas bolsas de adhesivo pedir.',

        'inputs' => [
            [
                'id'      => 'm2',
                'label'   => '¿Cuántos metros cuadrados tiene el ambiente?',
                'unit'    => 'm²',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 5000,
                'step'    => 0.5,
                'default' => 18,
            ],
            [
                'id'      => 'm2_caja',
                'label'   => '¿Cuántos m² trae cada caja? (figura en la caja)',
                'unit'    => 'm²',
                'type'    => 'number',
                'min'     => 0.1,
                'max'     => 10,
                'step'    => 0.01,
                'default' => 1.44,
            ],
            [
                'id'      => 'colocacion',
                'label'   => '¿Cómo vas a colocar las piezas?',
                'unit'    => '',
                'type'    => 'select',
                'default' => 'recta',
                'options' => [
                    ['value' => 'recta',    'label' => 'Recta (juntas paralelas a las paredes) — 10 % extra'],
                    ['value' => 'diagonal', 'label' => 'Diagonal o en espiga — 15 % extra'],
                ],
            ],
            [
                'id'      => 'adhesivo_kg_m2',
                'label'   => '¿Cuánto adhesivo rinde por m²? (figura en la bolsa)',
                'unit'    => 'kg/m²',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 15,
                'step'    => 0.5,
                'default' => 5,
            ],
            [
                'id'      => 'bolsa_kg',
                'label'   => '¿Cuántos kilos trae la bolsa de adhesivo?',
                'unit'    => 'kg',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 50,
                'step'    => 1,
                'default' => 25,
            ],
        ],

        'outputs' => [
            ['id' => 'm2_compra', 'label' => 'm² a comprar (con desperdicio)', 'unit' => 'm²'],
            ['id' => 'cajas',     'label' => 'Cajas cerradas',                 'unit' => 'cajas'],
            ['id' => 'bolsas',    'label' => 'Bolsas de adhesivo',             'unit' => 'bolsas'],
        ],

        'formula_note' => 'Los m² del ambiente, más el extra por cortes de la colocación elegida, se dividen por los m² de cada caja y se redondean a caja cerrada; el adhesivo sale de los m² del ambiente por el rendimiento de la bolsa.',

        'assumptions' => [
            'Colocación recta: 10 % de piezas extra por cortes en el perímetro, roturas y repuesto; diagonal o espiga: 15 %, porque se corta casi toda la hilera que toca una pared.',
            'Los m² por caja no son un dato fijo: cambian con el formato y el fabricante. El valor cargado (1,44 m², por ejemplo 4 piezas de 60 × 60 cm) es sólo un ejemplo: usá el que figura en tu caja.',
            'Las cajas se venden cerradas: el resultado se redondea siempre a caja entera hacia arriba.',
            'El adhesivo se calcula sobre los m² del ambiente, no sobre el extra: las piezas que sobran no se pegan. El rendimiento (5 kg/m²) y la bolsa (25 kg) son ejemplos: usá los que figuran en tu bolsa.',
            'La pastina no se calcula acá: pedíla con los m² del ambiente y el color de junta.',
        ],

        'related' => [
            'ceramica-para-piso',
            'porcelanato',
            'adhesivo-para-ceramica',
            'azulejos',
            'pisos-y-revestimientos',
        ],

        'faq' => [
            [
                'q' => '¿Dónde veo cuántos m² trae una caja?',
                'a' => 'Está impreso en la caja, junto al formato y la cantidad de piezas; si todavía no la tenés, pedíselo al proveedor al cotizar. No hay un número fijo: dos cajas del mismo formato pueden traer distinta cantidad de piezas según el fabricante.',
            ],
            [
                'q' => '¿Por qué la diagonal pide más cajas que la colocación recta?',
                'a' => 'Porque en diagonal casi toda pieza que toca una pared se corta en ángulo, y buena parte de ese recorte no se puede usar en otro lado. En la colocación recta sólo se corta la última hilera contra cada pared. Por eso esta calculadora suma 15 % en lugar de 10 %: es un supuesto, y si tu ambiente tiene muchas ochavas o columnas conviene pedir un poco más.',
            ],
            [
                'q' => '¿Sirve para porcelanato y para revestimiento de pared?',
                'a' => 'Sí: la cuenta de cajas es la misma para cerámica, porcelanato o azulejo, porque todos se venden por m² en caja cerrada. Lo que cambia es el adhesivo: el porcelanato pide el reforzado, y su rendimiento por m² es el que figura en esa bolsa, no en la del común.',
            ],
            [
                'q' => '¿Cuánto cuesta la cerámica o el porcelanato que necesito?',
                'a' => 'No publicamos precios porque cambian con el formato y la calidad de la pieza, si es cerámica o porcelanato, los m² —que se venden en caja cerrada—, el adhesivo que lleve, el flete hasta tu zona y el momento en que pidas. Cargá las cajas y los m² que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'ceramica-para-piso',
        'cta_quantity_template' => '{cajas} cajas cerradas ({m2_compra} m² con desperdicio) y {bolsas} bolsas de adhesivo',
        'cta_button'            => 'Cotizá estas {cajas} cajas →',
    ],


    'chapas-para-techo' => [
        'keyword' => 'cuántas chapas para un techo',
        'name'    => 'Cuántas chapas necesita tu techo',
        'status'  => 'activa',
        'published' => '2026-09-22',
        'updated'   => '2026-09-22',
        'order'   => 9,
        'title'   => 'Calculadora de chapas para techo: cuántas pedir | Paraguay',
        'meta'    => 'Calculá cuántas chapas y cuántos metros lineales lleva tu techo a 1 o 2 aguas, con el largo del faldón y el ancho útil de la chapa que te ofrecen.',
        'intro'   => 'Cargá las aguas del techo, el largo y el ancho de cada faldón y el ancho útil de la chapa, y mirá cuántas chapas y cuántos metros lineales pedir.',

        'inputs' => [
            [
                'id'      => 'faldones',
                'label'   => '¿Cuántas aguas tiene el techo?',
                'unit'    => '',
                'type'    => 'select',
                'default' => 'dos',
                'options' => [
                    ['value' => 'una', 'label' => '1 agua (un solo faldón)'],
                    ['value' => 'dos', 'label' => '2 aguas (dos faldones iguales)'],
                ],
            ],
            [
                'id'      => 'largo',
                'label'   => '¿Cuánto mide el faldón de cumbrera a alero? (sobre la pendiente, con el vuelo del alero)',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 0.5,
                'max'     => 30,
                'step'    => 0.1,
                'default' => 4.5,
            ],
            [
                'id'      => 'ancho',
                'label'   => '¿Cuánto mide el faldón a lo largo del alero?',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 0.5,
                'max'     => 200,
                'step'    => 0.1,
                'default' => 8.5,
            ],
            [
                'id'      => 'ancho_util',
                'label'   => '¿Qué ancho útil tiene la chapa que te ofrecen? (te lo da el proveedor; 1 m es sólo un ejemplo)',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 0.3,
                'max'     => 1.5,
                'step'    => 0.01,
                'default' => 1,
            ],
            [
                'id'      => 'desperdicio',
                'label'   => '¿Cuántas chapas de más querés sumar por golpes y cortes?',
                'unit'    => '%',
                'type'    => 'number',
                'min'     => 0,
                'max'     => 30,
                'step'    => 1,
                'default' => 5,
            ],
        ],

        'outputs' => [
            ['id' => 'por_faldon',  'label' => 'Chapas por faldón',                 'unit' => 'chapas'],
            ['id' => 'chapas',      'label' => 'Chapas en total (con el margen)',   'unit' => 'chapas'],
            ['id' => 'largo_chapa', 'label' => 'Largo de cada chapa',               'unit' => 'm'],
            ['id' => 'metros',      'label' => 'Metros lineales de chapa',          'unit' => 'm'],
            ['id' => 'm2',          'label' => 'Superficie de techo',               'unit' => 'm²'],
        ],

        'formula_note' => 'El ancho del faldón dividido por el ancho útil de la chapa, redondeado hacia arriba, da las chapas por faldón; eso se multiplica por la cantidad de faldones, se le suma el margen de chapas de más y cada chapa se pide al largo del faldón.',

        'assumptions' => [
            'Ancho útil: el que te informa el proveedor para el perfil que te ofrece, ya descontado el solape lateral con la chapa vecina. El 1 m que aparece por defecto es sólo un ejemplo.',
            'Cada chapa cubre el faldón entero, de cumbrera a alero y sin empalmes: el largo de cada chapa es el largo del faldón.',
            'Faldones rectangulares; en 2 aguas, los dos faldones iguales.',
            'Margen del 5 % de chapas de más por golpes en el manejo y cortes, sobre el total y redondeado hacia arriba. Es un supuesto: cambialo si tu techo tiene más cortes.',
            'No incluye cumbreras, babetas ni tornillería autoperforante: pedílas junto con las chapas.',
        ],

        'related' => [
            'chapa-de-zinc',
            'chapa-trapezoidal',
            'chapa-termoacustica',
            'chapas-y-techos',
            'que-chapa-conviene-para-techo',
        ],

        'faq' => [
            [
                'q' => '¿Qué es el ancho útil y por qué no uso el ancho total de la chapa?',
                'a' => 'Porque cada chapa se monta solapada con la de al lado, y esa franja que se superpone no cubre techo nuevo. El ancho útil es lo que cubre de verdad una chapa ya colocada, descontado el solape, y cambia según el perfil: pedíselo al proveedor para la chapa que te ofrece y cargalo en la calculadora.',
            ],
            [
                'q' => '¿Cómo mido el largo del faldón?',
                'a' => 'De la cumbrera hasta el borde del alero, sobre la pendiente del techo y con el vuelo del alero incluido. Si en tu plano la medida está en planta (horizontal), el largo sobre la pendiente es algo mayor: pedile a quien hizo el plano esa medida inclinada. Si el faldón es más largo que la chapa más larga que te pueden entregar, consultá con el proveedor dónde conviene el empalme.',
            ],
            [
                'q' => '¿Sirve para un techo a cuatro aguas o con faldones distintos?',
                'a' => 'Para faldones distintos, calculá cada uno por separado con la opción de 1 agua y sumá los resultados. En un techo a cuatro aguas los faldones no son rectangulares y las chapas de las esquinas se cortan en diagonal: usá la calculadora como punto de partida, subí el margen de chapas de más y pedile al proveedor que revise la cuenta con el plano.',
            ],
            [
                'q' => '¿Incluye tornillos, cumbrera y babetas?',
                'a' => 'No. La tornillería autoperforante, las cumbreras y las babetas se cotizan aparte de la chapa, y cada perfil trae su propia recomendación de fijación. Pedílas junto con las chapas en la misma cotización para no fraccionar el flete.',
            ],
            [
                'q' => '¿Cuánto cuesta la chapa para mi techo?',
                'a' => 'No publicamos precios porque cambian con si el proveedor cotiza por chapa o por metro lineal, el calibre, el perfil, el largo de cada chapa, la cantidad, el flete hasta tu zona y el momento en que pidas. Cargá las chapas y el largo que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'chapa-trapezoidal',
        'cta_quantity_template' => '{chapas} chapas de {largo_chapa} m de largo ({metros} metros lineales, {m2} m² de techo)',
        'cta_button'            => 'Cotizá estas {chapas} chapas →',
    ],


    'tanque-de-agua-litros' => [
        'keyword' => 'de cuántos litros el tanque de agua',
        'name'    => 'De cuántos litros tiene que ser el tanque de agua',
        'status'  => 'activa',
        'published' => '2026-09-22',
        'updated'   => '2026-09-22',
        'order'   => 10,
        'title'   => 'Calculadora de litros del tanque de agua | Paraguay',
        'meta'    => 'Calculá de cuántos litros tiene que ser tu tanque de agua según cuántas personas viven, el consumo por día y los días de reserva que querés tener.',
        'intro'   => 'Cargá cuántas personas viven en la casa, cuánta agua usa cada una por día y cuántos días de reserva querés, y mirá cuántos litros tiene que guardar el tanque.',

        'inputs' => [
            [
                'id'      => 'personas',
                'label'   => '¿Cuántas personas viven en la casa?',
                'unit'    => 'personas',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 50,
                'step'    => 1,
                'default' => 4,
            ],
            [
                'id'      => 'consumo',
                'label'   => '¿Cuánta agua usa cada persona por día?',
                'unit'    => 'litros por persona por día',
                'type'    => 'number',
                'min'     => 10,
                'max'     => 500,
                'step'    => 10,
                'default' => 150,
            ],
            [
                'id'      => 'dias',
                'label'   => '¿Cuántos días de reserva querés tener?',
                'unit'    => 'días',
                'type'    => 'number',
                'min'     => 0.5,
                'max'     => 7,
                'step'    => 0.5,
                'default' => 1,
            ],
            [
                'id'      => 'margen',
                'label'   => '¿Qué margen extra querés sumar?',
                'unit'    => '%',
                'type'    => 'number',
                'min'     => 0,
                'max'     => 100,
                'step'    => 5,
                'default' => 10,
            ],
        ],

        'outputs' => [
            ['id' => 'litros',       'label' => 'Litros de reserva necesarios',    'unit' => 'litros'],
            ['id' => 'tanques_1000', 'label' => 'Tanques de 1000 litros que suman', 'unit' => 'tanques'],
            ['id' => 'tanques_500',  'label' => 'Tanques de 500 litros que suman',  'unit' => 'tanques'],
        ],

        'formula_note' => 'Personas × litros por persona por día × días de reserva da el agua que tiene que guardar el tanque; con el margen extra sumado, esos litros se dividen por 1000 o por 500 y se cuentan tanques enteros hacia arriba.',

        'assumptions' => [
            'Consumo por defecto de 150 litros por persona por día: es un valor de referencia habitual de diseño en los manuales de instalaciones sanitarias para vivienda, no una medición de tu casa. Ajustalo: más baños, lavarropas o riego lo suben.',
            'Los días de reserva son los que querés cubrir cuando se corta el servicio o falta presión de la red; 1 día es sólo el ejemplo de partida.',
            'Margen extra del 10 % como ejemplo, para no dejar el litraje ajustado al mínimo; ponelo en 0 si no lo querés.',
            'Los tanques se cuentan enteros hacia arriba con los litrajes de 1000 y 500 litros que figuran en la página del tanque de agua.',
            'Un litro de agua pesa alrededor de 1 kg: la base tiene que aguantar el tanque lleno, no sólo vacío.',
        ],

        'related' => [
            'tanque-de-agua',
            'cano-de-agua',
            'canos-y-plomeria',
        ],

        'faq' => [
            [
                'q' => '¿De dónde sale el consumo de 150 litros por persona por día?',
                'a' => 'Es un valor de referencia que usan los manuales de instalaciones sanitarias para dimensionar la reserva de una vivienda. No es un dato de tu casa: si sabés cuánto consumen (por la factura del agua o porque tienen lavarropas, varios baños o riego), cargá tu propio número y la cuenta se rehace sola.',
            ],
            [
                'q' => '¿Por qué no pide cuántos baños hay?',
                'a' => 'Porque los baños pesan a través del consumo: más baños y más duchas suelen significar más litros por persona por día. Si la casa tiene varios baños o mucha gente que se ducha a diario, subí el consumo por persona en vez de sumar otra variable.',
            ],
            [
                'q' => '¿Cuántos días de reserva conviene tener?',
                'a' => 'Depende de cuánto duran los cortes o la falta de presión en tu zona. La cuenta es lineal: dos días de reserva duplican los litros de uno. Cargá los días que querés cubrir y mirá cuántos tanques suman.',
            ],
            [
                'q' => '¿Cuánto cuesta el tanque de agua que necesito?',
                'a' => 'No publicamos precios porque cambian con el litraje, cuántos tanques lleves, las capas del tanque, el flete hasta tu zona y el momento en que pidas, y porque la base, el flotante y las conexiones se cotizan aparte. Cargá los litros y los tanques que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'tanque-de-agua',
        'cta_quantity_template' => 'Tanque de agua para {litros} litros de reserva ({tanques_1000} × 1000 L o {tanques_500} × 500 L)',
        'cta_button'            => 'Cotizá tanques para {litros} litros →',
    ],


    'hierro-para-columnas' => [
        'keyword' => 'cuánto hierro lleva una columna',
        'name'    => 'Cuánto hierro lleva una columna: barras, estribos y kilos',
        'status'  => 'activa',
        'published' => '2026-09-22',
        'updated'   => '2026-09-22',
        'order'   => 5,
        'title'   => 'Calculadora de hierro para columnas | Paraguay',
        'meta'    => 'Pasá la planilla de tu plano a barras de 12 m y kilos: hierro longitudinal y estribos de tus columnas, con la cuenta explicada y cotización en un paso.',
        'intro'   => 'Cargá los datos de tu plano —columnas, altura, sección, barras, diámetros y estribos— y mirá cuántas barras de 12 m y cuántos kilos de hierro pedir.',

        // Conversor de planilla, no cálculo estructural: todo lo que define el calculista es
        // un input, y los valores por defecto son sólo un ejemplo de plano.
        'inputs' => [
            [
                'id'      => 'columnas',
                'label'   => '¿Cuántas columnas iguales vas a armar?',
                'unit'    => 'columnas',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 500,
                'step'    => 1,
                'default' => 8,
            ],
            [
                'id'      => 'altura',
                'label'   => '¿Qué altura tiene cada columna? (la del plano)',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 10,
                'step'    => 0.05,
                'default' => 3,
            ],
            [
                'id'      => 'lado_a',
                'label'   => 'Lado A de la sección',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 12,
                'max'     => 100,
                'step'    => 1,
                'default' => 20,
            ],
            [
                'id'      => 'lado_b',
                'label'   => 'Lado B de la sección',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 12,
                'max'     => 100,
                'step'    => 1,
                'default' => 30,
            ],
            [
                'id'      => 'barras',
                'label'   => '¿Cuántas barras longitudinales por columna? (según tu plano)',
                'unit'    => 'barras',
                'type'    => 'number',
                'min'     => 4,
                'max'     => 24,
                'step'    => 1,
                'default' => 4,
            ],
            [
                'id'      => 'diam_long',
                'label'   => 'Diámetro de las barras longitudinales (según tu plano)',
                'unit'    => '',
                'type'    => 'select',
                'default' => '12',
                'options' => [
                    ['value' => '8',  'label' => 'Hierro de 8 mm'],
                    ['value' => '10', 'label' => 'Hierro de 10 mm'],
                    ['value' => '12', 'label' => 'Hierro de 12 mm'],
                    ['value' => '16', 'label' => 'Hierro de 16 mm'],
                    ['value' => '20', 'label' => 'Hierro de 20 mm'],
                ],
            ],
            [
                'id'      => 'empalme',
                'label'   => 'Empalme o anclaje extra por barra longitudinal (0 si tu planilla ya trae el largo de corte)',
                'unit'    => 'm',
                'type'    => 'number',
                'min'     => 0,
                'max'     => 2,
                'step'    => 0.05,
                'default' => 0.5,
            ],
            [
                'id'      => 'diam_estribo',
                'label'   => 'Diámetro de los estribos (según tu plano)',
                'unit'    => '',
                'type'    => 'select',
                'default' => '6',
                'options' => [
                    ['value' => '6', 'label' => 'Hierro de 6 mm'],
                    ['value' => '8', 'label' => 'Hierro de 8 mm'],
                ],
            ],
            [
                'id'      => 'separacion',
                'label'   => '¿Cada cuánto van los estribos? (según tu plano)',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 5,
                'max'     => 40,
                'step'    => 0.5,
                'default' => 15,
            ],
            [
                'id'      => 'recubrimiento',
                'label'   => 'Recubrimiento de hormigón (según tu plano)',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 1,
                'max'     => 5,
                'step'    => 0.5,
                'default' => 2.5,
            ],
            [
                'id'      => 'ganchos',
                'label'   => 'Largo de los ganchos, sumando los dos extremos de cada estribo',
                'unit'    => 'cm',
                'type'    => 'number',
                'min'     => 0,
                'max'     => 40,
                'step'    => 1,
                'default' => 10,
            ],
        ],

        'outputs' => [
            ['id' => 'kg_long',        'label' => 'Hierro longitudinal',                      'unit' => 'kg'],
            ['id' => 'barras_long',    'label' => 'Barras de 12 m para el hierro longitudinal', 'unit' => 'barras'],
            ['id' => 'estribos',       'label' => 'Estribos en total',                        'unit' => 'unidades'],
            ['id' => 'largo_estribo',  'label' => 'Largo de corte de cada estribo',           'unit' => 'cm'],
            ['id' => 'barras_estribo', 'label' => 'Barras de 12 m para los estribos',         'unit' => 'barras'],
            ['id' => 'kg_estribos',    'label' => 'Hierro de estribos',                       'unit' => 'kg'],
            ['id' => 'kg_total',       'label' => 'Hierro total',                             'unit' => 'kg'],
        ],

        'formula_note' => 'El largo total de barras longitudinales y de estribos, con un 10 % extra por cortes, se divide por 12 m para sacar las barras y se multiplica por el peso por metro de cada diámetro (≈ 0,00617 × d²) para sacar los kilos.',

        'assumptions' => [
            'No es un cálculo estructural: diámetros, cantidad de barras, separación de estribos, recubrimiento, ganchos y empalme los define tu plano o tu calculista. Los valores precargados son sólo un ejemplo de plano.',
            'Peso del acero: 7.850 kg/m³ × área de la barra, o sea ≈ 0,00617 × d² kg por metro (d en mm): 6 mm 0,222 · 8 mm 0,395 · 10 mm 0,617 · 12 mm 0,888 · 16 mm 1,578 · 20 mm 2,466 kg/m.',
            'Barra comercial de 12 m.',
            '10 % extra por cortes sobre el largo total, en barras y en kilos.',
            'Estribo cerrado medido por fuera: perímetro de la sección menos el recubrimiento en cada cara, más los ganchos.',
            'Estribos a separación pareja en toda la altura, más uno por columna. Si tu plano los junta cerca de vigas y losas, calculá esos tramos aparte con la separación menor.',
            'No incluye el alambre de atar, los separadores ni el hierro de vigas y zapatas.',
        ],

        'related' => [
            'varilla-de-hierro',
            'alambre-negro',
            'hierro',
            'cuanto-hierro-lleva-una-columna',
            'que-diametro-de-hierro-para-que-uso',
        ],

        'faq' => [
            [
                'q' => '¿Esta calculadora me dice cuánto hierro necesita mi columna?',
                'a' => 'No. Cuántas barras, de qué diámetro y cada cuánto van los estribos lo define el cálculo estructural de tu obra. La calculadora toma esos datos de tu plano o de la planilla de tu calculista y los pasa a barras de 12 m y kilos para pedir cotización.',
            ],
            [
                'q' => '¿Cuántos kilos pesa una barra de hierro de 12 metros?',
                'a' => 'Por geometría, con acero de 7.850 kg/m³: la de 6 mm pesa unos 2,7 kg, la de 8 mm 4,7 kg, la de 10 mm 7,4 kg, la de 12 mm 10,7 kg, la de 16 mm 18,9 kg y la de 20 mm 29,6 kg. Es el peso teórico: la barra real puede variar un poco por la tolerancia de fabricación.',
            ],
            [
                'q' => '¿Por qué suma 10 % por cortes?',
                'a' => 'Porque las piezas casi nunca entran justas en una barra de 12 m: de una barra salen tres piezas de 3,50 m y sobra 1,50 m que no sirve para otra columna. Es un supuesto, no una ley: si tus largos de corte aprovechan bien la barra podés pedir menos, y si hay muchos largos distintos conviene más.',
            ],
            [
                'q' => '¿Qué hago si los estribos van más juntos cerca de la viga?',
                'a' => 'Es lo habitual cuando el plano los pide así: cerca de los nudos la separación se achica. Hacé la cuenta por tramos —la zona de estribos juntos con su separación y su altura, y el resto con la separación normal— o cargá la separación menor en toda la altura para quedar del lado seguro.',
            ],
            [
                'q' => '¿Cuánto cuesta el hierro para las columnas?',
                'a' => 'No publicamos precios porque cambian con si comprás por barra o por kilo, la cantidad que lleves, el diámetro de cada varilla, el flete hasta tu zona y el momento en que pidas. Cargá las barras de 12 m de cada diámetro, o los kilos, que te dio la calculadora en el formulario y hasta 3 proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.',
            ],
        ],

        'cta_material'          => 'varilla-de-hierro',
        'cta_quantity_template' => '{barras_long} barras de 12 m para longitudinales y {barras_estribo} barras de 12 m para estribos ({kg_total} kg en total)',
        'cta_button'            => 'Cotizá estos {kg_total} kg de hierro →',
    ],

];
