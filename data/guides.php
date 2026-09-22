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
 *
 * Clave OPCIONAL `image` (fase 11, decisión §1.20): ruta relativa al docroot de una foto
 * JPG 1200×630 de hasta 150 KB, p. ej. 'assets/img/cat/hierro.jpg'. Se usa para el héroe
 * (`<picture>`) y el `og:image` de la página. Por defecto hay UNA foto por categoría: un
 * material sin `image` propia hereda la de su categoría y una guía la de su primera página
 * de dinero. Si la clave no está, o el archivo todavía no se subió, la página se renderiza
 * exactamente como hoy. `tools/smoke.php` falla sólo cuando se DECLARA una ruta cuyo
 * archivo no existe. Los valores los cablea la fase 15.
 *
 * Clave OPCIONAL `updated` (YYYY-MM-DD, fase 14, decisión §1.25): la usa `sitemap.php` para
 * `lastmod`. Nunca se calcula de `filemtime()` (el deploy de Hostinger reescribe mtimes en
 * cada push). Sin la clave, esa URL no lleva `lastmod`.
 */

declare(strict_types=1);

return [
    'cuantas-bolsas-de-cemento-por-m2' => [
        'keyword' => 'cuántas bolsas de cemento por m2',
        'name'   => 'Cómo calcular las bolsas de cemento por m²',
        'status' => 'activa',
        'published' => '2026-09-06',
        'updated'   => '2026-09-22',
        'order'  => 1,
        'title'  => 'Bolsas de cemento por m² en Paraguay: cómo se calcula',
        'meta'   => 'De qué depende cuántas bolsas de cemento van por m² de contrapiso, revoque o mampostería, y cómo pedirle la cantidad justa al proveedor.',
        'related' => ['cemento', 'arena-lavada', 'ripio'],
    ],
    'que-piedra-usar-para-cimientos' => [
        'keyword' => 'qué piedra usar para cimientos',
        'name'   => 'Qué piedra usar para cimientos',
        'status' => 'activa',
        'published' => '2026-09-06',
        'updated'   => '2026-09-15',
        'order'  => 2,
        'title'  => 'Qué piedra usar para cimientos en Paraguay | Guía',
        'meta'   => 'Piedra bruta o triturada para cimientos: cuál conviene según el suelo y el tipo de obra. Comparación clara y cotización en un paso.',
        'related' => ['piedra-bruta', 'piedra-triturada', 'aridos'],
    ],
    'ladrillo-comun-vs-hueco' => [
        'keyword' => 'ladrillo común o hueco',
        'name'   => 'Ladrillo común vs hueco',
        'status' => 'activa',
        'published' => '2026-09-06',
        'updated'   => '2026-09-15',
        'order'  => 3,
        'title'  => 'Ladrillo común vs hueco en Paraguay: cuál conviene',
        'meta'   => 'Diferencias reales entre ladrillo común y hueco en costo, peso, aislación y mano de obra. Elegí bien y pedí tu cotización por millar.',
        'related' => ['ladrillo-comun', 'ladrillo-hueco', 'ladrillos-y-bloques'],
    ],
    'que-chapa-conviene-para-techo' => [
        'keyword' => 'qué chapa conviene para techo',
        'name'   => 'Qué chapa conviene para techo',
        'status' => 'activa',
        'published' => '2026-09-06',
        'updated'   => '2026-09-15',
        'order'  => 4,
        'title'  => 'Qué chapa conviene para techo en Paraguay: tipos',
        'meta'   => 'Zinc, trapezoidal o termoacústica: qué chapa conviene según el techo, el calor y el presupuesto. Después cotizá los metros que necesitás.',
        'related' => ['chapa-de-zinc', 'chapa-trapezoidal', 'chapa-termoacustica'],
    ],
    'cuanta-arena-y-ripio-por-m3-de-hormigon' => [
        'keyword' => 'cuánta arena y ripio por m3 de hormigón',
        'name'   => 'Arena y ripio para hormigón: cómo dosificar un m³',
        'status' => 'activa',
        'published' => '2026-09-06',
        'updated'   => '2026-09-22',
        'order'  => 5,
        'title'  => 'Arena y ripio para hormigón en Paraguay: cómo dosificar',
        'meta'   => 'Cómo se compone un m³ de hormigón, qué arena y qué ripio pedir y cuándo conviene el elaborado, con las dosificaciones de obra en Paraguay.',
        'related' => ['arena-lavada', 'ripio', 'hormigon-elaborado'],
    ],
    'que-diametro-de-hierro-para-que-uso' => [
        'keyword' => 'qué diámetro de hierro usar',
        'name'   => 'Qué diámetro de hierro para qué uso',
        'status' => 'activa',
        'published' => '2026-09-06',
        'updated'   => '2026-09-15',
        'order'  => 6,
        'title'  => 'Diámetros de hierro en Paraguay: cuál usar en cada parte',
        'meta'   => 'Del 6 al 16: qué varilla va en columnas, vigas, losas y estribos según la obra. Guía clara y cotización con proveedores verificados.',
        'related' => ['varilla-de-hierro', 'malla-electrosoldada', 'hierro'],
    ],
    'como-revocar-una-pared' => [
        'keyword' => 'cómo revocar una pared',
        'name'   => 'Cómo revocar una pared',
        'status' => 'activa',
        'published' => '2026-09-06',
        'updated'   => '2026-09-06',
        'order'  => 7,
        'title'  => 'Cómo revocar una pared en Paraguay | Materiales y pasos',
        'meta'   => 'Qué lleva el revoque grueso y el fino, en qué orden va cada capa y cómo calcular el material por m² en obra paraguaya. Después cotizá lo que falte.',
        'related' => ['cal-hidratada', 'arena-lavada', 'cemento'],
    ],
    'losa-de-hormigon-encofrado-y-hierro' => [
        'keyword' => 'losa de hormigón armado',
        'name'   => 'Losa de hormigón: encofrado y hierro',
        'status' => 'activa',
        'published' => '2026-09-06',
        'updated'   => '2026-09-06',
        'order'  => 8,
        'title'  => 'Losa de hormigón en Paraguay: encofrado y hierro',
        'meta'   => 'Cómo se arma una losa de hormigón armado —encofrado, puntales, armadura y hormigonado— y qué pedirle al proveedor de cada material, en qué orden.',
        'related' => ['hormigon-elaborado', 'varilla-de-hierro', 'tabla-de-encofrado'],
    ],
    'de-que-depende-el-costo-de-construir-en-paraguay' => [
        'keyword' => 'cuánto cuesta construir una casa en paraguay',
        'name'   => 'De qué depende el costo de construir en Paraguay',
        'status' => 'activa',
        'published' => '2026-09-13',
        'updated'   => '2026-09-15',
        'order'  => 9,
        'title'  => 'De qué depende el costo de construir en Paraguay',
        'meta'   => 'Los factores reales que mueven el costo de construir una casa en Paraguay: superficie, terminación, rubros y cómo pedir cotización de cada uno.',
        'related' => ['cemento-y-cal', 'hierro', 'ladrillos-y-bloques', 'chapas-y-techos'],
    ],
    'como-hacer-un-computo-metrico' => [
        'keyword' => 'cómo hacer un cómputo métrico',
        'name'   => 'Cómo hacer un cómputo métrico',
        'status' => 'activa',
        'published' => '2026-09-13',
        'updated'   => '2026-09-13',
        'order'  => 10,
        'title'  => 'Cómo hacer un cómputo métrico en Paraguay | Guía',
        'meta'   => 'Qué es un cómputo métrico, en qué orden se hace y cómo pasar de metros cuadrados a cantidades de material para presupuestar tu obra.',
        'related' => ['cemento', 'ladrillo-comun', 'hormigon-elaborado'],
    ],
    'como-elegir-un-corralon' => [
        'keyword' => 'cómo elegir un corralón',
        'name'   => 'Cómo elegir un corralón',
        'status' => 'activa',
        'published' => '2026-09-13',
        'updated'   => '2026-09-13',
        'order'  => 11,
        'title'  => 'Cómo elegir un corralón de materiales en Paraguay',
        'meta'   => 'Qué mirar antes de elegir un corralón o venta de materiales de construcción: stock, flete, forma de pago y cómo comparar cotizaciones.',
        'related' => ['cemento-y-cal', 'aridos', 'ladrillos-y-bloques'],
    ],
    'chapa-o-teja-que-techo-conviene' => [
        'keyword' => 'chapa o teja qué techo conviene',
        'name'   => 'Chapa o teja: qué techo conviene',
        'status' => 'activa',
        'published' => '2026-09-13',
        'updated'   => '2026-09-13',
        'order'  => 12,
        'title'  => 'Chapa o teja en Paraguay: qué techo conviene',
        'meta'   => 'Chapa de zinc, trapezoidal, termoacústica o teja: comparación por calor, ruido, peso de estructura y mantenimiento. Después cotizá los m² que necesitás.',
        'related' => ['chapa-de-zinc', 'teja-espanola', 'chapas-y-techos'],
    ],
    'cuanto-hierro-lleva-una-columna' => [
        'keyword' => 'cuánto hierro lleva una columna',
        'name'   => 'Cuánto hierro lleva una columna',
        'status' => 'activa',
        'published' => '2026-09-13',
        'updated'   => '2026-09-13',
        'order'  => 13,
        'title'  => 'Cuánto hierro lleva una columna en Paraguay | Guía',
        'meta'   => 'Qué define la cantidad de hierro de una columna —diámetro, estribos, recubrimiento— y cómo pedirle la lista de materiales a tu calculista.',
        'related' => ['varilla-de-hierro', 'malla-electrosoldada', 'hierro'],
    ],
    'como-impermeabilizar-una-losa' => [
        'keyword' => 'cómo impermeabilizar una losa',
        'name'   => 'Cómo impermeabilizar una losa',
        'status' => 'activa',
        'published' => '2026-09-13',
        'updated'   => '2026-09-13',
        'order'  => 14,
        'title'  => 'Cómo impermeabilizar una losa en Paraguay | Paso a paso',
        'meta'   => 'Membrana asfáltica, líquida o hidrófugo: qué opción conviene para impermeabilizar una losa y en qué orden van los trabajos previos.',
        'related' => ['membrana-asfaltica', 'membrana-liquida', 'hidrofugo'],
    ],
];
