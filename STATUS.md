# STATUS — materiales.com.py

Documento vivo: qué está hecho, qué falta y qué depende de Anton. Se actualiza al cerrar cada
fase. El plan está en `plan.md`; las instrucciones por fase, en `prompts/`; el copy cerrado,
en `CONTENT-SPEC.md`; los desvíos menores, en `KNOWN-ISSUES.md`.

_Última actualización: 2026-09-11 (revisión de Fable: informe de mejoras y plan de las fases
9–15 — ver `docs/IMPROVEMENT-REPORT.md` y `plan.md` §11)._

## Estado por fase

| Fase | Modelo | Estado | PR | Qué quedó en el repo |
|---|---|---|---|---|
| 1 Foundation | Opus | ✅ Mergeada | #2, #3 | Layout, router, plantillas, partials, datos, sitemap, .htaccess, CI, smoke |
| 2 Lead pipeline | Opus | ✅ Mergeada | #4 | Formulario, handler → VenderCRM, consentimiento, leads.log, /gracias/, analytics con consentimiento |
| 3 Content spec | Opus | ✅ Mergeada | #5 | Datos de contenido cerrados (13 categorías, 34 materiales, 6 guías) + `CONTENT-SPEC.md` |
| 4 Design & pages | Sonnet | ✅ Mergeada | #6 (pendiente de verificar) | Capa visual completa (track INDUSTRIAL adaptado, `web-design-system`): tokens, tipografía, tarjetas, formulario, FAQ-acordeón, pie en cinta, motion |
| 5 Keyword expansion (PR 5a · 5b · 5c) | Opus (una ventana) | ✅ Mergeada | #8, #9, #10 | CONTENT-SPEC §11 (propiedad de keywords, marcas genéricas, FAQ de precio, regla de medidas), 30 materiales nuevos, FAQ de precio en los 64, 2 guías nuevas y 6 categorías promovidas con su copy reescrita |
| 6 Content wave 1 | Sonnet (una ventana, PR 1/3) | ✅ Mergeada | #11 | Prosa de las 5 categorías de lanzamiento, sus 34 materiales y las 8 guías |
| 7 Content wave 2 | Sonnet (misma ventana, PR 2/3) | ✅ Mergeada | #12 | Prosa de pisos, aberturas, impermeabilizantes, yeso, plomería y madera |
| 8 Imagery + QA + launch | Sonnet (misma ventana, PR 3/3) | ✅ Mergeada | #13 | QA SEO completo, `og-default.jpg` de fallback (sin fotografía real — CDN bloqueado, ver KNOWN-ISSUES #22), checklist de salida |
| 9 Home & conversion | Opus (ventana 1/2, PR 1/4) | ✅ Mergeada | #18 | Home real (franja de datos, cómo funciona, prosa local, teaser de guías, formulario), CTA en héroes, barra móvil pegajosa, próximos pasos en /gracias/ |
| 10 Proveedores | Opus (misma ventana, PR 2/4) | ✅ Mergeada | #19 | `/proveedores/` (landing de captación: qué recibís, cómo funciona, condiciones desde `supplier_pitch`, rubros, FAQ), formulario de proveedor, `tipo=proveedor` en el handler, consentimiento `proveedor-v1`, cláusula de privacidad y enlace "Para proveedores" en nav y pie |
| 11 Cross-links + slots de imagen | Opus (misma ventana, PR 3/4) | ⏳ Pendiente | — | "Guías relacionadas" desde datos, clave `image` opcional, og:image por página |
| 12 Calculadoras (base) | Opus (misma ventana, PR 4/4) | ⏳ Pendiente | — | Ruta `/calculadoras/`, contrato de datos, `calc.js`, CONTENT-SPEC §12, 1 calculadora |
| 13 Calculadoras + guías ola 3 | Sonnet (ventana 2/2, PR 1/3) | ⏳ Pendiente | — | 3 calculadoras, 6 guías |
| 14 Endurecimiento técnico | Sonnet (misma ventana, PR 2/3) | ⏳ Pendiente | — | deflate/headers, fuentes locales, lastmod, replay de leads, test de overflow |
| 15 Link pass + imágenes + QA | Sonnet (misma ventana, PR 3/3) | ⏳ Pendiente | — | Enlaces editoriales, fotos si existen, KNOWN-ISSUES, informe de cierre |

## Qué anda hoy

- Rutas: `/`, `/materiales/`, `/materiales/{categoria|material}/`, `/guias/`, `/guias/{slug}/`,
  `/cotizar/`, `/gracias/`, `/contacto/`, `/politica-de-privacidad/`, 404 y `sitemap.xml`.
- Namespace de slugs plano y compartido; CI falla si un slug se repite entre categorías y
  materiales (77 slugs únicos hoy: 13 categorías —11 activas— y 64 materiales, todos activos).
- Todo material cierra sus FAQ con `¿Cuánto cuesta …?` respondida con los factores y el CTA,
  nunca con una cifra (CONTENT-SPEC §11.3). El smoke test lo exige.
- Ninguna categoría puede publicarse con menos de 3 materiales activos: lo verifica el smoke.
- Formulario → `/cotizar/enviar.php` → VenderCRM: idempotencia, honeypot, sello de tiempo
  firmado, validación de teléfono paraguayo, consentimiento obligatorio y `leads.log` como
  respaldo. Sin config de CRM el sitio igual funciona y guarda todo en el log.
- GA4 y Meta Pixel sólo cargan con el consentimiento correspondiente; la conversión se
  dispara una vez por token en `/gracias/`.
- Cada página de categoría y material muestra bajada, unidad de venta, FAQ visibles (las
  mismas que emiten `FAQPage`), relacionados y formulario preseleccionado.
- Todo el sitio sale con `noindex` mientras `data/site.php` tenga `staging_noindex => true`.
- **Capa visual completa** (fase 4): track INDUSTRIAL de `web-design-system` adaptado —
  cáscara oscura con grano en header/hero/pie, campo claro para prosa y FAQ, un acento
  (`#E8562A`), Bricolage Grotesque + Inter, tarjetas y tiles de catálogo, formulario y
  FAQ-acordeón restilizados, pie en cinta de confianza, `motion.js` (reveal + header
  sticky) y `events.js` (shim `data-ev` sin proveedor, no reemplaza la analítica de fase 2).
- **QA SEO (fase 8)**: `og-default.jpg` (1200×630, fallback de paleta sin fotos, generado
  con GD en `tools/generate-og-default.php`) sitewide; un `<h1>` por página, canonicales,
  JSON-LD válido en todas las rutas verificadas, títulos ≤ 60 y metas ≤ 155 en las 85
  entradas de datos, sin precios ni marcas fuera de la lista cerrada. Fotografía real por
  página de dinero queda bloqueada por el entorno — ver KNOWN-ISSUES #22.

## Lo que falta antes de salir a producción

1. **Fotografía real por página de dinero** — bloqueada por el entorno (CDN de Higgsfield
   sin permitir), KNOWN-ISSUES #22. Correr `higgsfield-image-pipeline` completo cuando el
   dominio `*.cloudfront.net` esté permitido.
2. **Datos reales de NAP** (abajo).
3. **`staging_noindex => false`** cuando el dominio esté apuntando.

(El supuesto bug de header mobile de la primera pasada de QA de fase 8 se descartó — era un
artefacto de la herramienta de captura, no un problema real. Ver KNOWN-ISSUES #23.)

Prosa real: ✅ completa. Las 11 categorías, 64 materiales y 8 guías ya tienen cuerpo en
`content/categorias/`, `content/materiales/` y `content/guias/` (fases 6 y 7) — ninguna
página activa muestra ya el aviso "estamos publicando el contenido".

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
- Fase 4 no tocó `tools/smoke.php` ni `tools/render-check.sh`: sólo capa visual. Sí quedó fija
  una convención nueva para las fases de contenido: `tools/render-check.sh` verifica la
  subcadena literal `<h1>` en `/guias/` y `/cotizar/`, así que el `<h1>` de cada plantilla debe
  seguir sin atributos (el estilo se aplica por selector CSS contextual, no por clase en el h1).

## Próximo paso

**Ventana Opus — fases 9–12** (ver `docs/IMPROVEMENT-REPORT.md` §4 y `plan.md` §11). Línea a
pegar en una ventana nueva de Opus con permisos en auto-aceptar:

`Read prompts/opus-9-conversion-window.md in this repo and execute it.`

Cuando esa ventana termine (4 PR mergeados), la ventana Sonnet (fases 13–15) arranca con
`Read prompts/sonnet-13-content-window.md in this repo and execute it.`

Nada de esto frena el go-live: NAP, `config/vendercrm.php`, DNS y `staging_noindex => false`
se pueden hacer hoy (informe §5). Las fotos son un paso manual de Anton antes de la fase 15.

Nota de proceso: "Allow auto-merge" ya está habilitado en el repo (Anton, 2026-09-06), así que
desde la fase 6 el flujo de §4.2/§4.12 corrió sin intervención manual.
