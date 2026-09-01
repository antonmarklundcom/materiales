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
