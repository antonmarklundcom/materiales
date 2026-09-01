<?php
/**
 * data/categories.php — una entrada por categoría. La clave del array ES el slug.
 *
 * Namespace de slugs COMPARTIDO con data/materials.php: ambos se sirven bajo
 * /materiales/{slug}/ (plan §2). El smoke test (tools/smoke.php) falla si un slug se repite
 * entre los dos archivos.
 *
 * status:
 *   'activa'  → página publicada, indexable, entra en sitemap.xml
 *   'proxima' → página renderiza aviso "próximamente" + noindex, NO entra en sitemap
 *
 * Campos de contenido (intro_keywords, faq) se completan en la fase 3. En la fase 1 sólo
 * viven slug, name, status, order, title y meta — title/meta son reales desde ya para que
 * cada placeholder sirva su <title>/<meta> definitivos.
 */

declare(strict_types=1);

return [
    // ---- categorías con captación de proveedores al lanzamiento (plan §5) ----------
    'hierro' => [
        'name'   => 'Hierro',
        'status' => 'activa',
        'order'  => 1,
        'title'  => 'Hierro para construcción en Paraguay | Cotizá gratis',
        'meta'   => 'Varillas, mallas y alambre para obra. Pedí tu cotización y hasta 3 proveedores verificados te escriben por WhatsApp. Asunción y Gran Asunción.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'cemento-y-cal' => [
        'name'   => 'Cemento y cal',
        'status' => 'activa',
        'order'  => 2,
        'title'  => 'Cemento y cal en Paraguay | Pedí tu cotización',
        'meta'   => 'Cemento, cal y hormigón elaborado para tu obra. Cargá tu pedido y hasta 3 proveedores verificados te pasan precio por WhatsApp el mismo día.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'aridos' => [
        'name'   => 'Áridos',
        'status' => 'activa',
        'order'  => 3,
        'title'  => 'Áridos: arena, ripio y piedra en Paraguay',
        'meta'   => 'Arena, ripio, piedra triturada y piedra bruta con entrega en obra. Pedí cotización a hasta 3 proveedores verificados de Gran Asunción.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'ladrillos-y-bloques' => [
        'name'   => 'Ladrillos y bloques',
        'status' => 'activa',
        'order'  => 4,
        'title'  => 'Ladrillos y bloques en Paraguay | Cotizá por millar',
        'meta'   => 'Ladrillo común, hueco, prensado, bloques y tejuelón. Decinos cuántos necesitás y hasta 3 proveedores verificados te cotizan con flete incluido.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'chapas-y-techos' => [
        'name'   => 'Chapas y techos',
        'status' => 'activa',
        'order'  => 5,
        'title'  => 'Chapas y techos en Paraguay | Cotizá tu techo',
        'meta'   => 'Chapa de zinc, trapezoidal, termoacústica, tejas y fibrocemento. Pasanos los metros y hasta 3 proveedores verificados te cotizan por WhatsApp.',
        'intro_keywords' => [],
        'faq'    => [],
    ],

    // ---- expansión: contenido en olas posteriores (orden del plan §5) -------------
    'madera' => [
        'name'   => 'Madera',
        'status' => 'proxima',
        'order'  => 6,
        'title'  => 'Madera para construcción en Paraguay | Cotizá',
        'meta'   => 'Tirantes, puntales, tablas de encofrado, terciada y machimbre. Pedí tu cotización y te contactan proveedores verificados de tu zona.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'aberturas' => [
        'name'   => 'Aberturas',
        'status' => 'proxima',
        'order'  => 7,
        'title'  => 'Aberturas en Paraguay: puertas y ventanas',
        'meta'   => 'Puertas, ventanas y portones de aluminio, madera o PVC. Contanos qué necesitás y proveedores verificados te pasan precio por WhatsApp.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'canos-y-plomeria' => [
        'name'   => 'Caños y plomería',
        'status' => 'proxima',
        'order'  => 8,
        'title'  => 'Caños y plomería en Paraguay | Pedí precio',
        'meta'   => 'Caños de PVC, accesorios, tanques y grifería para tu obra. Pedí tu cotización y proveedores verificados te responden por WhatsApp.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'pisos-y-revestimientos' => [
        'name'   => 'Pisos y revestimientos',
        'status' => 'proxima',
        'order'  => 9,
        'title'  => 'Pisos y revestimientos en Paraguay | Cotizá m²',
        'meta'   => 'Cerámica, porcelanato, piso flotante y revestimientos. Decinos cuántos m² necesitás y proveedores verificados te cotizan sin vueltas.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'electricidad' => [
        'name'   => 'Electricidad',
        'status' => 'proxima',
        'order'  => 10,
        'title'  => 'Materiales eléctricos en Paraguay | Cotizá',
        'meta'   => 'Cables, tableros, caños corrugados y llaves térmicas para obra. Pedí tu cotización y proveedores verificados te escriben por WhatsApp.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'pinturas' => [
        'name'   => 'Pinturas',
        'status' => 'proxima',
        'order'  => 11,
        'title'  => 'Pinturas para obra en Paraguay | Pedí cotización',
        'meta'   => 'Látex, esmalte, impermeabilizantes de techo y accesorios. Contanos los m² a pintar y proveedores verificados te pasan precio por balde.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'yeso-y-durlock' => [
        'name'   => 'Yeso y durlock',
        'status' => 'proxima',
        'order'  => 12,
        'title'  => 'Yeso y durlock en Paraguay | Cotizá tu obra',
        'meta'   => 'Placas de durlock, perfiles, yeso y masillas. Decinos cuántas placas necesitás y proveedores verificados te cotizan por WhatsApp.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
    'impermeabilizantes' => [
        'name'   => 'Impermeabilizantes',
        'status' => 'proxima',
        'order'  => 13,
        'title'  => 'Impermeabilizantes en Paraguay | Pedí precio',
        'meta'   => 'Membranas, pinturas asfálticas y aditivos para techos y cimientos. Pedí tu cotización y proveedores verificados te responden el mismo día.',
        'intro_keywords' => [],
        'faq'    => [],
    ],
];
