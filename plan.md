# materiales.com.py — Build Plan

Spanish-language (es-PY, voseo) SEO content site for construction materials in Paraguay.
Model: rank on `/materiales` category + per-material pages, capture quote requests, sell each
lead to up to 3 suppliers per category. Marketplace mechanics invisible on-site. Stack:
static HTML + PHP on Hostinger shared hosting, no Node, no database at launch.

**Status: APPROVED for build (Anton, 2026-08-31).** Decisions below are locked; items marked
⚠️ were flagged assumptions Anton did not veto. §8 lists business questions parked outside
the build.

## Phase table

| Phase | Model | Prompt file | Covers |
|---|---|---|---|
| 1 Foundation | Opus | `prompts/opus-1-foundation.md` | §2 data model, router/templates, schema partials, sitemap/robots, deploy + CI |
| 2 Lead pipeline | Opus | `prompts/opus-2-lead-pipeline.md` | §3 form → PHP handler → VenderCRM, consent, analytics |
| 3 Content spec | Opus | `prompts/opus-3-content-spec.md` | Full taxonomy data populated, locked copy for homepage + templates, per-material outlines |
| 4 Design & pages | Sonnet | `prompts/sonnet-4-design-pages.md` | Visual layer for all page types (web-design-system) |
| 5 Content wave 1 | Sonnet | `prompts/sonnet-5-content-wave1.md` | Prose for 5 launch categories + their materials + guías |
| 6 Content wave 2 + launch QA | Sonnet | `prompts/sonnet-6-content-wave2-qa.md` | Remaining categories, OG images, SEO QA, go-live |

Per the model cost guardrail: phases only ever run on Opus or Sonnet — never Fable.

---

## §1 Decisions already made — do not re-litigate

1. **Stack**: static HTML + PHP includes on Hostinger shared hosting. No Node, no framework,
   no SSR, no database at launch. Flat files (PHP arrays) as the data layer.
2. **Rendering: PHP at request time, not build-time static generation.** Reasons:
   - Hostinger shared hosting runs PHP natively with OPcache; rendering a template + array
     lookup per request is milliseconds. There is no traffic level this site will plausibly
     reach where that breaks — and if it ever does, `/materiales/*` responses are trivially
     cacheable (Cache-Control / LiteSpeed cache) without changing the architecture.
   - A build step needs somewhere to run. No Node is allowed, GitHub Actions minutes are
     budgeted (see `budgeted-runner-deploy`), and a local build step reintroduces the deploy
     pipeline this stack choice exists to avoid.
   - The site will grow to 100+ material pages (long-tail is the strategy). Adding a page =
     add one array entry + one content include + push. No regeneration of anything;
     sitemap.xml is itself PHP reading the same data files, so it can never drift.
   - The future listing-platform pivot needs request-time logic anyway (supplier pages,
     filters). Choosing PHP now means the pivot extends the router instead of replacing a
     static-generation pipeline.
3. **Lead destination**: VenderCRM only, via `POST {CRM_URL}/api/v1/leads` from the site's own
   PHP handler. Browser never talks to the CRM. Key in non-web-readable config, never in the
   repo. Follow `vendercrm-lead-capture` skill (`references/php.md`) exactly.
4. **No automated supplier fan-out at launch** (see §3). VenderCRM is the single source of
   truth; routing to suppliers is manual inside VenderCRM.
5. **No published prices.** Price bands / "cotización personalizada" framing only.
6. **Supplier billing**: manual prepaid lead packs, tracked in a spreadsheet + VenderCRM
   notes. No admin backend, no billing code on the site.
7. **Launch supplier recruitment scope**: 5 categories (§5). Content launches broader.
8. **Leads sold to up to 3 suppliers** at launch (not 5) — disclosed on the form as a
   benefit, required for consent anyway. ⚠️ Raise to 5 later per category if buyers tolerate it.
9. **Language**: Paraguayan Spanish, voseo in every CTA. Code/identifiers in English, all
   public copy and slugs in Spanish (no accents/ñ in slugs).
10. **Deploy**: push to `main` → Hostinger Git deployment webhook. GitHub Actions is never in
    the deploy path; CI is a required lint/smoke check only (~1 min per PR).

## §2 Object model & data layer (no DB)

All data files are PHP arrays (fast, typo-safe via `php -l`, no JSON parsing per request).
English keys, Spanish values.

```
public_html/
  index.php                 ← homepage
  materiales/index.php      ← router for /materiales/… (via .htaccess rewrite)
  cotizar/                  ← quote form page + POST handler (enviar.php)
  gracias/                  ← thank-you (conversion events fire here)
  guias/                    ← informational content router
  politica-de-privacidad/  contacto/  404.php
  sitemap.xml → sitemap.php (rewrite)   robots.txt (static)
  assets/  (css, js, img — WebP)
  partials/ (header, footer, schema blocks, cookie banner, form)
data/            ← inside repo, above public_html on the server
  site.php        NAP, RUC, areaServed cities, WhatsApp, GA4/Pixel IDs
  categories.php  slug, name, status(activa|proxima), order, title, meta, intro_keywords, faq[]
  materials.php   slug ⇒ { category, name, synonyms[], sale_unit, price_band(internal),
                  title, meta, faq[], related[], schema{} }
content/
  categorias/{slug}.php   prose body per category page
  materiales/{slug}.php   prose body per material page
  guias/{slug}.php
config/          ← NOT in repo; lives above public_html on the server
  vendercrm.php   VENDERCRM_URL, VENDERCRM_API_KEY  (config.sample.php IS in repo)
storage/         ← NOT web-readable
  leads.log       JSONL append: every submission + CRM response (fallback + audit)
```

Rules:
- **Slug namespace is flat and shared**: `/materiales/{slug}/` serves BOTH category pages
  (`/materiales/hierro/`) and material pages (`/materiales/piedra-bruta/`). The router checks
  categories first, then materials; CI asserts slug uniqueness across both files. Hierarchy
  lives in breadcrumbs and internal links, not the URL — so a material can be recategorized
  without a redirect, URLs stay short, and the slug is the exact-match keyword.
- `synonyms[]` per material captures the vocabulary long-tail ("piedra bruta" vs "piedra para
  cimiento", "6ta" vs "piedra triturada sexta") — used in on-page copy, FAQ phrasing, and
  internal anchor text. One PAGE per material concept; synonyms never get their own page
  (cannibalization).
- **Suppliers are not site data.** Roster lives in a spreadsheet + VenderCRM (columns:
  category, empresa, WhatsApp, prepaid pack balance, response-time notes). Reserved for the
  pivot: `data/suppliers.php` + URL namespace `/proveedores/{empresa}/` — never used at launch.
- **Lead records**: VenderCRM is the record. `storage/leads.log` is the failure fallback and
  audit trail only.

### URL taxonomy (locked — survives the listing-platform pivot)

```
/                                homepage (WebSite + SearchAction + LocalBusiness)
/materiales/                     index — ItemList of categories
/materiales/{categoria}/         category money page (e.g. /materiales/hierro/)
/materiales/{material}/          material page (e.g. /materiales/piedra-bruta/)
/guias/{slug}/                   informational long-tail feeding money pages
/cotizar/  /gracias/  /contacto/  /politica-de-privacidad/
--- reserved, unused at launch ---
/proveedores/{empresa}/          supplier public pages (pivot)
/materiales/{slug}/proveedores/  per-material supplier listings (pivot)
/zonas/{ciudad}/                 only if genuinely localized content ever exists
```

The pivot adds pages; it never moves one. That is the whole test.

## §3 Lead flow end-to-end

```
visitor → form (on every category/material page + /cotizar/)
  fields: material (preselected from page), cantidad, ciudad/zona, nombre,
          teléfono* (required, PY format), mensaje, consent checkbox* (unticked)
  honeypot field + min-time trap
→ POST to site's own /cotizar/enviar.php
  1. honeypot filled or <3s since render ⇒ redirect to /gracias/ silently, log, send nothing
  2. validate phone server-side (accepts 0981 123 456 style), consent must be checked
  3. idempotency_key = sha256(phone . "|" . date("Y-m-d-H"))
  4. POST {CRM_URL}/api/v1/leads, X-Api-Key from config/, 10s timeout, try/catch
     payload: phone, idempotency_key, name, message, source="site:materiales",
       utm_*/gclid/fbclid (from POST + vc_attr cookie merge, cookie wins for first-touch),
       page_url, referrer,
       fields: { material, categoria, cantidad, ciudad, presupuesto_band?,
                 consent: "proveedores-v1 @ ISO-timestamp" }
     NEVER: pipeline, stage, owner, tag — routing is configured on the site record in CRM.
  5. append JSONL line to storage/leads.log (payload + CRM status) — always, success or fail
  6. 303 redirect to /gracias/?m={material}  (PRG — browser refresh can't double-submit;
     idempotency_key catches everything the PRG misses; this IS the no-inline-confirmation
     mechanism — they are one system, not two)
→ /gracias/ fires GA4 `cotizacion_form_submitted` + Meta Pixel `Lead` (gated on marketing
  consent), shows expectation copy: "Hasta 3 proveedores verificados te van a escribir por
  WhatsApp, normalmente dentro del día."
→ routing: MANUAL inside VenderCRM — you open the lead, forward to the 2–3 suppliers for
  that category with prepaid balance, note who got it.
```

**Fan-out decision: manual at launch, and when automated, it lives in VenderCRM, not in the
site's PHP.** Reasons: (a) automated fan-out in the handler means the handler owns supplier
config, delivery state and retries — a second lead system, exactly what you said you don't
want; (b) at launch volume (a few leads/day at best) manual forwarding costs seconds and lets
you QA every lead before a supplier pays for it — lead quality is the product; (c) the CRM
already owns the contact and is WhatsApp-native, so "notify N suppliers when a lead hits site
X" is a CRM automation feature you build once in VenderCRM and every future site gets it. The
`fields.material` + `fields.categoria` tags are what make that routing possible, manual or
automated.

Attribution: `{CRM_URL}/vc-attribution.js` sitewide (defer). Handler reads `vc_attr` cookie
and folds first-touch UTM/gclid/fbclid into the payload.

Failure behavior: visitor ALWAYS reaches /gracias/. CRM 4xx/5xx/timeout → logged with
response body in leads.log; a weekly (later daily) manual replay from the log is acceptable at
launch volume. 429 → logged, no retry in-request. Duplicate (200, `duplicate:true`) → success.

## §4 Autonomy protocol (build phases)

1. Work until the phase's exit criteria pass; never ask permission for in-plan work.
2. One PR per phase: branch `phase/<id>` off latest main, open PR, IMMEDIATELY arm GitHub
   auto-merge (squash) via `mcp__github__enable_pr_auto_merge`. If arming fails, a §7
   preflight setting is missing — say so in the phase report, never silently fall back to
   watching. A red build is the session's own work. Never build on an unmerged phase.
3. Minor non-blocking issues → `KNOWN-ISSUES.md`, keep building.
4. Stop and ask ONLY for: a missing credential with no graceful fallback, or a
   bad-foundation decision (taxonomy shape, lead handler contract, consent wording) where a
   wrong guess forces a rewrite. Everything else: choose, record in build log, continue.
5. Missing env/config values never block: `config.sample.php` documents them; handler
   degrades to leads.log-only and the site still ships.
6. Every phase prompt is re-runnable: check the branch, continue from the first unmet exit
   criterion.
7. Sonnet hard limits: no changes to the router, data-file schemas, lead handler contract,
   URL taxonomy, or consent text. Workaround + Backlog note instead.
8. **Model cost guardrail: Fable/Mythos is NEVER used for phases, subagents, or spawned
   sessions.** If a session believes Fable is needed, stop and ask Anton with the reason.
9. Phase handoff only when all four gates pass: merge VERIFIED via GitHub MCP tools (PR
   merged, origin/main contains the commit, checks green); exit checklist passed;
   pre-handoff audit done (re-run lint/smoke, adversarially re-read own merged diff); build
   log entry committed. Then spawn the next phase as a NEW session via `create_session`
   (inherit env + permission mode, never `plan`; model per phase table; prompt exactly
   `Read prompts/<next-file>.md in this repo and execute it.`). Fallback without
   `create_session`: continue same window if same model; stop and report at a model switch.
10. Build log: before merging, append a dated 5–10 line entry to §9.
11. Silence is never progress. All GitHub state checks via `mcp__github__*` tools, never
    curl/gh. Every wait has a deadline and a "could not determine" branch. No phase ends a
    turn "waiting for CI" — it ends merged, or blocked with a stated need.

## §5 Category scope & content plan

**Supplier recruitment at launch — 5 categories** (high order value, wholesale-friendly,
deep supplier pools, repeat B2B demand):

1. **hierro** (varillas, mallas)
2. **cemento-y-cal** (cemento, cal, hormigón elaborado)
3. **aridos** (arena, ripio, piedra triturada, piedra bruta)
4. **ladrillos-y-bloques**
5. **chapas-y-techos**

**Content launches broader than recruitment.** SEO takes months to ramp; publishing a
category costs nothing and a quote request in an unrecruited category is a *recruitment tool*
("tengo demanda real en tu rubro"). So all category pages ship, but only the 5 above get
active supplier outreach at launch.

Expansion order (by supplier availability + lead value): madera → aberturas →
canos-y-plomeria (PVC) → pisos-y-revestimientos → electricidad → pinturas →
yeso-y-durlock → impermeabilizantes.

**Material page long-tail (this is the ranking strategy).** One page per material concept,
authentic PY vocabulary, synonyms on-page. Launch set (~40 pages, wave 1 = the 5 recruited
categories):

- hierro: varilla-de-hierro (diameters as on-page table, not separate pages),
  malla-electrosoldada, alambre-negro, clavos, perfiles-metalicos
- cemento-y-cal: cemento, cal-viva, cal-hidratada, hormigon-elaborado, adhesivo-para-ceramica
- aridos: arena-lavada, arena-gorda, ripio, piedra-triturada (4ta/5ta/6ta on-page),
  **piedra-bruta** (the foundation/wall stone — real PY search behavior, low competition),
  tierra-gorda, escombro-relleno
- ladrillos-y-bloques: ladrillo-comun, ladrillo-hueco, ladrillo-prensado,
  bloque-de-hormigon, tejuelon
- chapas-y-techos: chapa-de-zinc, chapa-trapezoidal, chapa-termoacustica (isopanel),
  teja-espanola, teja-francesa, fibrocemento
- madera (wave 2): tirantes, puntales, tabla-de-encofrado, terciada, machimbre, listones

Guías (informational tail feeding money pages, wave 1: 4–6): cuántas bolsas de cemento por
m², qué piedra usar para cimientos, ladrillo común vs hueco, qué chapa conviene para techo,
cuánta arena y ripio por m³ de hormigón, hierro: qué diámetro para qué uso. Every guía links
to its money pages with descriptive anchors.

Quality bar: 15 real pages beat 60 thin ones. A material page ships only with genuinely
different content (uses, sale units, how it's quoted in PY, FAQ). Thin doorway pages sink
the whole domain.

## §6 SEO & schema decisions

- Titles ≤60 chars front-loaded, metas ≤155 written as ad copy with conversion verb
  ("Pedí tu cotización"), exactly one H1 per page, voseo throughout.
- `hreflang`/lang: `<html lang="es-PY">`, self-referencing canonical per page. (Single-locale
  site — a full hreflang set is unnecessary; the lang attribute + es-PY content is what
  matters.)
- Sitewide `LocalBusiness` JSON-LD in the **service-area pattern**: `areaServed` = Asunción,
  Lambaré, Fernando de la Mora, San Lorenzo, Luque, Capiatá, Mariano Roque Alonso,
  Villa Elisa, Ñemby, Limpio, Itauguá. `streetAddress` only if the real business address
  goes in the footer — never fabricated. No `aggregateRating`, ever, until real displayed
  reviews exist.
- Homepage: `WebSite` + `SearchAction`. `/materiales/`: `ItemList` + `BreadcrumbList`.
  `BreadcrumbList` sitewide.
- **Product schema — amended call**: `Product` without an offer/price earns no rich result
  and Search Console nags about missing offers. Since prices are deliberately unpublished:
  category pages get `ItemList` + `FAQPage`; material pages get minimal `Product`
  (name/description/image, no offers) + `FAQPage` + `BreadcrumbList`. Revisit `AggregateOffer`
  with honest PYG ranges only if we later maintain bands publicly. ⚠️ This trims your
  original "Product per category page" spec — flagged for veto.
- `FAQPage` only where Q&As are visible on-page (they are — every category/material page
  carries 3–5 FAQs from the data file).
- sitemap.xml = PHP over the data files (only status=activa pages); robots.txt static,
  links sitemap.
- Footer trust stack sitewide: business name, RUC, IVA status, address, clickable WhatsApp +
  `tel:` + `mailto:`, horarios, Google Maps embed (loaded on interaction, not on page load —
  page weight), payment icons (Tigo Money, Personal Pay, Bancard, Efectivo, Transferencia).
- Cookie banner: Necesarias always on; Estadísticas/Marketing default OFF; GA4 + Pixel load
  only after respective consent. Accepted cost: some conversions untracked. (Ley 6534 + 7593.)
- OG image 1200×630 per money page (WhatsApp link previews are the ad in PY) — generated via
  `higgsfield-web-imagery` in phase 6.
- **CTA pattern — amended call**: this site inverts the usual PY WhatsApp-first rule. The
  *form* is the primary CTA ("Pedí cotización a proveedores verificados") because the resale
  model needs structured fields + the consent checkbox; a WhatsApp chat to your number
  carries no consent to share data and no structure. WhatsApp remains the secondary CTA for
  questions ("¿Dudas? Escribinos") — those leads get consent asked in-chat before any resale.
  Calendar embed: DROPPED — B2B materials buyers don't book calls; it's dead weight. ⚠️ Both
  flagged for veto.

## §7 Human-inputs checklist (Anton)

| Needed by | Item |
|---|---|
| Before phase 1 | **Auto-merge preflight**: repo Settings → General → Pull Requests → tick "Allow auto-merge"; branch protection on `main` requiring the CI check |
| Before phase 1 | Merge this plan PR so phase 1 branches off a main containing it |
| Phase 1 | Hostinger: create site slot, enable Git deployment webhook on `main`, PHP 8.x |
| Phase 2 | VenderCRM: create the **Sitios** record for materiales, copy CRM base URL + site API key into `config/vendercrm.php` on the server (never the repo); set default pipeline/stage routing on the site record |
| Phase 2 | GA4 property ID + Meta Pixel ID (or say "placeholder" — handler degrades gracefully) |
| Phase 3 | Real NAP: business name, RUC, IVA status, address, horarios, contact email |
| Phase 6 | Domain live: materiales.com.py DNS → Hostinger; remove staging noindex |
| Parallel, human-only | Recruit 2–3 founding suppliers per launch category (§8 cold start) |

## §8 Resolved positions on the 8 open problems (+ parked questions)

1. **Phone validation/OTP: NO at launch.** Premature on flat-file PHP. Instead: server-side
   PY-format validation, honeypot, min-time trap, and — the real quality gate — you manually
   eyeball every lead in VenderCRM before forwarding (you're routing manually anyway). Add
   WhatsApp-ping validation only when volume makes manual QA impossible.
2. **"Sold N times": disclose as the value prop.** Form microcopy + gracias page say "hasta 3
   proveedores verificados te contactan" — comparison shopping is a *benefit* to a B2B buyer,
   and the disclosure doubles as consent substance. Cap 3 at launch.
3. **Supplier billing: manual prepaid packs confirmed.** Spreadsheet + VenderCRM notes.
   Building billing into a no-DB static site is the wrong hill; revisit only at the pivot.
4. **Cold start: founding suppliers first, then small paid traffic, content compounding
   behind.** Sequence: (a) during the build, recruit 2–3 founding suppliers per launch
   category with a free-first-leads deal (5–10 free leads in exchange for fast response +
   quality feedback + a testimonial-grade reference); (b) at go-live, run a small Google Ads
   budget on high-intent quote queries (e.g. "precio hierro construcción asunción",
   "arena ripio precio") to generate provable leads within days; (c) convert founders to
   paid packs once free leads prove out; SEO traffic replaces paid spend over months 3–9.
   Never sell packs on projected SEO traffic — sell on leads already flowing.
5. **Category scope**: 5 recruited categories, content broader, expansion order — §5.
6. **Ley 7593 consent: compatible, one submission.** The unticked checkbox consents to
   *sharing with suppliers so they can contact you* ("Acepto que mis datos sean compartidos
   con proveedores del rubro para recibir cotizaciones" + link to Política de Privacidad).
   VenderCRM is named in the privacy policy as **encargado de tratamiento** (processor acting
   for you, the responsable) — processing on your behalf needs disclosure, not separate
   consent. Consent version + timestamp travel in `fields.consent` so every CRM record
   carries proof. WhatsApp-channel leads: consent asked in-chat before resale (§6).
7. **Pricing: no published prices confirmed.** Internal `price_band` field powers
   "presupuesto estimado" bands in form dropdowns and future calculators — never rendered as
   a price list.
8. **Slug taxonomy: locked in §2.** Flat `/materiales/{slug}/`, hierarchy in breadcrumbs,
   pivot namespaces reserved. Pivot adds URLs, never moves one.

**Parked (not build work):** lead pack pricing in Gs.; founding-supplier deal terms; whether
Ciudad del Este / Encarnación become zones later; when to raise 3→5 buyers per lead; pivot
timing.

## §9 Build log & handoff

*(Each phase appends a dated 5–10 line entry before merging: phase id + PR, what now exists,
decisions/deviations, where the next phase should look first.)*

### 2026-09-01 — Phase 1 Foundation (branch `phase/1-foundation`)

- **Exists now**: repo layout of §2; `public_html/` with homepage, `/materiales/` router
  (flat shared slug namespace, categories checked before materials), `/guias/` router,
  `cotizar`/`gracias`/`contacto`/`politica-de-privacidad`, `404.php`, `sitemap.php`
  (served as `/sitemap.xml`), static `robots.txt`, `.htaccess` rewrites, `partials/`
  (init, header, footer trust stack, schema builders, cookie-banner shell + consent.js),
  minimal placeholder CSS.
- **Data**: `data/site.php` (NAP empty and marked PENDIENTE in comments only — never on-page),
  `categories.php` (13: the 5 recruited = `activa`, the 8 expansion = `proxima`),
  `materials.php` (34: 28 wave-1 `activa`, 6 madera `proxima`), plus a new `guides.php`
  (6 wave-1 guías, `proxima`) — that file is a deviation, see KNOWN-ISSUES #1. Every entry
  ships its real title (≤60) and meta (≤155); prose/FAQ/synonyms land in phase 3.
- **CI**: one `pull_request` job (`check`), ≤5 min — `php -l` over all PHP, `tools/smoke.php`
  (slug uniqueness across categories+materials, cross-file references, status coherence,
  title/meta limits) and `tools/render-check.sh` (router, sitemap, 404 via `php -S`).
  Actions is not in the deploy path; deploy is the Hostinger Git webhook — see `DEPLOY.md`.
- **Decisions/deviations**: `SearchAction` withheld behind a `has_search` flag (no internal
  search exists — flagged for veto, KNOWN-ISSUES #2); `data/guides.php` added; rewrite rules
  are mirrored in `tools/router-cli.php` for local/CI serving; `staging_noindex` ships `true`.
- **Phase 2 starts here**: `public_html/cotizar/index.php` (page exists, form does not),
  `config.sample.php` (VenderCRM url/api_key/timeout), `partials/cookie-banner.php` +
  `assets/js/consent.js` (fires a `consent:changed` event for GA4/Pixel gating), and
  `page()`/`site()` helpers in `partials/init.php`.

## §10 Backlog

- Automated supplier fan-out as a VenderCRM automation (not site PHP)
- WhatsApp-ping phone validation at volume
- `AggregateOffer` PYG ranges if public bands ever maintained
- Materials calculator (m³/bolsas) as interactive guía
- `/proveedores/{empresa}/` public pages (the pivot)
- Cloudflare Turnstile if honeypot stops sufficing
- Daily automated replay of failed CRM posts from leads.log (cron)
