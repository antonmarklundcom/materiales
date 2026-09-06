# Known issues & desviaciones

Cosas menores no bloqueantes, anotadas para no frenar el build (plan §4.3). Cada entrada dice
la fase que la resuelve.

## Fase 1 — Foundation

1. **`data/guides.php` no estaba en el plan §2.** El plan lista `site/categories/materials`,
   pero el router de `/guias/` y el sitemap necesitan una fuente de datos para las guías.
   Creado con el mismo formato que `categories.php` (slug, name, status, order, title, meta,
   related). Sin impacto en la taxonomía de URLs.
2. **`SearchAction` no se emite.** El plan §6 pide `WebSite` + `SearchAction` en la homepage,
   pero el sitio no tiene búsqueda interna y el skill `seo-web-builds` prohíbe declarar una
   que no existe (Google la penaliza como marcado inexacto). `schema_website()` ya trae el
   bloque listo detrás del flag `has_search` en `data/site.php`: se enciende el día que exista
   `/buscar/`. **Marcado para veto de Anton.**
3. **Las reglas de reescritura viven en dos lugares.** `public_html/.htaccess` (producción,
   Apache/LiteSpeed) y `tools/router-cli.php` (servidor embebido para desarrollo y CI). Si se
   toca una, hay que tocar la otra. El render check de CI usa el segundo, así que una
   divergencia se nota como fallo de ruta, no en silencio.
4. **Trust stack incompleto por diseño.** Razón social, RUC, estado de IVA, dirección,
   teléfonos, horarios y el embed de Maps están vacíos en `data/site.php` y por eso no se
   renderizan. Son input humano de la fase 3 (plan §7); nada se inventa mientras tanto.
5. **Sin `og:image`.** La etiqueta sólo se emite si existe `assets/img/og-default.jpg`. Las
   imágenes OG por página de dinero se generan en la fase 6 con `higgsfield-web-imagery`.
6. **`staging_noindex` está en `true`.** Todo el sitio sale con `noindex, nofollow` hasta el
   go-live. Apagarlo es parte del checklist de la fase 6 (`DEPLOY.md`).
7. **La política de privacidad necesita revisión legal antes del go-live.** El texto refleja
   las decisiones del plan (§3, §6, §8.6) pero le falta la identificación del responsable
   (fase 3) y no pasó por abogado.
8. **CI corre en todos los PR, sin `paths-ignore`.** La forma estándar del skill
   `budgeted-runner-deploy` incluye `paths-ignore`, pero acá el job es el check requerido de
   la protección de rama: un run salteado no reporta status y dejaría bloqueado para siempre
   cualquier PR que sólo toque markdown. El job tarda ~15 s, así que el costo de correrlo
   siempre es menor que el del bloqueo. Si algún día deja de ser check requerido, volver a
   poner `paths-ignore`.

## Fase 2 — Lead pipeline

9. **`presupuesto_band` viaja en el payload pero todavía no tiene valores.** `lead_resolve_slug()`
   lee `price_band` de `data/materials.php` y `lead_build_payload()` lo manda en
   `fields.presupuesto_band`, pero ningún material declara `price_band` todavía: el campo se
   omite del payload en vez de ir vacío. Los valores son contenido de la fase 3 (plan §8.7).
   Nunca se renderiza como precio en el sitio — es interno, para el repaso del lead en el CRM.
10. **`vc-attribution.js` se carga sin pedir consentimiento.** Lo fija el plan §3 ("sitewide,
    defer") y es coherente: es una cookie de primer toque de nuestro propio dominio que sirve
    para atribuir el lead que el propio visitante decide enviar, no para perfilarlo ni para
    publicidad de terceros. GA4 y el Meta Pixel sí quedan detrás del banner. Queda declarada
    por nombre en `/politica-de-privacidad/` dentro de "Necesarias". **Punto concreto para la
    revisión legal de la fase 6** (junto con KNOWN-ISSUES #7): si el abogado la considera
    no-necesaria, moverla detrás del consentimiento de marketing es un cambio de tres líneas
    en `partials/analytics.php` — no toca el handler ni el payload.
11. **El redirect de error no repuebla nombre, teléfono ni mensaje.** Vuelve al formulario con
    `?error=`, el material, la cantidad y la ciudad, pero los tres campos personales quedan en
    blanco a propósito: la URL termina en el historial, en el `Referer` y en el `page_location`
    de GA4, y ahí no se ponen datos personales. El campo que hay que retipear es justamente el
    que falló. Si algún día molesta, la solución correcta es una sesión PHP, no la query string.
12. **`data/site.php` no tiene todavía `ga4_id`, `meta_pixel_id` ni `vc_attribution`.** Son
    input humano de la fase 2 (plan §7) que Anton aún no pasó. Sin ellos no se emite ni una
    línea de script de analítica y el sitio funciona igual; `partials/analytics.php` los toma
    apenas se completen, sin más cambios de código.
13. **Sin `config/vendercrm.php` el handler corre en modo sólo-log.** Es el degradado del plan
    §4.5, no un fallo: escribe el lead completo en `storage/leads.log` y el visitante llega a
    `/gracias/` igual. CI corre siempre en ese modo, así que el camino sin CRM está probado en
    cada PR; el camino CON CRM se verifica con un envío real recién cuando exista la config en
    el servidor (criterio de salida de la fase 2).

## Fase 4 — Design & pages

14. **Fuentes vía Google Fonts CDN, no autohospedadas.** `web-design-system` recomienda
    autohospedar; este stack no tiene paso de build (plan §1.2: sin Node), así que
    `header.php` carga Bricolage Grotesque + Inter desde `fonts.googleapis.com` con
    `display=swap` y `preconnect`. Si en producción se prefiere autohospedar, es reemplazar el
    `<link>` por `@font-face` con los `.woff2` en `assets/fonts/` — no toca ninguna plantilla.
15. **Track resuelto es un híbrido, no INDUSTRIAL puro.** El track INDUSTRIAL del skill es
    dark-dominant en todo el body; acá el catálogo tiene ~50 páginas de prosa larga (300–900
    palabras) más FAQ, así que el body vive sobre un campo claro ("paper") y sólo el header, el
    hero de cada página y el pie quedan en la cáscara oscura (`.band--dark`) con grano. Es una
    extensión deliberada del track para legibilidad, no una desviación de las reglas duras
    (un acento, tipografía, motion, tarjetas siguen el sistema tal cual).
16. **Sin imágenes todavía.** Ningún `hero-bleed`/`section-break`/`card-motif` se generó: la
    fase 6 corre `higgsfield-web-imagery`. Hasta entonces el hero de cada página es texto sobre
    banda oscura con grano — no hay ningún placeholder de imagen visible ni "ILLUSTRATIVE
    PANEL". `og:image` sigue sin emitirse (KNOWN-ISSUES #5).
17. **`events.js` es un shim inerte adicional, no reemplaza `analytics.js`.** Empuja
    `data-ev`/`data-ev-loc` a `window.dataLayer` sin proveedor y sin red (analytics-prep del
    skill); la analítica real con consentimiento (GA4/Meta Pixel, fase 2) sigue intacta en
    `assets/js/analytics.js`. Los dos conviven: uno mide clicks de UI sin cuenta, el otro mide
    conversión con consentimiento.

## Fase 5 — Keyword expansion

18. **`ladrillo sapo` (260 búsquedas/mes) quedó sin página dueña.** El plan §5.1 lo proponía
    como sinónimo de `ladrillo-hueco` con la marca "(verify)". No se pudo verificar con una
    fuente confiable si en Paraguay nombra al ladrillo hueco o a un macizo grande, y meterlo
    como sinónimo en la página equivocada arrastra la prosa al vocabulario incorrecto (regla
    de anti-fabricación). Queda anotado en CONTENT-SPEC §11.1 como término sin dueño: se
    resuelve preguntándole a una olería o a un proveedor, y entonces se agrega a
    `synonyms[]` de la página que corresponda.
19. **`cano-de-agua` se autoriza con demanda sin medir.** Los términos `termofusión`, `PPR`
    y `cañería de agua` figuran en KEYWORDS §4.2 (frases a chequear en el próximo pull), no
    en los clústeres medidos. La página se sostiene sobre `caño` (480 búsquedas, la puja más
    alta del clúster de plomería) y sobre el hecho de que el caño de agua y el de desagüe son
    productos distintos. Si el segundo pull de Keyword Planner vuelve vacío, la página se
    funde con `cano-de-pvc`. **Resuelto en 5c**: la página se escribió (`cano-de-agua`, agua
    fría y caliente, roscado y termofusión) porque el caño de agua y el de desagüe son productos
    físicamente distintos y una sola página no puede liderar los dos sin canibalizarse; el
    término `termofusión` queda como sinónimo, no como cabecera.
20. **La FAQ de entrega de `cemento` se reemplazó por una de cemento blanco y mortero.** El
    smoke test limita `faq[]` a 5 entradas y la FAQ de precio de §11.3 ocupa un lugar. El
    contenido de entrega y descarga no se perdió: pasó a la respuesta de la FAQ de precio,
    que nombra el flete y la descarga entre los factores.
21. **Los skills `paraguay-local-site` y `seo-web-builds` no están disponibles en esta
    sesión.** Se usaron los equivalentes más cercanos que sí lo están (`paraguay-business-apps`
    para el vocabulario y las convenciones de PY, y los límites de título ≤ 60 y meta ≤ 155 ya
    codificados en `tools/smoke.php`, que es la forma en que este repo aplica esa regla de SEO).
    Sin impacto en el resultado: las reglas que esos skills aportan —voseo, anti-fabricación,
    límites de title/meta— ya están escritas en `CONTENT-SPEC.md` y verificadas por CI.
