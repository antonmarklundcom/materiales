<?php
/**
 * data/guides.php — guías informativas servidas en /guias/{slug}/ (plan §5).
 *
 * Namespace de slugs PROPIO (no comparte espacio con /materiales/{slug}/), pero igual debe
 * ser único dentro de este archivo. Cada guía enlaza a sus páginas de dinero con anchor
 * descriptivo — sin contenido huérfano.
 *
 * Nota de desviación (plan §9): el plan §2 no lista un archivo de datos para guías, pero el
 * router de /guias/ y el sitemap necesitan una fuente. Mismo formato que categories.php.
 * La prosa llega en content/guias/{slug}.php (olas 1 y 2); acá sólo slug, name, status,
 * title y meta.
 */

declare(strict_types=1);

return [
    'cuantas-bolsas-de-cemento-por-m2' => [
        'name'   => 'Cuántas bolsas de cemento por m²',
        'status' => 'proxima',
        'order'  => 1,
        'title'  => 'Cuántas bolsas de cemento por m² | Cálculo rápido',
        'meta'   => 'Cuántas bolsas de cemento necesitás por m² de contrapiso, revoque o losa, con ejemplos de obra en Paraguay. Después pedí tu cotización.',
        'related' => ['cemento', 'arena-lavada', 'ripio'],
    ],
    'que-piedra-usar-para-cimientos' => [
        'name'   => 'Qué piedra usar para cimientos',
        'status' => 'proxima',
        'order'  => 2,
        'title'  => 'Qué piedra usar para cimientos | Guía práctica',
        'meta'   => 'Piedra bruta o triturada para cimientos: cuál conviene según el suelo y el tipo de obra. Comparación clara y cotización en un paso.',
        'related' => ['piedra-bruta', 'piedra-triturada', 'aridos'],
    ],
    'ladrillo-comun-vs-hueco' => [
        'name'   => 'Ladrillo común vs hueco',
        'status' => 'proxima',
        'order'  => 3,
        'title'  => 'Ladrillo común vs hueco: cuál conviene',
        'meta'   => 'Diferencias reales entre ladrillo común y hueco en costo, peso, aislación y mano de obra. Elegí bien y pedí tu cotización por millar.',
        'related' => ['ladrillo-comun', 'ladrillo-hueco', 'ladrillos-y-bloques'],
    ],
    'que-chapa-conviene-para-techo' => [
        'name'   => 'Qué chapa conviene para techo',
        'status' => 'proxima',
        'order'  => 4,
        'title'  => 'Qué chapa conviene para tu techo | Comparativa',
        'meta'   => 'Zinc, trapezoidal o termoacústica: qué chapa conviene según el techo, el calor y el presupuesto. Después cotizá los metros que necesitás.',
        'related' => ['chapa-de-zinc', 'chapa-trapezoidal', 'chapa-termoacustica'],
    ],
    'cuanta-arena-y-ripio-por-m3-de-hormigon' => [
        'name'   => 'Cuánta arena y ripio por m³ de hormigón',
        'status' => 'proxima',
        'order'  => 5,
        'title'  => 'Arena y ripio por m³ de hormigón | Cálculo',
        'meta'   => 'Cuánta arena, ripio y cemento entran en un m³ de hormigón, con las dosificaciones que se usan en obra en Paraguay. Cotizá los m³ que faltan.',
        'related' => ['arena-lavada', 'ripio', 'hormigon-elaborado'],
    ],
    'que-diametro-de-hierro-para-que-uso' => [
        'name'   => 'Qué diámetro de hierro para qué uso',
        'status' => 'proxima',
        'order'  => 6,
        'title'  => 'Qué diámetro de hierro usar en cada parte de la obra',
        'meta'   => 'Del 6 al 16: qué varilla va en columnas, vigas, losas y estribos según la obra. Guía clara y cotización con proveedores verificados.',
        'related' => ['varilla-de-hierro', 'malla-electrosoldada', 'hierro'],
    ],
];
