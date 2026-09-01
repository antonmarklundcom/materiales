# STATUS — materiales.com.py

Documento vivo: qué está hecho, qué falta y qué depende de Anton. Se actualiza al cerrar cada
fase. El plan está en `plan.md`; las instrucciones por fase, en `prompts/`; el copy cerrado,
en `CONTENT-SPEC.md`; los desvíos menores, en `KNOWN-ISSUES.md`.

_Última actualización: 2026-09-01._

## Estado por fase

| Fase | Modelo | Estado | PR | Qué quedó en el repo |
|---|---|---|---|---|
| 1 Foundation | Opus | ✅ Mergeada | #2, #3 | Layout, router, plantillas, partials, datos, sitemap, .htaccess, CI, smoke |
| 2 Lead pipeline | Opus | ✅ Mergeada | #4 | Formulario, handler → VenderCRM, consentimiento, leads.log, /gracias/, analytics con consentimiento |
| 3 Content spec | Opus | 🟡 En PR | #5 | Datos de contenido cerrados (13 categorías, 34 materiales, 6 guías) + `CONTENT-SPEC.md` |
| 4 Design & pages | Sonnet | ⬜ Pendiente | — | Capa visual de todos los tipos de página (`web-design-system`) |
| 5 Content wave 1 | Sonnet | ⬜ Pendiente | — | Prosa de las 5 categorías de lanzamiento, sus materiales y las 6 guías |
| 6 Content wave 2 + QA | Sonnet | ⬜ Pendiente | — | Resto de categorías, imágenes OG, QA SEO, salida a producción |

## Qué anda hoy

- Rutas: `/`, `/materiales/`, `/materiales/{categoria|material}/`, `/guias/`, `/guias/{slug}/`,
  `/cotizar/`, `/gracias/`, `/contacto/`, `/politica-de-privacidad/`, 404 y `sitemap.xml`.
- Namespace de slugs plano y compartido; CI falla si un slug se repite entre categorías y
  materiales (47 slugs únicos hoy).
- Formulario → `/cotizar/enviar.php` → VenderCRM: idempotencia, honeypot, sello de tiempo
  firmado, validación de teléfono paraguayo, consentimiento obligatorio y `leads.log` como
  respaldo. Sin config de CRM el sitio igual funciona y guarda todo en el log.
- GA4 y Meta Pixel sólo cargan con el consentimiento correspondiente; la conversión se
  dispara una vez por token en `/gracias/`.
- Cada página de categoría y material muestra bajada, unidad de venta, FAQ visibles (las
  mismas que emiten `FAQPage`), relacionados y formulario preseleccionado.
- Todo el sitio sale con `noindex` mientras `data/site.php` tenga `staging_noindex => true`.

## Lo que falta antes de salir a producción

1. **Prosa real** en `content/categorias/`, `content/materiales/` y `content/guias/`
   (fases 5 y 6). Hoy las páginas muestran metadatos, FAQ y formulario, sin cuerpo.
2. **Capa visual** (fase 4). El CSS actual es una base funcional, no el diseño final.
3. **Imágenes OG** por página de dinero (fase 6).
4. **Datos reales de NAP** (abajo).
5. **`staging_noindex => false`** cuando el dominio esté apuntando.

## Lo que depende de Anton (nada de esto lo puede inventar Claude)

| Cuándo | Qué | Dónde va |
|---|---|---|
| Ya | Razón social, RUC, condición IVA, dirección, horarios, email, teléfono, WhatsApp | `data/site.php` |
| Ya | Hostinger: slot del sitio, PHP 8.x, Git deploy por webhook en `main` | hPanel — ver `DEPLOY.md` |
| Ya | VenderCRM: registro en **Sitios**, URL + API key, ruteo por defecto | `config/vendercrm.php` en el servidor, fuera del repo |
| Ya | GA4 property ID + Meta Pixel ID (o dejarlos vacíos: degradan bien) | `data/site.php` |
| Ya | Repo → Settings → Pull Requests → "Allow auto-merge" (sin esto, cada fase queda esperando merge manual) | GitHub |
| Antes del lanzamiento | DNS de materiales.com.py → Hostinger | Registrador |
| En paralelo, humano | Reclutar 2–3 proveedores fundadores por categoría de lanzamiento | Planilla + VenderCRM |

## Cómo verificar localmente

```sh
find . -name '*.php' -not -path './.git/*' -print0 | xargs -0 -n1 php -l
php tools/smoke.php          # datos + unidades del pipeline de leads
bash tools/render-check.sh   # rutas reales y POSTs reales contra el handler
php -S 127.0.0.1:8080 -t public_html tools/router-cli.php
```

## Notas del build

- Las fases 1–3 corrieron en sesiones distintas. La sesión de la fase 3 encontró la fase 2
  terminada pero **sin mergear** (PR #4 verde, esperando): sin "Allow auto-merge" activado en
  el repo, ninguna fase se mergea sola y la siguiente queda bloqueada por el protocolo §4.2.
  Se mergeó a mano para poder seguir.
- Fase 3 tocó plantilla además de datos: las FAQ ahora se **muestran**. Emitir `FAQPage` sin
  preguntas visibles es marcado inexacto, así que el dato y el render van juntos.
- `tools/smoke.php` ahora exige contenido cerrado (keyword, intro, sale_unit, price_band,
  synonyms, 3–5 FAQ con respuesta, related válidos). Una regresión de contenido rompe CI.

## Próximo paso

Fase 4 (Sonnet): `Read prompts/sonnet-4-design-pages.md in this repo and execute it.`
