# Fase 9 — Home & capa de conversión (Opus, `phase/9-home-conversion`)

Fecha: 2026-09-11 · plan §11.1 · decisiones §1.18, §1.23

## Built
- `index.php` reconstruida: hero partido con franja `.page-hero__facts` (3 cifras calculadas
  del dato: materiales activos, rubros activos, `max_proveedores`), "Cómo funciona" en 3
  pasos, grilla de rubros, prosa nueva, teaser de guías y el formulario sin preselección.
- `content/home/intro.php` (nuevo dir): ~250 palabras con los términos de cabecera y locales
  de KEYWORDS §4.3 (Asunción, Luque, San Lorenzo, Lambaré, Capiatá, *corralón*, *venta de
  materiales de construcción*) en prosa, sin listas de keywords ni páginas de ciudad.
- `partials/cta.php` (nuevo): barra pegajosa sólo en `max-width: 39.99rem`, formulario
  primero y WhatsApp segundo **sólo** si `site('whatsapp')` está cargado. La incluye
  `footer.php` en todas las páginas salvo `/cotizar/` y `/gracias/`.
- `assets/js/motion.js`: IntersectionObserver que esconde la barra mientras `#cotizar` está a
  la vista. Sin JS la barra queda visible.
- CTA en el hero de páginas de dinero: categoría y material anclan a `#cotizar` (el
  formulario ya está en la página, no se pierde la preselección); las guías van a `/cotizar/`.
- `/gracias/`: "Qué pasa ahora" en 3 pasos, WhatsApp secundario condicional y 3 guías.
- `assets/css/site.css`: bloque `/* == 9 == */` (`.steps`, `.cta-bar`, `.gracias__wa`).
- `tools/render-check.sh`: 4 checks nuevos (home con `Cómo funciona` y `name="consentimiento"`,
  `/materiales/hierro/` con `href="#cotizar"`, `data-cta-bar` presente).

## Decisions
- La barra decide entre `#cotizar` y `/cotizar/` por `body_class` (`page-home`,
  `page-categoria`, `page-material` llevan formulario). No se tocó `partials/form.php`, que
  es fundacional (CONTENT-SPEC §10), así que no hay flag global que consultar.
- El CTA del hero de la home pasó de `/cotizar/` a `#cotizar`: la home ahora cierra en el
  formulario y mandar fuera del sitio-propio perdía el scroll.
- La prosa no afirma con cuántos proveedores se trabaja ni en qué corralones: sólo describe
  la cobertura del formulario (regla de cifras inventadas de CONTENT-SPEC).
- `docs/screenshots/` va al `.gitignore`: las capturas son verificación local, no entregable.

## Known issues
- **Auto-merge no se pudo armar** (`enable_pr_auto_merge` → "Protected branch rules not
  configured for this branch"): la rama `main` no tiene protección ni check requerido
  configurados, que es el preflight §7 del plan. Mientras no exista, cada PR de esta ventana
  se mergea a mano después de ver CI en verde. Anton: configurar protección de rama en
  `main` con el check `CI / check` como requerido.
- Ninguno más abierto por esta fase. La barra pegajosa queda con un solo botón hasta que se
  cargue `whatsapp` en `data/site.php` (input humano §7) — es el comportamiento diseñado.

## Verification
`php -l` limpio en todo el repo · `php tools/smoke.php` OK (13 categorías / 11 activas, 64
materiales, 8 guías) · `./tools/render-check.sh` OK incluidos los 4 checks nuevos · Playwright
con viewport real (360/390/1280) sobre `/`, `/materiales/hierro/`, `/materiales/cemento/`,
una guía y `/gracias/`: `scrollWidth === clientWidth` en todos.
