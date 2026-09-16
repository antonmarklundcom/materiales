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
5. ~~Sin `og:image`~~ — **resuelto en la fase 8.** `assets/img/og-default.jpg` está versionado
   y `partials/header.php` lo usa como fallback sitewide. Fotografía por página: ver #22.
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

9. ~~`presupuesto_band` viaja en el payload pero todavía no tiene valores~~ — **resuelto.**
   `data/materials.php` ya declara `price_band`; `lead_resolve_slug()` lo lee y
   `lead_build_payload()` lo manda en `fields.presupuesto_band` (plan §8.7).
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

14. ~~Fuentes vía Google Fonts CDN, no autohospedadas~~ — **resuelto en la fase 14.**
    `assets/fonts/*.woff2` (Bricolage Grotesque + Inter, sólo latin/latin-ext, 196 KB) +
    `@font-face` en `site.css`; `header.php` ya no pide nada a `fonts.googleapis.com`. Detalle
    y la decisión de pinnear el eje óptico de Bricolage a 14 (para entrar en el presupuesto de
    200 KB) en `docs/log/14-tech-hardening.md`.
15. **Track resuelto es un híbrido, no INDUSTRIAL puro.** El track INDUSTRIAL del skill es
    dark-dominant en todo el body; acá el catálogo tiene ~50 páginas de prosa larga (300–900
    palabras) más FAQ, así que el body vive sobre un campo claro ("paper") y sólo el header, el
    hero de cada página y el pie quedan en la cáscara oscura (`.band--dark`) con grano. Es una
    extensión deliberada del track para legibilidad, no una desviación de las reglas duras
    (un acento, tipografía, motion, tarjetas siguen el sistema tal cual).
16. ~~Sin imágenes todavía~~ — **resuelto para el fallback OG en la fase 8.**
    `partials/header.php` emite `og:image` usando `assets/img/og-default.jpg`, ya versionado,
    cuando no hay imagen por página. La fotografía real sigue pendiente en #22 y #26.
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
    **Actualización (2026-09-16, investigación web, no reemplaza la consulta al proveedor):**
    evidencia consistente de varios corralones paraguayos (Construex, Termopac) y de
    clasificados reales (Clasipar, Facebook) muestra que "ladrillo sapo" **no es un ladrillo de
    pared** — ni hueco ni macizo grande —, sino un bloque de relleno para losa alivianada,
    vendido junto con viguetas (sistema vigueta + ladrillo sapo/bovedilla). Esto confirma que
    NO corresponde a `ladrillo-hueco`. El candidato más cercano ya existente en el sitio es
    `tejuelon`, cuyos sinónimos ya incluyen "ladrillo para losa" y "bovedilla cerámica" — mismo
    rubro de losa alivianada. Aun así, no se asignó como sinónimo todavía: la evidencia web no
    distingue con certeza si "ladrillo sapo" (cerámico o de isopor) es exactamente lo mismo que
    "tejuelón" para un comprador paraguayo o una variante distinta del mismo sistema. Sigue
    pendiente la llamada de confirmación a un corralón antes de tocar `synonyms[]`.
19. **`cano-de-agua` se autoriza con demanda sin medir.** Los términos `termofusión`, `PPR`
    y `cañería de agua` figuran en KEYWORDS §4.2 (frases a chequear en el próximo pull), no
    en los clústeres medidos. La página se sostiene sobre `caño` (480 búsquedas, la puja más
    alta del clúster de plomería) y sobre el hecho de que el caño de agua y el de desagüe son
    productos distintos. Si el segundo pull de Keyword Planner vuelve vacío, la página se
    funde con `cano-de-pvc`. **Resuelto en 5c**: la página se escribió (`cano-de-agua`, agua
    fría y caliente, roscado y termofusión) porque el caño de agua y el de desagüe son productos
    físicamente distintos y una sola página no puede liderar los dos sin canibalizarse; el
    término `termofusión` queda como sinónimo, no como cabecera.
    **Actualización (2026-09-16, investigación web, no reemplaza el segundo pull de Keyword
    Planner):** varios proveedores paraguayos independientes (Ferremas, Titan, Casa de los
    Compresores, Sanitarios Roy, Construex) tienen líneas de producto dedicadas a
    termofusión/PPR, y hay actividad de instaladores especializados (termofusoras) en
    clasificados — señal de que el mercado es real, no una traducción sin uso. Esto no mide
    volumen de búsqueda, pero reduce el riesgo de que la página dependa de un término inventado.
    Recomendación: mantener `cano-de-agua` como página propia hasta que el segundo pull real
    de Keyword Planner diga lo contrario.
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

## Fase 8 — Imagery + QA + launch

22. **Sin imágenes por página: la descarga desde el CDN de Higgsfield está bloqueada en este
    entorno.** `curl -sI` contra `*.cloudfront.net` devuelve 403 (política del proxy del
    entorno, ver `higgsfield-image-pipeline` §Regla 2 — el fix es habilitar
    `*.cloudfront.net` en la lista de dominios permitidos del entorno de Claude Code, un clic
    en la configuración del entorno). Por eso esta fase no generó fotografía real ni un
    `og:image` por página: en su lugar se generó un único `public_html/assets/img/og-default.jpg`
    (1200×630) con `tools/generate-og-default.php`, usando GD y la tipografía de marca
    (Bricolage Grotesque) — un motivo de paleta con los tokens del sitio (fondo oscuro, acento
    `#E8562A`), sin fotos ni rostros. Sirve como fallback sitewide (`partials/header.php` ya lo
    busca por convención); no hay imagen distinta por categoría o material todavía. Cuando se
    habilite el dominio en el entorno, correr `higgsfield-image-pipeline` completo para generar
    fotografía real por página de dinero.
23. ~~Bug de responsividad en el header mobile~~ — **descartado, no era real.** El QA de la
    fase 8 reportó desborde horizontal en 390px a partir de una captura con
    `chromium --headless --screenshot` (modo headless viejo): esa herramienta arma la ventana
    con `--window-size` en vez de fijar el viewport real, así que el DOM se renderizó más ancho
    que 390px y la imagen resultante salió recortada — no hay scroll horizontal real. Repetido
    con Playwright fijando el viewport (320/360/375/390px, home y una página de material):
    `document.documentElement.scrollWidth` es igual a `clientWidth` en los cuatro anchos, y el
    nav (`.site-nav`, ya tiene `flex-wrap: wrap`) baja "Contacto" a una segunda línea sin cortar
    nada, banner de cookies incluido. Verificado en la sesión de 2026-09-06 que revisó esto;
    sin cambios de CSS porque no hacía falta ninguno. Moraleja para QA futuro: medir
    responsividad con Playwright/Puppeteer (viewport real), nunca con la bandera
    `--screenshot` de la CLI de Chromium.

## Fase 13 — Calculadoras + guías ola 3

24. **`ladrillos-por-m2` no incluye ladrillo hueco.** Sus medidas de cara (largo × alto) no
    están documentadas en `content/materiales/ladrillo-hueco.php` — esa página sólo publica
    el espesor (8/12/18 cm) — y no se inventó una para poder ofrecer la opción. El selector
    ofrece las 3 medidas de ladrillo común ya publicadas más el bloque de hormigón estándar de
    manual (39 × 19 cm). Se resuelve confirmando la medida real con una olería o un proveedor
    y agregándola a las tablas `largo_cm`/`alto_cm` de `data/calculators.php` — un cambio de
    una línea. Ver `docs/decisions-needed.md` #1.
25. **El bloque de hormigón de `ladrillos-por-m2` usa una medida estándar de manual (39 × 19
    cm de cara), no una verificada con una olería paraguaya.** Si un proveedor local usa otra
    medida, es el mismo cambio de una línea que el punto anterior. Ver
    `docs/decisions-needed.md` #2.

## Fase 15 — Link pass + imágenes + QA de lanzamiento

26. **Sigue sin fotografía real por página** (misma causa que #22: el entorno bloquea
    `*.cloudfront.net`, verificado de nuevo en esta fase — `connect_rejected` por política de
    la organización). No se gastaron créditos de Higgsfield intentándolo. Sigue pendiente el
    paso manual de Anton: subir los 12 archivos de `docs/imagery-brief.md` a
    `assets/img/cat/{slug}.jpg` y `assets/img/hero-home.jpg`, o habilitar el dominio en la
    configuración del entorno para correr `higgsfield-image-pipeline`.

## Post-Fase 15 — auditoría de seguridad y correctitud

27. **El sello anti-bot del formulario (`lead_form_stamp`) no tiene ventana de un solo uso.**
    Un sello válido puede reenviarse cualquier cantidad de veces dentro de `LEAD_STAMP_TTL`
    (12 h) — no hay rate limiting en `cotizar/enviar.php` ni en `.htaccess`. La falsificación
    del secreto ya se resolvió (ver `partials/lead.php` → `lead_form_secret_auto()`, un
    secreto aleatorio persistido en vez de derivar de `api_key`/`base_url`), pero la reutilización
    del mismo sello sigue abierta: alguien con un sello scrapeado de `/cotizar/` podría postear
    miles de leads con teléfonos ajenos. Antes del go-live conviene decidir una estrategia de
    rate limiting (por IP, por sello de un solo uso con un store simple en `storage/`, o a
    nivel de servidor) — es una decisión de diseño, no un cambio de una línea. Anton decide.
28. **El CRM recibe el teléfono tal cual lo tipeó el visitante (`fields.phone`), no el
    normalizado a E.164.** Es intencional y está fijado por `tools/smoke.php:441` (plan §4.4),
    pero la validación de `lead_normalize_phone()` sólo exige que el string tenga la
    subsecuencia de dígitos correcta — no que el string typed sea *sólo* dígitos y separadores,
    así que hasta ~30 caracteres de contenido arbitrario pueden colarse junto con el número
    real. Si VenderCRM alguna vez renderiza ese campo sin escapar, es un vector de XSS
    almacenado ahí (fuera de este repo). Cambiar el contrato del payload es decisión de plan,
    no de esta auditoría — Anton decide si vale la pena.
