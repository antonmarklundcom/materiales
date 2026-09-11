# Fase 12 — Base de calculadoras (Opus, `phase/12-calculadoras-foundation`)

Fecha: 2026-09-11 · plan §11.4 · decisión §1.21

## Built
- **CONTENT-SPEC §12 primero**: reglas cerradas de toda calculadora, tabla de dosificaciones
  estándar (§12.1) con la conversión a bolsas explicada (bolsa 50 kg ≈ 0,035 m³, desperdicio
  10 %), redacción cerrada (§12.2) y el esquema del árbol de expresiones que evalúa `calc.js`
  (§12.3). Regla de ampliación: una dosificación nueva se agrega a §12.1 en el mismo PR.
- `data/calculators.php` (nuevo) con el contrato documentado y el exemplar
  `bolsas-de-cemento-por-m2` (inputs m², espesor, tipo; salidas bolsas, arena, ripio).
- `calculadoras/index.php` (nuevo): índice con `ItemList` y ficha con `BreadcrumbList` +
  `FAQPage`, widget, supuestos visibles, prosa, FAQ, relacionadas y el formulario con
  `cemento` preseleccionado. Sin `HowTo` (ya no gana resultados enriquecidos).
- `assets/js/calc.js` (nuevo, ~4 KB): evalúa el árbol de expresiones sin `eval` ni `Function`,
  actualiza salidas en vivo y escribe `cantidad` en el formulario — salvo que el visitante ya
  la haya tipeado a mano, que nunca se pisa.
- `content/calculadoras/bolsas-de-cemento-por-m2.php`: fórmula JSON + ~500 palabras con la
  cuenta en palabras, un ejemplo resuelto y qué pedirle al proveedor.
- Ruteo espejo en `.htaccess` y `tools/router-cli.php`, `/calculadoras/` y cada calculadora
  en el sitemap, enlace "Calculadoras" en nav y pie, bloque CSS `/* == 12 == */`.
- `tools/smoke.php`: forma de la entrada, FAQ 3–5 con la de precio al final, ids de inputs y
  outputs, `cta_quantity_template` sólo con salidas existentes, `related` y `cta_material`
  apuntando a páginas reales, archivo de contenido para las `activa`, el JSON parsea y
  calcula todas las salidas declaradas, y la frase de referencia en la plantilla.
  `tools/render-check.sh`: índice, exemplar, 404, enlaces cruzados y sitemap.

## Decisions
- La fórmula vive en el contenido y no en el JS: un solo `calc.js` sirve a todas las
  calculadoras y la fase 13 (Sonnet) agrega calculadoras sin tocar código.
- Árbol de expresiones con lista blanca de operaciones (`add sub mul div ceil floor round min
  max`) más nodos `var`, `const` y `table`. Cualquier nodo desconocido, división por cero o
  entrada no numérica devuelve `—`: nunca una excepción en pantalla.
- El enlace con la guía se resuelve desde `calculators[].related` (que incluye el slug de la
  guía): así funciona en ambos sentidos sin tocar `data/guides.php`, que es de otra fase.
- El ripio se muestra como salida con valor 0 en revoque y carpeta, y el supuesto lo dice.
  Ocultar una salida según el tipo agregaba estado a `calc.js` sin ganar nada.

## Known issues
- Una sola calculadora publicada: las otras tres son la fase 13.
- El sitemap sigue tomando `lastmod` de `filemtime` (la clave `updated` de §1.25 está
  documentada en `data/calculators.php` pero la cablea la fase 14).

## Verification
`php -l` limpio · `node --check assets/js/calc.js` OK · `php tools/smoke.php` OK (1
calculadora) · `./tools/render-check.sh` OK con los checks nuevos · Playwright: 50 m² / 8 cm /
contrapiso → 22 bolsas, 2,2 m³ de arena y 3,74 m³ de ripio (idéntico al ejemplo resuelto de la
prosa); cambiar a 100 m² duplica; carpeta de 3 cm sobre 100 m² → 30 bolsas y ripio 0; la
`cantidad` del formulario se precarga y deja de pisarse apenas el visitante la tipea; con
JavaScript desactivado se ve el aviso y la página no desborda a 390 px · overflow OK en
360/390/1280.
