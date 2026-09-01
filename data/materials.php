<?php
/**
 * data/materials.php — una entrada por CONCEPTO de material. La clave del array ES el slug.
 *
 * Reglas (plan §2 y §5):
 *  - Namespace de slugs compartido con data/categories.php; el smoke test exige unicidad.
 *  - UNA página por concepto. Los sinónimos ("6ta", "piedra triturada sexta") viven en
 *    synonyms[] y se usan en la copy, nunca como página propia (canibalización).
 *  - Las variantes por medida (diámetros de varilla, 4ta/5ta/6ta de piedra triturada) van
 *    como tabla dentro de la página, no como páginas separadas.
 *  - price_band es INTERNO: alimenta los rangos de "presupuesto estimado" del formulario y
 *    nunca se renderiza como lista de precios (plan §8.7).
 *  - 'category' debe existir en data/categories.php. Un material en categoría 'proxima' no
 *    puede estar 'activa' (lo verifica el smoke test).
 *
 * Fase 1 siembra slug, name, category, status, title y meta. synonyms, sale_unit, price_band,
 * faq, related y schema se completan en la fase 3.
 */

declare(strict_types=1);

/** Plantilla de campos vacíos que la fase 3 completa. */
$pending = [
    'synonyms'   => [],
    'sale_unit'  => '',
    'price_band' => '',
    'faq'        => [],
    'related'    => [],
    'schema'     => [],
];

$materials = [
    // ---- hierro -------------------------------------------------------------------
    'varilla-de-hierro' => ['category' => 'hierro', 'name' => 'Varilla de hierro', 'status' => 'activa',
        'title' => 'Varilla de hierro en Paraguay | Precio por barra',
        'meta'  => 'Varillas del 6 al 16 para columnas, vigas y losas. Decinos cuántas barras necesitás y hasta 3 proveedores verificados te cotizan por WhatsApp.'],
    'malla-electrosoldada' => ['category' => 'hierro', 'name' => 'Malla electrosoldada', 'status' => 'activa',
        'title' => 'Malla electrosoldada en Paraguay | Cotizá por panel',
        'meta'  => 'Malla para contrapisos, losas y cerramientos. Pasanos la medida y los m² y hasta 3 proveedores verificados te pasan precio el mismo día.'],
    'alambre-negro' => ['category' => 'hierro', 'name' => 'Alambre negro', 'status' => 'activa',
        'title' => 'Alambre negro para obra en Paraguay | Cotizá kilos',
        'meta'  => 'Alambre recocido para atar armaduras y encofrados. Decinos cuántos kilos necesitás y proveedores verificados te cotizan por WhatsApp.'],
    'clavos' => ['category' => 'hierro', 'name' => 'Clavos', 'status' => 'activa',
        'title' => 'Clavos para construcción en Paraguay | Precio por kilo',
        'meta'  => 'Clavos de punta París y para encofrado, por kilo o por caja. Pedí tu cotización y hasta 3 proveedores verificados te escriben por WhatsApp.'],
    'perfiles-metalicos' => ['category' => 'hierro', 'name' => 'Perfiles metálicos', 'status' => 'activa',
        'title' => 'Perfiles metálicos en Paraguay | Cotizá tu estructura',
        'meta'  => 'Caños estructurales, ángulos, perfiles C y U para techos y estructuras. Pasanos el listado y proveedores verificados te cotizan sin vueltas.'],

    // ---- cemento y cal --------------------------------------------------------------
    'cemento' => ['category' => 'cemento-y-cal', 'name' => 'Cemento', 'status' => 'activa',
        'title' => 'Cemento en Paraguay | Precio por bolsa de 50 kg',
        'meta'  => 'Cemento en bolsa para obra, con entrega en tu zona. Decinos cuántas bolsas necesitás y hasta 3 proveedores verificados te pasan precio hoy.'],
    'cal-viva' => ['category' => 'cemento-y-cal', 'name' => 'Cal viva', 'status' => 'activa',
        'title' => 'Cal viva en Paraguay | Cotizá por bolsa',
        'meta'  => 'Cal viva para mezcla y estabilización de suelos. Pedí tu cotización y hasta 3 proveedores verificados te escriben por WhatsApp.'],
    'cal-hidratada' => ['category' => 'cemento-y-cal', 'name' => 'Cal hidratada', 'status' => 'activa',
        'title' => 'Cal hidratada en Paraguay | Precio por bolsa',
        'meta'  => 'Cal hidratada para revoques y asentado de mampostería. Decinos la cantidad y proveedores verificados te cotizan con flete a tu obra.'],
    'hormigon-elaborado' => ['category' => 'cemento-y-cal', 'name' => 'Hormigón elaborado', 'status' => 'activa',
        'title' => 'Hormigón elaborado en Paraguay | Cotizá por m³',
        'meta'  => 'Hormigón listo con mixer y bombeo para losas y platea. Pasanos los m³ y la fecha, y hasta 3 hormigoneras verificadas te cotizan.'],
    'adhesivo-para-ceramica' => ['category' => 'cemento-y-cal', 'name' => 'Adhesivo para cerámica', 'status' => 'activa',
        'title' => 'Adhesivo para cerámica en Paraguay | Precio por bolsa',
        'meta'  => 'Pegamento para cerámica, porcelanato y piedra. Decinos cuántos m² vas a colocar y proveedores verificados te pasan precio por bolsa.'],

    // ---- áridos ---------------------------------------------------------------------
    'arena-lavada' => ['category' => 'aridos', 'name' => 'Arena lavada', 'status' => 'activa',
        'title' => 'Arena lavada en Paraguay | Precio por m³ con flete',
        'meta'  => 'Arena lavada para hormigón y revoque fino, con entrega en obra. Decinos los m³ y tu zona: hasta 3 proveedores verificados te cotizan.'],
    'arena-gorda' => ['category' => 'aridos', 'name' => 'Arena gorda', 'status' => 'activa',
        'title' => 'Arena gorda en Paraguay | Cotizá por m³',
        'meta'  => 'Arena gruesa para asentar mampostería y contrapisos. Pasanos la cantidad y la zona y proveedores verificados te pasan precio con flete.'],
    'ripio' => ['category' => 'aridos', 'name' => 'Ripio', 'status' => 'activa',
        'title' => 'Ripio en Paraguay | Precio por m³ con entrega',
        'meta'  => 'Ripio para hormigón, contrapisos y caminos. Decinos cuántos m³ necesitás y hasta 3 proveedores verificados te cotizan por WhatsApp.'],
    'piedra-triturada' => ['category' => 'aridos', 'name' => 'Piedra triturada', 'status' => 'activa',
        'title' => 'Piedra triturada en Paraguay | 4ta, 5ta y 6ta',
        'meta'  => 'Piedra triturada 4ta, 5ta y 6ta para hormigón y bases. Pedí tu cotización por m³ y proveedores verificados te escriben el mismo día.'],
    'piedra-bruta' => ['category' => 'aridos', 'name' => 'Piedra bruta', 'status' => 'activa',
        'title' => 'Piedra bruta en Paraguay | Precio por m³ para cimientos',
        'meta'  => 'Piedra bruta para cimientos y muros de contención, con entrega en obra. Decinos los m³ y hasta 3 proveedores verificados te cotizan.'],
    'tierra-gorda' => ['category' => 'aridos', 'name' => 'Tierra gorda', 'status' => 'activa',
        'title' => 'Tierra gorda en Paraguay | Cotizá por camionada',
        'meta'  => 'Tierra gorda para relleno y nivelación de terreno. Pasanos la cantidad y tu zona y proveedores verificados te pasan precio con flete.'],
    'escombro-relleno' => ['category' => 'aridos', 'name' => 'Escombro para relleno', 'status' => 'activa',
        'title' => 'Escombro para relleno en Paraguay | Cotizá el flete',
        'meta'  => 'Escombro para relleno y nivelación, por camionada. Decinos qué volumen necesitás y proveedores verificados te cotizan con entrega.'],

    // ---- ladrillos y bloques ---------------------------------------------------------
    'ladrillo-comun' => ['category' => 'ladrillos-y-bloques', 'name' => 'Ladrillo común', 'status' => 'activa',
        'title' => 'Ladrillo común en Paraguay | Precio por millar',
        'meta'  => 'Ladrillo común para mampostería, por millar y con flete a tu obra. Decinos cuántos necesitás y hasta 3 proveedores verificados te cotizan.'],
    'ladrillo-hueco' => ['category' => 'ladrillos-y-bloques', 'name' => 'Ladrillo hueco', 'status' => 'activa',
        'title' => 'Ladrillo hueco en Paraguay | Cotizá por millar',
        'meta'  => 'Ladrillo hueco de 6, 8 y 12 para paredes y tabiques. Pasanos la cantidad y tu zona: proveedores verificados te pasan precio por WhatsApp.'],
    'ladrillo-prensado' => ['category' => 'ladrillos-y-bloques', 'name' => 'Ladrillo prensado', 'status' => 'activa',
        'title' => 'Ladrillo prensado en Paraguay | Precio por millar',
        'meta'  => 'Ladrillo prensado a la vista para fachadas y muros. Pedí tu cotización por millar y hasta 3 proveedores verificados te responden hoy.'],
    'bloque-de-hormigon' => ['category' => 'ladrillos-y-bloques', 'name' => 'Bloque de hormigón', 'status' => 'activa',
        'title' => 'Bloque de hormigón en Paraguay | Cotizá por unidad',
        'meta'  => 'Bloques de 10, 15 y 20 cm para muros y cercos. Decinos cuántos m² vas a levantar y proveedores verificados te cotizan con flete.'],
    'tejuelon' => ['category' => 'ladrillos-y-bloques', 'name' => 'Tejuelón', 'status' => 'activa',
        'title' => 'Tejuelón en Paraguay | Precio por m² de losa',
        'meta'  => 'Tejuelón para losas y techos tradicionales. Pasanos los m² de losa y hasta 3 proveedores verificados te pasan precio por WhatsApp.'],

    // ---- chapas y techos --------------------------------------------------------------
    'chapa-de-zinc' => ['category' => 'chapas-y-techos', 'name' => 'Chapa de zinc', 'status' => 'activa',
        'title' => 'Chapa de zinc en Paraguay | Precio por metro',
        'meta'  => 'Chapa de zinc acanalada para techos, en varios espesores y largos. Decinos los metros y proveedores verificados te cotizan el mismo día.'],
    'chapa-trapezoidal' => ['category' => 'chapas-y-techos', 'name' => 'Chapa trapezoidal', 'status' => 'activa',
        'title' => 'Chapa trapezoidal en Paraguay | Cotizá por metro',
        'meta'  => 'Chapa trapezoidal para techos de galpón y vivienda. Pasanos los metros lineales y hasta 3 proveedores verificados te pasan precio.'],
    'chapa-termoacustica' => ['category' => 'chapas-y-techos', 'name' => 'Chapa termoacústica', 'status' => 'activa',
        'title' => 'Chapa termoacústica (isopanel) en Paraguay | Cotizá',
        'meta'  => 'Chapa termoacústica tipo isopanel para techos aislados. Decinos los m² y el espesor y proveedores verificados te cotizan por WhatsApp.'],
    'teja-espanola' => ['category' => 'chapas-y-techos', 'name' => 'Teja española', 'status' => 'activa',
        'title' => 'Teja española en Paraguay | Precio por m² de techo',
        'meta'  => 'Teja española cerámica para techos a dos aguas. Pasanos los m² y hasta 3 proveedores verificados te pasan precio con flete a tu obra.'],
    'teja-francesa' => ['category' => 'chapas-y-techos', 'name' => 'Teja francesa', 'status' => 'activa',
        'title' => 'Teja francesa en Paraguay | Cotizá por m²',
        'meta'  => 'Teja francesa para techos de vivienda, por m² o por unidad. Pedí tu cotización y proveedores verificados te escriben por WhatsApp.'],
    'fibrocemento' => ['category' => 'chapas-y-techos', 'name' => 'Chapa de fibrocemento', 'status' => 'activa',
        'title' => 'Chapa de fibrocemento en Paraguay | Precio por chapa',
        'meta'  => 'Chapas de fibrocemento para techos de galpón y depósito. Decinos la medida y la cantidad: proveedores verificados te cotizan sin vueltas.'],

    // ---- madera (ola 2 — categoría 'proxima', ver plan §5) ------------------------------
    'tirantes' => ['category' => 'madera', 'name' => 'Tirantes', 'status' => 'proxima',
        'title' => 'Tirantes de madera en Paraguay | Cotizá por metro',
        'meta'  => 'Tirantes para estructura de techo, en varias escuadrías. Pasanos el listado y proveedores verificados te pasan precio por WhatsApp.'],
    'puntales' => ['category' => 'madera', 'name' => 'Puntales', 'status' => 'proxima',
        'title' => 'Puntales de madera en Paraguay | Precio por unidad',
        'meta'  => 'Puntales para apuntalar losas y encofrados. Decinos cuántos necesitás y por cuánto tiempo, y proveedores verificados te cotizan.'],
    'tabla-de-encofrado' => ['category' => 'madera', 'name' => 'Tabla de encofrado', 'status' => 'proxima',
        'title' => 'Tabla de encofrado en Paraguay | Cotizá por tabla',
        'meta'  => 'Tablas para encofrado de losas, vigas y columnas. Pasanos la medida y la cantidad y proveedores verificados te pasan precio hoy.'],
    'terciada' => ['category' => 'madera', 'name' => 'Terciada', 'status' => 'proxima',
        'title' => 'Terciada (fenólico) en Paraguay | Precio por placa',
        'meta'  => 'Placas de terciada y fenólico para encofrado y carpintería. Decinos el espesor y la cantidad: proveedores verificados te cotizan.'],
    'machimbre' => ['category' => 'madera', 'name' => 'Machimbre', 'status' => 'proxima',
        'title' => 'Machimbre en Paraguay | Cotizá por m² de cielorraso',
        'meta'  => 'Machimbre para cielorrasos y revestimientos de madera. Pasanos los m² y hasta 3 proveedores verificados te pasan precio por WhatsApp.'],
    'listones' => ['category' => 'madera', 'name' => 'Listones', 'status' => 'proxima',
        'title' => 'Listones de madera en Paraguay | Precio por metro',
        'meta'  => 'Listones para clavaderas, cielorrasos y trabajos de obra. Decinos la escuadría y los metros y proveedores verificados te cotizan.'],
];

// Completa los campos que siembra la fase 3, sin repetirlos en cada entrada.
foreach ($materials as $slug => $material) {
    $materials[$slug] = $material + $pending;
}

return $materials;
