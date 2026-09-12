# Fase 14 — Endurecimiento técnico (Sonnet, `phase/14-tech-hardening`)

Fecha: 2026-09-12 · plan §11.6, decisiones §1.24–1.26

## Built
- `.htaccess`: bloque `mod_headers` con `X-Frame-Options`, `Permissions-Policy`
  (`Strict-Transport-Security` queda comentada con la instrucción de habilitarla recién con
  el SSL confirmado; sin CSP, ver nota en el propio archivo); bloque `mod_deflate` nuevo
  (html/css/xml/js/svg/json); `mod_expires` suma `font/woff2`. El bloque de reescrituras no
  se tocó.
- **Fuentes autohospedadas** (`assets/fonts/*.woff2`): Bricolage Grotesque y Inter, sólo
  latin + latin-ext, 4 archivos, **196 KB en total** (dentro del presupuesto de 200 KB).
  Bricolage Grotesque son fuentes variables (un mismo archivo woff2 sirve los pesos 500 y
  600 vía el eje `wght`, confirmado comparando los CSS de Google Fonts); pinneé el eje
  óptico (`opsz`) a 14 en vez de dejarlo como rango 12..96 — eso solo bajó el archivo de
  ~77 KB a ~41 KB, y es lo que permitió entrar en el presupuesto. `@font-face` nuevo en
  `assets/css/site.css`, `<link rel="preload">` de los dos archivos "latin" en
  `partials/header.php`, y se sacaron los `<link>`/`preconnect` de Google Fonts.
- `sitemap.php`: `lastmod` sale ahora SÓLO de la clave opcional `updated` (YYYY-MM-DD) de
  cada entrada de datos — nunca de `filemtime()`. Documentada en el comentario de cabecera
  de `categories.php`, `materials.php` y `guides.php` (ya estaba en `calculators.php`).
  Ninguna entrada trae `updated` todavía: el sitemap sigue sin `lastmod` hasta que alguien lo
  cargue a mano en una entrada real.
- `partials/header.php`: `og:type` pasa a `article` en las páginas de detalle de guía y
  calculadora (deducido del `body_class` ya existente, sin agregar una clave nueva a
  `page()`); `Cache-Control: no-cache` en toda respuesta PHP (va en el header, no en
  `.htaccess`, porque `.htaccess` sólo controla estáticos).
- `404.php` + `partials/header.php`: el 404 ya no emite `<link rel="canonical">` ni
  `og:url` — `page()` acepta `canonical => ''` y el header omite ambas etiquetas cuando está
  vacío.
- `tools/replay-leads.php` (nuevo): CLI idempotente por `idempotency_key` que reintenta los
  leads con `outcome: 'fallo_crm'` de `storage/leads.log`, anota el resultado en
  `storage/replayed.log`, soporta `--dry-run` y (sólo para el fixture de CI) `--log=`/
  `--replayed=`. `DEPLOY.md` documenta la línea de cron horaria.
- `tests/mobile-overflow.mjs` (nuevo, NO corre en CI): Playwright con viewport real en
  320/360/390/1280px sobre 6 rutas representativas (home, dos materiales, guías índice, una
  calculadora, proveedores); guarda capturas sólo si encuentra desborde, en
  `docs/screenshots/` (gitignored).
- `tools/smoke.php`: un unit nuevo para `replay-leads.php` con un fixture en un directorio
  temporal (nunca toca `storage/leads.log` real): confirma que un `fallo_crm` pendiente
  aparece en `--dry-run` y que deja de aparecer una vez que su `idempotency_key` ya figura
  como reenviado con éxito en `replayed.log`.

## Decisions
- Bricolage Grotesque con `opsz` fijo en 14 en vez de variable 12..96: pierde el ajuste
  automático de proporciones entre texto chico y titulares muy grandes, pero es lo que bajó
  el peso del archivo lo suficiente para cumplir el presupuesto de 200 KB del plan. Si el
  peso deja de importar (HTTP/2 ya lo sirve comprimido y cacheado 30 días), se puede volver
  a pedir el rango completo sin tocar nada más que esos dos archivos.
- El "`ladrillo hueco`" que apareció en la fase 13 sigue sin resolver (ver
  `docs/decisions-needed.md`); esta fase no lo tocó.
- `og:type` se dedujo de `body_class` en vez de agregar una clave `type` a `page()`: el plan
  no puso `partials/init.php` en la lista de "Owns" de esta fase, y `body_class` ya
  distingue exactamente los dos casos que hacían falta (`page-guia`, `page-calculadora`).

## Known issues
- Ninguna entrada de datos tiene todavía la clave `updated`: hasta que se cargue una a mano
  en una entrada real, el sitemap sigue sin `lastmod` para esa URL (comportamiento esperado,
  no un bug).
- `tests/mobile-overflow.mjs` necesita Playwright accesible (`npm i -D playwright` o
  `NODE_PATH=$(npm root -g)` si ya está instalado global) — no se agregó un `package.json`
  al repo porque el plan exige "no Node" para el sitio en sí (§1.1); esta herramienta es de
  QA local, no de build.

## Verification
`find . -name '*.php' | xargs -n1 php -l` limpio · `php tools/smoke.php` → SMOKE OK (con el
unit nuevo de replay-leads) · `./tools/render-check.sh` OK sin cambios · `tests/mobile-
overflow.mjs` corrido a mano: 24/24 combinaciones de ruta×ancho sin desborde, más las 9
páginas nuevas de la fase 13 verificadas aparte a 360/1280px, también sin desborde ·
capturas manuales confirmando que las fuentes autohospedadas cargan y se ven correctas ·
`curl -I` local confirma `Cache-Control: no-cache`, `og:type=article` en guía/calculadora y
`website` en el resto, sin `canonical` ni `og:url` en el 404.
