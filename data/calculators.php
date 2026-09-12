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
        'order'   => 1,
        'title'   => 'Calculadora: bolsas de cemento por m²',
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
    ],

    'hormigon-por-m3' => [
        'keyword' => 'cuánto cemento arena y ripio por m3 de hormigón',
        'name'    => 'Cuánto cemento, arena y ripio por m³ de hormigón',
        'status'  => 'activa',
        'order'   => 2,
        'title'   => 'Calculadora: cemento, arena y ripio por m³',
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
    ],

    'ladrillos-por-m2' => [
        'keyword' => 'cuántos ladrillos por m2',
        'name'    => 'Cuántos ladrillos o bloques por m²',
        'status'  => 'activa',
        'order'   => 3,
        'title'   => 'Calculadora: ladrillos o bloques por m²',
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
    ],

    'revoque-y-mortero' => [
        'keyword' => 'cuánta cal cemento y arena por m2 de revoque',
        'name'    => 'Cuánta cal, cemento y arena por m² de revoque',
        'status'  => 'activa',
        'order'   => 4,
        'title'   => 'Calculadora: cal, cemento y arena por m²',
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
    ],
];
