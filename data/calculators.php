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
];
