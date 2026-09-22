# Improvement report #2 — materiales.com.py (2026-09-22)

Post-launch audit, one week after go-live. Four parallel audits (technical SEO, content &
keywords, conversion/UX, code/ops) ran against a local copy of the site (`php -S` +
Playwright/Chromium, 108 sitemap URLs crawled). **The live server was not reachable from the
audit sandbox**, so anything that depends on hPanel/server state is marked _unverified_.

Every finding below was verified in code or by a local reproduction. Item IDs (B1, S3, …) are
stable so a build session can be pointed at them.

Legend — **Impact**: H / M / L on traffic or leads. **Effort**: S (< 1 h), M (half day),
L (multi-day / multi-PR).

Housekeeping note: the catalog is now **70 materials** (STATUS.md still says 64).

---

## Tier 0 — Bugs that are costing leads or data *right now* (do first, all small)

| ID | Finding | Evidence | Fix | Impact | Effort |
|---|---|---|---|---|---|
| B1 | **Every material/category/calculator form posts `origen=/cotizar/`.** `header.php` overwrites the page's `$canonical` with the absolute URL (same PHP scope), `lead_safe_path()` rejects it and falls back to `/cotizar/`. Result: validation errors bounce the visitor to a *different page*, and the CRM gets `page_url=/cotizar/` for every lead — lead attribution is wrong site-wide. | `partials/header.php:14`, `materiales/index.php:202`, `calculadoras/index.php:225`, `partials/lead.php:560`. Rendered `/materiales/cemento/` → `name="origen" value="/cotizar/"` | Rename the header variable (`$canonicalAbs`) or pass the path explicitly. Add a render-check assertion on `origen`. | H | S |
| B2 | **Throttled and bot-flagged leads are thrown away without the payload.** Second lead from the same IP within 60 s (shared mobile CGNAT IPs in PY, or someone fixing a typo'd phone) → logged as `descartado/limite_ip` with **no name/phone**. Same for honeypot (browser autofill of `name="website"`), stamp > 12 h old (tab left open), "too fast". Visitor sees /gracias/ and waits for a call that never comes. | `cotizar/enviar.php:60-69`, `:170-178`; `partials/lead.php:22`; `partials/form.php:110` | Log full payload as `retenido`/`sospechoso` when phone + consent are valid; loosen throttle (e.g. > 5 in 10 min); rename honeypot to something autofill ignores; refresh the stamp via JS on focus. | H | S |
| B3 | **Nobody is notified when a lead arrives.** No mail/Telegram/WhatsApp hook, no digest. STATUS says the server is still in `solo_log` mode (no `config/vendercrm.php`) → leads sit in `storage/leads.log` while /gracias/ promises a reply "dentro del día". | grep: no `mail(`; `enviar.php:212` | Email/Telegram to Anton on every `solo_log` / `fallo_crm` / log failure + daily count digest. And create `config/vendercrm.php` on the server. | H | S |
| B4 | **og:image and `Product.image` point to a 404.** URL is built from the image *base path* with no size/extension (`…/assets/img/hero-cemento-y-cal`). Broken on 97 pages' og:image and all 70 Product blocks → no preview when shared on WhatsApp/Facebook (the main sharing channel in PY). | `partials/header.php:20`, `partials/schema.php:138` | Append `-1280.webp`, or better generate one 1200×630 JPG per category (WhatsApp previews are most reliable with JPG). | H | S |
| B5 | **Idempotency key = phone + hour.** A buyer who asks for cemento, then hierro 10 min later, produces the same key → second request is likely treated as a duplicate by the CRM (CRM side _unverified_) and never replayed. | `partials/lead.php:106` | Include material slug / message hash in the key. | M | S |
| B6 | **`tools/render-check.sh` truncates the real `storage/leads.log`.** Harmless in CI, but repo = docroot, so running it on the server wipes all leads. | `tools/render-check.sh:101` | Point storage at a temp dir via env var (smoke.php already does). | H if ever run on prod | S |
| B7 | **`storage/.htaccess` is never created** when storage/ is first created by the form-secret code path; only the root `[F]` rule protects leads + secret. | `partials/lead.php:145-147` vs `:424-435` | Copy the deny `.htaccess` whenever it is missing. | M | S |
| B8 | **Analytics events are mislabelled.** Hero CTAs, sticky bar and supplier form all fire `data-ev="form_submit"` on *click*; once GA4 is on, "form_submit" will be inflated. `/gracias/?k=<any 16 hex>` also counts as a conversion. | `index.php:56`, `materiales/index.php:102`, `partials/cta.php:29`, `gracias/index.php:23` | `cta_click` for CTAs, `form_start` on first focus, `supplier_signup`; the /gracias/ token event stays the only lead conversion, signed with HMAC. | M | S |

---

## Tier 1 — SEO, highest traffic leverage

### 1a. Quick technical wins (one PR, a few hours)

| ID | Finding | Fix | Impact | Effort |
|---|---|---|---|---|
| S1 | **No http→https / www→non-www 301s** in `.htaccess` (only the trailing-slash rule). A "Force HTTPS" toggle in hPanel may be overwritten by the Git deploy (_unverified_). | Explicit 301 rules at the top of the rewrite block; enable HSTS once SSL is confirmed stable. | H (risk) | S |
| S2 | **No favicon** (`/favicon.ico` 404, no `<link rel="icon">`). Google shows a generic globe in mobile results → lower CTR. | SVG + 48×48 PNG + `favicon.ico`, plus `apple-touch-icon`. | M-H | S |
| S3 | **Product schema invalid on 70 pages** (no `offers`/`review`/`aggregateRating`) → Search Console "invalid item" errors, no benefit. | Remove Product for now (or later: `AggregateOffer` if the price policy changes, see D1). | M | S |
| S4 | **Home and /materiales/ share the same title** ("Materiales de construcción en Paraguay \| Cotizá gratis") and H1; /materiales/ has 59 words. They cannibalise the head term. | /materiales/ → "Catálogo de materiales de construcción (70) \| Materiales.com.py" + 150–250 words of intro. | M-H | S |
| S5 | **Hero image: no `sizes`, no `fetchpriority`.** Desktop downloads the 1920w AVIF (up to 218 KB) to show it at 440 px; on mobile category pages it's the LCP element. | `sizes="(min-width:64rem) 40vw, 100vw"`, `fetchpriority="high"`, `loading="eager"` on the hero. | M | S |
| S6 | **Mobile CLS 0.14–0.15** (needs-improvement band) from the web-font swap; plus nav collapse depends on deferred `motion.js`. | Metric-matched fallback `@font-face` (`size-adjust`, `ascent-override`); set `html.js-nav` with a 1-line inline script in `<head>`. | M | S |
| S7 | **`robots.txt` disallows /gracias/**, so Google never sees its `noindex`. | Remove the `Disallow`; rely on `noindex`. | L | S |
| S8 | **Caching**: no `ExpiresByType image/avif` (the format browsers actually get) or svg; CSS/JS have no version string, so after a deploy returning visitors can get new HTML + stale CSS for 7 days (relevant after the nav PRs #41/#42). | Add avif/svg; `?v=<filemtime>` on CSS/JS and raise expiry to 1 year. | L-M | S |
| S9 | **`proxima` categories (pinturas, electricidad) are linked as normal tiles** from home and /materiales/ but are `noindex, nofollow` thin pages. | Render as non-links ("Próximamente") until published. | L | S |
| S10 | `LocalBusiness` without address/hours on a marketplace. | Switch to `Organization` (logo, `contactPoint`, `areaServed`) + `WebSite` with `publisher`. | L-M | S |

### 1b. On-page targeting (one PR — needs a decision on CONTENT-SPEC §3/§10 title lock)

| ID | Finding | Fix | Impact | Effort |
|---|---|---|---|---|
| S11 | **Titles miss the page's biggest keyword.** `tierra-gorda` owns "tierra colorada" (1.300/mo) but title says "Tierra gorda"; `ripio` misses "canto rodado" (1.000); `ladrillo-prensado` misses "visto" (1.300); `puertas-de-chapa` misses "puertas de metal" (1.300); `membrana-asfaltica` misses "membrana para techo" (1.300); `tanque-de-agua` misses "1000 litros" (720). | Rewrite those titles/H1s/intros to lead with the measured term. | H | S |
| S12 | **17 titles say "Precio por …" but the page never shows a price** → click, then bounce (pogo-sticking signal). | Either publish something price-like (D1) or rename to "Cotizá por … / Precio actualizado por WhatsApp". | M-H | S |
| S13 | **Material H1s are the bare name** ("Cemento", "Clavos"); 18 guide/calculator titles lack "Paraguay"; no year anywhere. | H1 "Cemento en Paraguay: bolsas, tipos y cotización"; add "2026" only on pages that will actually be kept fresh. | M | S |
| S14 | **Guide ↔ calculator cannibalisation**: identical H1/keyword on `guias/cuantas-bolsas-de-cemento-por-m2` vs `calculadoras/bolsas-de-cemento-por-m2`; near-duplicates arena-ripio ↔ hormigón, revoque guide ↔ revoque calculator, two chapa guides, two hierro guides. | Calculator owns "cuántas/cuánto …" (tool intent); guide re-angled to "cómo / por qué / errores comunes". Distinct H1s, keep the cross-links. | M | S |
| S15 | **Money pages with volume are nearly orphaned**: `ladrillo-refractario` (880/mo), `mdf-fibrofacil` (~1.100), `adoquines`, `piedra-laja` linked only from their category; 20 more from just 2 pages. Meanwhile 49 pages link to the cost guide. | Add in-prose links from related materials/guides; widen `related[]`. | M | S |

### 1c. Freshness & E-E-A-T (one PR)

| ID | Finding | Fix | Impact | Effort |
|---|---|---|---|---|
| S16 | **Zero `updated` keys** → no `lastmod` in the sitemap; no visible dates; guides have no Article markup; no author/reviewer; dosages cited as "de manual" with no source; no About/methodology page. | Add `published`/`updated` to every data entry, show "Actualizado: …", emit `Article` (author/publisher) on guides & calculators, add `/nosotros/` and `/como-trabajamos/` (how suppliers are verified — the verification call already exists in `/proveedores/`). Ideally a named technical reviewer (civil engineer / maestro mayor) on guides and calculators. | M-H | M |

---

## Tier 2 — Conversion (turn the traffic into leads)

| ID | Finding | Evidence | Fix | Impact | Effort |
|---|---|---|---|---|---|
| C1 | **Measurement is zero.** `ga4_id`, `meta_pixel_id`, `vc_attribution` empty; no Search Console mentioned as done. You can't optimise SEO or CRO without it. | `data/site.php:47-49` | GA4 + Search Console (DNS verify) + **Consent Mode v2** default-denied stub so conversions are modelled even without opt-in. Key events: `form_start`, lead (gracias token), `whatsapp_click`, `calculator_use`, `cta_click`. | H | S (+ Anton's IDs) |
| C2 | **Calculator result doesn't lead to a quote.** `calc.js` already prefills "22 bolsas de cemento de 50 kg", but there's no button under the result, the form is 5.687 px down, and the sticky bar sends to a *blank* /cotizar/ (`page-calculadora` missing from `$ctaHasForm`). | `assets/js/calc.js:110`, `partials/cta.php:143` | "Cotizá estas 22 bolsas →" button right under `.calc__outputs`; add the calculator to `$ctaHasForm`. Calculators are the highest-intent pages on the site. | H | S |
| C3 | **Form is buried** (mobile: 6.000 px down on material pages, 4.300 px on category, 5.300 px home). Hero has no benefit line. | Playwright measurements | Hero: "Gratis · hasta 3 proveedores · respuesta en el día" + a 2-field mini form (cantidad + WhatsApp) that expands; move the full form up after the first content block. | H | M |
| C4 | **Cookie banner covers 42 % of a mobile screen** (351 px) and hides the hero CTA on every first visit. | `material-390-fold.png` | Compact bottom strip: "Aceptar / Rechazar", "Configurar" expands. | M-H | S |
| C5 | **Error round-trip wipes name/phone/consent**, lands under the sticky header, beige low-contrast error box. | `form.php:23-26`, `novalidate` | Client-side validation of phone + consent before submit (keep server check); `scroll-margin-top` on `#cotizar`; red error style. | M | S |
| C6 | **WhatsApp links are bare `wa.me/595…`** everywhere except /contacto/. | | `?text=Hola, quiero cotizar {material} – {cantidad} – {zona}` per page; track as `whatsapp_click`. Keep the form as primary (consent + structured CRM data) and log WhatsApp leads into VenderCRM by hand. | M | S |
| C7 | **/proveedores/ sticky bar shows the *buyer* CTA** ("Pedir cotización"); rubro tiles send suppliers into buyer pages. | `cta.php:143-144`, `proveedores/index.php:131` | Bar → "Sumate como proveedor" (`#sumate`); tiles unlinked. | M | S |
| C8 | **/gracias/ is a dead end.** | `gracias/index.php:79-113` | "¿Te falta arena/ripio? Sumalo al pedido" (related materials, prefilled), reference code in the WhatsApp link, "Guardá nuestro número". | M | S |
| C9 | **Guides end with a generic `/cotizar/` link** (no material prefilled). | | Inline CTA with `?m={related material}`. | M | S |
| C10 | **Form field details**: `nombre` is HTML-required but server-optional; no required markers; 7 fields visible. | | Mark required fields; consider hiding optional fields behind "Agregar detalles". | L-M | S |
| C11 | **New mechanisms** (bigger bets): "Pegá tu lista de materiales" / photo of a presupuesto upload; multi-material bundle from one calculation (cemento + arena + ripio); price-alert opt-in ("avisame si baja el cemento") into a WhatsApp list. | | Build after C1 so you can measure them. | M-H | M-L |

---

## Tier 3 — Content growth (the long-term traffic engine)

| ID | Opportunity | Why | Impact | Effort |
|---|---|---|---|---|
| G1 | **New calculators**, each tied to a big cluster: cerámica/porcelanato por m² (cajas + adhesivo + pastina; pisos cluster ~12k/mo), chapas para techo (chapas ~10.5k), hierro kg/barra + estribos (~8.5k), durlock placas + perfiles por m², membrana rollos por m², tanque litros por personas. | Calculators are link-worthy, rank for "cuántos/cuánto" queries and already have a prefill path to the form (after C2). | H | M each |
| G2 | **Promote Pinturas** (~15k/mo — the largest uncovered cluster): pintura para pared 2.900, pintura 2.400, pintura para piso 1.600, acrílico 1.300, barniz 880, sellador 880. Needs §11.1 keyword rows, ≥ 3 active materials (smoke rule), category copy, suppliers. | Biggest single volume gain available. | H | M |
| G3 | **Missing pages with measured volume**: ducha higiénica 590, piso parquet 480, metal desplegado 320, melamina 1.300 (borderline), aislantes (fibra/lana de vidrio 720+), vigueta pretensada (unmeasured). "Colores de pintura para casa" ~2.000 as a guide once G2 exists. | Straight volume. | M | S-M |
| G4 | **Comparison guides**: porcelanato vs cerámica, durlock vs ladrillo, cielorraso PVC vs durlock, piso vinílico vs porcelanato, bloque vs ladrillo, teja española vs francesa. | Decision-stage queries, natural lead-ins. Volume unmeasured — check in Keyword Planner. | M | S each |
| G5 | **Depth pass on existing pages**: materials avg 426 words, categories 328, 0 tables in guides/calculators/categories, 0 inline images, generic lines ("un número fijo de bolsas" instead of the number). Add measurement tables, worked numeric examples, real photos. Also fix the grammar slip "mirá la hormigón elaborado" in `content/materiales/cemento.php`. | Helpful-content / quality signals; winning featured snippets needs tables and numbers. | M-H | L (batch by category) |
| G6 | **Glossary of Paraguayan construction terms** (millar, camionada, 4ta/5ta/6ta, tejuelón, ladrillo sapo, alambre dulce…). | Cheap, unique, gets cited by AI answers (AI Overviews / ChatGPT search). | L-M | S |
| G7 | **Run the second Keyword Planner pull** (`KEYWORDS-MATERIALES.md` §6, 100 phrases) — still not done. Price, city and head-term volumes are all unmeasured, which blocks D1/D3. | Cheap input that de-risks everything in Tier 3. | H (enabler) | S (Anton, 30 min) |
| G8 | **Hub pages are thin**: /guias/ 83 words, /calculadoras/ 67, /cotizar/ 42, /contacto/ 30. | Add intros to the hubs; utility pages can stay short. | L-M | S |

---

## Tier 4 — Off-site / authority (non-code, but where rankings come from for a new domain)

| ID | Idea | Impact | Effort |
|---|---|---|---|
| A1 | **"Proveedor verificado" badge**: a page per supplier or a badge + HTML snippet linking back to materiales.com.py. Every supplier you sign becomes a backlink. | M-H | S |
| A2 | **Link targets**: CAPACO (cámara de la construcción), FIUNA/UCA engineering & architecture faculties (calculators as teaching tools), local construction blogs, Clasipar / Facebook Marketplace groups. | M | M (ongoing) |
| A3 | **Short video**: TikTok/Reels "¿Cuántas bolsas de cemento para 20 m²?" → calculator link. A WhatsApp Channel with a weekly material tip/price trend. | M | M (ongoing) |
| A4 | **Google Business Profile**: lead-gen businesses are generally *not eligible* under Google's guidelines (verify the current wording) — don't create one without a real staffed location. Help suppliers with *their* profiles instead. | — | — |

---

## Tier 5 — Reliability, security & hygiene

| ID | Finding | Fix | Impact | Effort |
|---|---|---|---|---|
| R1 | **Replay cron not set up** (STATUS), exits 2 silently without CRM config, retries permanent 4xx forever, skips `solo_log` leads older than 72 h by default. | Cron with alert on non-zero exit + retry cap. When the CRM goes live: `--max-age-hours=0 --dry-run`, check, then run for real so the backlog since 2026-09-16 isn't lost. | H (once CRM on) | S |
| R2 | **No uptime / 5xx monitoring.** | Free UptimeRobot on `/` and `/cotizar/` with a keyword check (`name="consentimiento"`). | M | S |
| R3 | **Rewrite rules duplicated** in `.htaccess` and `tools/router-cli.php`; CI tests only the latter. | Cheap CI step diffing the `[F]` folder lists + a manual `tools/prod-check.sh` that curls the expected 403s on production. | L-M | S |
| R4 | **Nothing blocks `*.bak`, `*.orig`, `*~`, `*.sh`, `*.log` at the root**; a stray backup would serve PHP source. `config.sample.php:5` says "FUERA de public_html", contradicting DEPLOY.md. | One `FilesMatch` deny rule; fix the comment. | L-M | S |
| R5 | **Leads kept forever** (leads.log no rotation, 0644; `storage/throttle/` one file per IP, no cleanup) while the privacy policy promises deletion on request. | Monthly rotation + retention period + throttle cleanup. | L-M | M |
| R6 | **Phone field stored-XSS risk** (KNOWN-ISSUES #28) still open. | Send only `[0-9 +()-]` in `fields.phone`. | L | S |
| R7 | **No CSP; HSTS commented.** | HSTS after S1; CSP stays backlog. | L | S |
| R8 | **Repo clutter** (plan.md 80 KB, prompts/, 44 KB spec docs) inside the docroot — blocked, but noisy for agents. Stale STATUS numbers. | Archive plan.md + prompts/ to a branch; refresh STATUS. | L | S |

CI (one ~15 s job) is fine as is — don't add Lighthouse CI or a link checker to the Actions budget; `tests/` Playwright runs locally.

---

## Decisions only Anton can make

| ID | Decision | Options / recommendation |
|---|---|---|
| D1 | **The "never publish a price" policy** (plan §8.7). ~2.500+ "precio" searches/mo in the measured data, unmeasured "precio … paraguay" variants likely more. Pages without a number struggle against results that show one. | (a) Rename "Precio por" titles (free, gives up the query). (b) Worked quantity examples (no price). (c) **Monthly "Índice de precios de materiales — [mes] 2026"** built from real supplier quotes (median + date + number of quotes) — strongest SEO + PR + link asset, requires suppliers to report quoted prices back. **Recommendation: (c) as soon as there are enough leads, (a) meanwhile.** |
| D2 | Unlock the CONTENT-SPEC title lock for S11–S13. | Recommended yes — measured volume is on the table. |
| D3 | City pages (plan §1.23 bans `/zonas/` unless genuinely localized). | Only after G7 measures "materiales de construcción luque/san lorenzo" and only with real local data (suppliers covering the city, freight notes, lead counts). Not CDE/Encarnación until there are suppliers there. |
| D4 | Real NAP/trust: razón social, RUC, email, hours, a named person on /nosotros/. | Directly lifts conversion (C-tier) and E-E-A-T (S16). |
| D5 | Server inputs: `config/vendercrm.php`, GA4 ID, Search Console, replay cron. | Needed for B3, C1, R1 to matter. |

---

## Suggested execution order (PR batches)

| # | PR | Items | Suggested Opus 5.5 effort |
|---|---|---|---|
| 1 | Lead-loss & data fixes | B1, B2, B5, B6, B7, B8, R6 | **high** (lead pipeline, tests must stay green) |
| 2 | Lead notifications + ops | B3, R1 (script side), R2 docs, R4 | medium |
| 3 | Technical SEO quick wins | B4, S1, S2, S3, S5, S6, S7, S8, S9, S10 | medium |
| 4 | Conversion quick wins | C2, C4, C5, C6, C7, C8, C9, C10 + C1 code (Consent Mode v2 stub, event names) | medium |
| 5 | On-page targeting (after D2) | S4, S11–S15, G8 | **high** (copy + keyword judgement) |
| 6 | Freshness & E-E-A-T | S16 (+ /nosotros/, /como-trabajamos/ once D4 data exists) | medium |
| 7 | Hero mini-form | C3 | high (UX + pipeline touch) |
| 8+ | Content growth waves | G1 calculators (one PR per 2–3), G2 Pinturas, G3–G6, G5 depth pass by category | high for spec/new calculators, medium for prose waves |

Human, in parallel: D1–D5, G7 (Keyword Planner pull), A1–A3.

Medium is enough for the mechanical PRs (3, 4, 6); use high where a mistake silently loses
leads (1, 7) or where the job is judgement about keywords and copy (5, content waves).
