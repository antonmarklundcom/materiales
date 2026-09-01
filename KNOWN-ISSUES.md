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

## Fase 2 — Lead pipeline (EN CURSO, PR parcial)

9. **La fase 2 quedó a medias a pedido de Anton.** Lo que YA está mergeado es sólo la
   biblioteca `public_html/partials/lead.php` (normalización de teléfono PY, clave de
   idempotencia, sello firmado de la trampa de tiempo, merge de la cookie `vc_attr`, armado
   del payload, POST al CRM, escritura de `storage/leads.log`) más la clave opcional
   `form_secret` en `config.sample.php`. Nada la llama todavía: el sitio se comporta igual
   que al final de la fase 1.

   **Falta, en este orden** (plan §3 y `prompts/opus-2-lead-pipeline.md`):
   - `public_html/partials/form.php` — material preseleccionado, cantidad, ciudad, nombre,
     teléfono (requerido), mensaje, casilla de consentimiento DESMARCADA, honeypot `website`,
     sello `ts` + `tsg` de `lead_form_stamp()`, `origen` para el redirect de error.
   - Incluir el formulario en `/cotizar/` y en las páginas de categoría y material.
   - `public_html/cotizar/enviar.php` — shell HTTP: honeypot/sello ⇒ 303 silencioso a
     `/gracias/` sin postear; teléfono inválido o consentimiento sin marcar ⇒ 303 de vuelta
     al formulario con `?error=`; camino feliz ⇒ `lead_send()` + `lead_log()` + 303 a
     `/gracias/?m={slug}&k={token de un solo uso}`.
   - `partials/analytics.php` + `assets/js/analytics.js` — GA4 detrás del consentimiento de
     estadísticas, Meta Pixel detrás del de marketing, snippet `vc-attribution.js` sitewide;
     `/gracias/` dispara `cotizacion_form_submitted` y `Lead` una sola vez por token `k`.
   - Ampliar `/politica-de-privacidad/`: cookie `vc_attr`, GA4/Pixel y Ley 7593 por nombre.
   - Ampliar `tools/smoke.php` (unitarios sobre `lead.php`) y `tools/render-check.sh` (POST
     real al handler) con los criterios de salida de la fase.
   - Entrada de build log en `plan.md` §9.
