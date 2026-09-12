# Fase 15 — Link pass + imágenes + QA de lanzamiento (Sonnet, `phase/15-link-pass-launch`)

Fecha: 2026-09-12 · plan §11.7 — última fase de la ventana Sonnet 13→15 y del build completo.

## Built
- **Enlace editorial en las 75 páginas que todavía no tenían ninguno**: las 64 páginas de
  material y las 11 de categoría ahora llevan al menos un link real dentro de la prosa
  (nunca una lista "ver también") a una guía o calculadora temáticamente relevante — un
  script verificó al final que 0 páginas de material o categoría quedaron sin ese enlace.
  El mapeo de qué página enlaza a qué guía/calculadora salió de las relaciones ya existentes
  en `data/guides.php`/`data/calculators.php` (`related[]`) cuando las había, y de un
  criterio editorial (mismo rubro, mismo tipo de decisión de compra) para el resto — el
  enlace en prosa no está limitado a las relaciones de datos, así que se pudo, por ejemplo,
  enlazar `pintura-antihumedad` y `selladores-y-siliconas` a la guía
  `como-impermeabilizar-una-losa` aunque esa guía no los lista en su `related[]`.
- Trabajo repartido en 6 subagentes Sonnet en paralelo por grupo de categorías (cada uno con
  la lista exacta de archivo → destino → texto de ancla sugerido), un verify por grupo y un
  verify final propio sobre las 75 páginas juntas.
- **Imágenes**: NO se generó ninguna. `curl` contra `*.cloudfront.net` sigue devolviendo
  `connect_rejected` (política del proxy del entorno) — mismo bloqueo que KNOWN-ISSUES #22,
  reverificado en esta fase antes de intentar nada, sin gastar créditos de Higgsfield.
- **QA de lanzamiento** sobre los tipos de página nuevos de las fases 9–14 (home, `/proveedores/`,
  calculadoras, las 6 guías nuevas): un `<h1>` por página, `canonical` correcto (o ausente en
  el 404), JSON-LD válido (decodificado con `json_decode` en home/calculadora/guía/proveedores),
  títulos/metas dentro de los límites del smoke, sin precios ni marcas fuera de la lista
  cerrada §11.2 (verificado con grep sobre los 75 archivos tocados), voseo. `tests/mobile-
  overflow.mjs` corrido de nuevo tras los cambios de contenido: sin desborde.
- **Housekeeping**: KNOWN-ISSUES #24–26 (ladrillo hueco sin medida, bloque de hormigón con
  medida de manual, fotografía todavía bloqueada) y #14 marcado resuelto (fuentes ya
  autohospedadas desde la fase 14). `STATUS.md` con las 15 fases en ✅. `README.md` con las
  secciones "Calculadoras" y "Para proveedores". `docs/log/README.md` con el índice de los 7
  archivos de fase 9–15.

## Decisions
- El enlace editorial se resolvió por criterio temático, no estrictamente por `related[]`:
  las relaciones de datos alimentan los bloques automáticos de "relacionados" (fase 11), y
  el enlace en prosa de esta fase es un sistema aparte y deliberadamente más flexible —
  documentado así para que una fase futura no intente sincronizar ambos.
- No se re-intentó `higgsfield-image-pipeline`: el bloqueo de red es a nivel de política del
  proxy del entorno (`connect_rejected`), no un 403 puntual del CDN — reintentarlo no iba a
  cambiar el resultado y hubiera arriesgado créditos sin necesidad.

## Known issues
- Ver KNOWN-ISSUES #24–26 (ladrillo hueco, bloque de hormigón, fotografía).
- `data/*.php` sigue sin ninguna entrada con la clave `updated` (fase 14): el sitemap sigue
  sin `lastmod` en todas las URLs hasta que alguien la cargue a mano.

## Verification
`find . -name '*.php' | xargs -n1 php -l` limpio · `php tools/smoke.php` → SMOKE OK ·
`./tools/render-check.sh` OK · `tests/mobile-overflow.mjs` sin desborde tras los cambios de
contenido · script propio: 0/64 materiales y 0/11 categorías sin enlace a `/guias/` o
`/calculadoras/` · conteo de palabras: las 75 páginas tocadas siguen dentro de sus rangos
(materiales 350–600, categorías 250–450) · grep de marcas cerradas y de precios/moneda sobre
los 75 archivos: sin violaciones nuevas · JSON-LD decodificado sin errores en home,
`/proveedores/`, una calculadora y una guía.

## Cierre del build (fases 1–15)

Con esta fase se completan las 15 fases de `plan.md`. No queda trabajo de código o
contenido pendiente de ninguna fase — el informe de cierre para Anton va en la PR de esta
fase, con el checklist de lo que sigue siendo manual (§7 del plan): NAP real, credenciales
de VenderCRM/GA4/Pixel, DNS, `staging_noindex`, y la fotografía real bloqueada por el
entorno. Próximo insumo de planificación sugerido: el segundo pull de Keyword Planner
(`KEYWORDS-MATERIALES.md` §6).
