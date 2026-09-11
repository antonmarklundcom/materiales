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
| **5 Keyword expansion** (PRs 5a · 5b · 5c) | **Opus** | `prompts/opus-5-keyword-expansion.md` | §5.1: spec amendments, new materials in active categories, promotion of 5 categories with their material data, 2 new guías — everything the prose phases build on |
| 6 Content wave 1 | Sonnet | `prompts/sonnet-6-content-wave1.md` | Prose for the 5 recruited categories, their materials (incl. the 5a additions) and the 8 guías |
| 7 Content wave 2 | Sonnet | `prompts/sonnet-7-content-wave2.md` | Prose for the promoted categories (pisos, aberturas, impermeabilizantes, yeso, plomería, madera) |
| 8 Imagery + QA + launch | Sonnet | `prompts/sonnet-8-imagery-qa-launch.md` | OG images, image slots, SEO QA, go-live checklist, closing report |
| **9 Home & conversion layer** | **Opus** | `prompts/opus-9-conversion-window.md` (PR 1/4) | §11.1: homepage rebuild, hero CTA on money pages, sticky mobile CTA, WhatsApp secondary, /gracias/ next steps |
| **10 Supplier recruitment** | **Opus** | same file (PR 2/4) | §11.2: `/proveedores/` landing, supplier form + `tipo=proveedor` + `proveedor-v1` consent, privacy clause |
| **11 Cross-link engine & image slots** | **Opus** | same file (PR 3/4) | §11.3: data-driven "Guías relacionadas" / "Calculadoras relacionadas", optional `image` key + hero picture + per-page og:image |
| **12 Calculators foundation** | **Opus** | same file (PR 4/4) | §11.4: `/calculadoras/` route, `data/calculators.php` contract, template, `calc.js`, CONTENT-SPEC §12, one exemplar |
| 13 Calculators + guías wave 3 | Sonnet | `prompts/sonnet-13-content-window.md` (PR 1/3) | §11.5: 3 more calculators, 6 new guías (fan-out) |
| 14 Technical hardening | Sonnet | same file (PR 2/3) | §11.6: .htaccess deflate/cache/headers, self-hosted fonts, sitemap lastmod, replay-leads tool, mobile overflow test |
| 15 Link pass + images + launch QA | Sonnet | same file (PR 3/3) | §11.7: editorial cross-links in prose, image placement, KNOWN-ISSUES promotion, docs/log index, closing report |

Phases 1–4 are merged (§9). Phases 5–8 were re-planned on 2026-09-06 from the keyword
research in `KEYWORDS-MATERIALES.md` (Fable planning session; that file is the content input
for every remaining phase). **Windows:** phase 5 is ONE Opus window that ships three PRs in
sequence; phases 6–8 are ONE Sonnet window that ships three PRs in sequence (fan-out to
Sonnet subagents inside each PR for same-shaped pages). No spawning, no watcher: the Opus
window ends with a report telling Anton to paste the Sonnet line; the Sonnet window ends
with the closing report.

Per the model cost guardrail: phases only ever run on Opus or Sonnet — never Fable.

**Improvement build (added 2026-09-11, Fable review — see `docs/IMPROVEMENT-REPORT.md`).**
Phases 9–15 in §11. Same window pattern as §1.16: phases 9–12 are ONE Opus window that
ships four PRs in sequence; phases 13–15 are ONE Sonnet window that ships three PRs in
sequence. Opus ships first so every Sonnet PR starts on a finished foundation. Go-live
(NAP, CRM config, DNS, `staging_noindex`) does NOT wait for any of these phases.

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

Added 2026-09-06 from the keyword research (`KEYWORDS-MATERIALES.md`), decided by Fable in
planning — build sessions never reopen them:

11. **Keyword ownership per page** is written in CONTENT-SPEC §11 (phase 5a). One head term
    belongs to exactly one page; every other spelling/variant of it is a synonym on that
    page. Bare `chapa`, `cal`, `arena`, `hierro`, `madera` belong to the CATEGORY pages.
12. **Brand-as-generic exception to the no-brands rule.** Paraguayan buyers search the
    brand as the product name: durlock, isopanel, blindex, eternit, syopar, sikaflex,
    canaleta tigre, caño amanco. These words are allowed inside prose, synonyms and FAQ
    text — never in H1/title, never as a page, never as a supplier or "we sell X"
    claim. The list is closed in CONTENT-SPEC §11; anything not on it stays prohibited.
13. **Price-intent FAQ.** Every material page carries a visible FAQ `¿Cuánto cuesta
    {material}?` answered with the factors (unidad, cantidad, flete, zona) and the CTA —
    never a number. This is how ~2.500 monthly "precio" searches are served without
    breaking decision 5.
14. **Sanitarios y grifería, herrajes/cerraduras, herramientas and pinturas stay out of
    this build.** Sanitarios (~9k/mo) is showroom retail with no bids; herrajes and tools are
    the Productos bucket; pinturas is the largest cluster but the lowest bids and DIY
    intent. All four go to §10 Backlog. ⚠️ Flagged for veto — sanitarios is the one Anton
    may want back in if the quote pipeline can route bathroom fit-outs.
15. **Promotion order changes** to money-on-the-table order (§5.1), replacing the
    supplier-availability order: pisos-y-revestimientos → aberturas → impermeabilizantes →
    yeso-y-durlock + canos-y-plomeria → madera. Electricidad and pinturas remain próxima.
16. **One window per model for the rest of the build** (Anton, 2026-09-06): phase 5 is one
    Opus window (3 PRs), phases 6–8 one Sonnet window (3 PRs). §4.9's cross-model
    `create_session` handoff is replaced by §4.12.

Added 2026-09-11 from the improvement review (`docs/IMPROVEMENT-REPORT.md`), decided by
Fable in planning — build sessions never reopen them:

17. **`/proveedores/` is a recruitment landing page, not the pivot.** It lives at the
    reserved namespace root and the pivot's `/proveedores/{empresa}/` pages are added under
    it later — nothing moves. Its form posts to the SAME handler with a hidden
    `tipo=proveedor`, which travels as `fields.tipo` (buyer leads send no `tipo`; the
    handler never defaults it). Supplier consent is a SEPARATE text and version
    (`proveedor-v1`, in `data/site.php` `consent_version_proveedor`) because a supplier is
    not consenting to be shared with suppliers. Source stays `site:materiales`; routing by
    `fields.tipo` is configured in VenderCRM, never in PHP. No supplier deal numbers are
    invented: terms come from `data/site.php` `supplier_pitch` and the page degrades to
    "te contamos las condiciones por WhatsApp" when empty.
18. **The form is the primary CTA everywhere; WhatsApp is secondary and only renders when
    `data/site.php` `whatsapp` is set.** The sticky mobile bar carries the form anchor
    first, WhatsApp second. Calendar/phone-call CTAs stay dropped (§6).
19. **Cross-links between page types are computed from data, not typed into prose.**
    A material or category page lists every guía (and later calculator) whose `related[]`
    names it or its category; a guía lists its `related[]` money pages (already does).
    Editorial in-prose links are added by the link pass (phase 15) on top, never instead.
20. **Images are an optional `image` key per category, material and guía, one photo per
    CATEGORY by default** (`docs/imagery-brief.md`): a material without its own `image`
    inherits its category's; a guía inherits its first `related[]` money page's. Path
    convention `assets/img/cat/{category-slug}.jpg`, `assets/img/hero-home.jpg`, 1200×630
    JPG ≤ 150 KB, used for the hero `<picture>` and `og:image`. Missing file ⇒ the page
    renders exactly as today (palette hero, `og-default.jpg`). The smoke test fails only
    on a DECLARED image whose file is missing. Generating the files is a human step (§7).
21. **Calculators live at `/calculadoras/{slug}/`, own data file `data/calculators.php`,
    own content dir `content/calculadoras/`, plain PHP + one vanilla JS file
    (`assets/js/calc.js`), no build step.** Every calculator: (a) computes client-side
    with the formula ALSO written in the page as prose (works without JS: the prose is
    the fallback and the SEO content); (b) states its assumptions and ends every result
    with "es una referencia — confirmá con tu proveedor"; (c) ends in the quote form with
    `material` preselected and `cantidad` pre-filled from the result. Dosages are the
    standard textbook ones (documented in CONTENT-SPEC §12 with the source line); nothing
    PY-specific is invented. Never a price, never a currency.
22. **`data/guides.php` and `data/calculators.php` entries may be ADDED by Sonnet phases**
    (relaxes §4.13 for those two files only): same key shape as the exemplar, `status`
    `activa` only in the PR that ships the prose/page, no new keys, no edits to existing
    entries beyond `related[]` additions. `data/materials.php` and `data/categories.php`
    stay Opus-only except for the `image` value and the `status` flip.
23. **No city pages.** `/zonas/{ciudad}/` stays reserved; KEYWORDS §4.3 local terms are
    served by the homepage prose and the form's `ciudad` field.
24. **Fonts are self-hosted** (`assets/fonts/*.woff2`, `@font-face`, `font-display: swap`)
    — no third-party request on page load. Same two families, same weights.
25. **`sitemap.xml` `lastmod` comes from an optional `updated` (YYYY-MM-DD) key on the data
    entry, never from `filemtime`** (Hostinger's git deploy rewrites mtimes on every push).
    No `updated` ⇒ no `lastmod` for that URL.
26. **Failed CRM posts are replayed by `tools/replay-leads.php`** (CLI, idempotent by the
    logged `idempotency_key`, marks each replayed line in a sidecar `storage/replayed.log`,
    never re-sends a line whose logged CRM status was 200/201). Hostinger cron, hourly.
    Documented in DEPLOY.md; no web endpoint.

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
12. **Windows and PR sequence (phases 5–8, replaces §4.9's spawn step).** A window runs its
    PRs strictly in order: branch off latest main, open PR, arm auto-merge, verify merged
    via `mcp__github__*`, write the §9 log line, then start the next PR in the SAME window.
    At the model switch (after 5c) the Opus window STOPS and reports the exact Sonnet line
    to paste; it spawns nothing. The Sonnet window stops after PR 8 with the closing
    report. No watcher Routine. Same-shaped units inside a PR (N ≥ 4 material pages) fan
    out to parallel Sonnet subagents per `fable-directs-sonnet-builds` §Fan-out: the window
    writes one exemplar first, fans out the rest, runs one verify, opens one PR. Per-PR
    budget ≤ 90 min; a PR still polishing at minute 60 stops polishing (§4.13 of the
    method skill applies: one screenshot pass, one verify reported).
13. **What Sonnet may change in `data/*.php`:** only the `status` value of a category,
    material or guide, from `proxima` to `activa`, and only in the same PR that ships that
    page's prose. Never keys, never other values, never new entries — those are 5a–5c work.
    `tools/smoke.php` enforces that an `activa` material sits in an `activa` category, so
    the category flips in the PR that completes its material set.
14. **Phase logs (phases 9+).** Before merging, write `docs/log/<phase>.md` (≤ 12 lines
    "Built", ≤ 8 "Decisions", ≤ 8 "Known issues", one "Verification" line) and add ONE
    index line to §9. No more multi-paragraph §9 entries; the detail lives in the log file.
15. **Prompt re-read.** Before opening each PR and before merging it, `git fetch` and re-read
    your prompt file from `origin/main`; if it changed, follow the newer version. Decisions
    travel by files, never by chat.

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

Expansion order — superseded 2026-09-06 by §1.15 / §5.1: pisos-y-revestimientos →
aberturas → impermeabilizantes → yeso-y-durlock + canos-y-plomeria → madera. Electricidad
and pinturas stay próxima (the original order was madera → aberturas → caños → pisos →
electricidad → pinturas → yeso → impermeabilizantes; the keyword data reversed it).

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

### §5.1 Keyword-driven expansion (added 2026-09-06; source: `KEYWORDS-MATERIALES.md`)

Read that file's §1–§2 for the volumes and §5 for the cannibalization map. What it changes:

**PR 5a — CONTENT-SPEC amendments (Opus, small, first).** Append CONTENT-SPEC §11
"Keyword ownership and vocabulary" with: (a) the head-term → page table for every activa
page after 5b/5c (one row per page: owned head terms, synonyms to weave in, terms that
belong elsewhere), built from KEYWORDS-MATERIALES §5.1; (b) the closed brand-as-generic
list (§1.12); (c) the mandatory `¿Cuánto cuesta {material}?` FAQ pattern and its answer
template (§1.13); (d) a "Medidas" rule: any material with a size/gauge/diameter long-tail
(varilla, ladrillo común, ladrillo hueco, chapa trapezoidal, tanque de agua, policarbonato,
terciada) carries a measures table under `Qué mirar antes de comprar`; (e) outlines
(H2s + mandatory links) for the two new guías below. Also amend the CONTENT-SPEC §5 material
structure so the second H2 reads `Cómo se vende, cómo pedirlo y de qué depende el precio`.
No data changes in 5a.

**PR 5b — new materials inside the 5 active categories (Opus).** Full closed entries in
`data/materials.php` (keyword, title, meta, synonyms, sale_unit, price_band, intro, 3–5 FAQ
incl. the precio FAQ, related), status `activa`:

| Slug | Category | Head terms it owns (vol) | Synonyms |
|---|---|---|---|
| cielorraso-de-pvc | chapas-y-techos | pvc para techos 1.300 · cielorraso de pvc 1.000 · cielo raso pvc 390 | techo de pvc, machimbre de pvc, cielorraso |
| policarbonato | chapas-y-techos | techos de policarbonato 590 · policarbonato techo 390 | alveolar, compacto, claraboya, 6/8/10 mm (table) |
| canaletas | chapas-y-techos | canaletas 590 · canaleta embutida 590 · canaletas de pvc 260 | canaleta para techo, desagüe pluvial, bajada, canaleta de chapa galvanizada |
| tejido-de-alambre | hierro | tejido de alambre 1.300 · alambre de púas 480 | tejido romboidal, alambrado, alambre galvanizado, malla para cerco |
| ladrillo-refractario | ladrillos-y-bloques | ladrillo refractario 880 | ladrillo para parrilla, ladrillo para horno |
| adoquines | ladrillos-y-bloques | adoquines 1.000 · adoquinado | adoquín de hormigón, adopasto, adoquín ecológico |

Synonym additions to existing entries: alambre-negro += `alambre dulce`; tierra-gorda +=
`tierra colorada`; ripio → `canto rodado` becomes the first synonym and opens the intro;
ladrillo-prensado → `ladrillo visto` first; ladrillo-hueco += `ladrillo sapo` (verify);
teja-espanola += `teja romana`; chapa-termoacustica += `chapa sandwich`, `techo sandwich`;
chapa-de-zinc += `chapa galvanizada`, `chapa ondulada`; cemento += `cemento blanco`,
`mortero premezclado` (as FAQ lines, not pages); perfiles-metalicos += `perfil C`, `perfil
U`, `IPN`, `UPN`, `ángulo` (table). `data/guides.php` gets two entries, status proxima:
`como-revocar-una-pared` (revoque/revocado ~1.800; links cal-hidratada, arena-lavada,
cemento) and `losa-de-hormigon-encofrado-y-hierro` (hormigón armado, encofrado, losas,
zapatas ~1.900; links hormigon-elaborado, varilla-de-hierro, tabla-de-encofrado). Update
`intro_keywords[]` of the 5 categories with their newly owned head terms. Smoke green.

**PR 5c — promotion of five categories (Opus).** Flip these categories to `activa` and
author their full material entries (status `activa`; prose comes in phase 7):

| Category | Materials (slug — head terms) |
|---|---|
| pisos-y-revestimientos | ceramica-para-piso (cerámica 1.600, cerámicos, pisos de cerámica) · porcelanato (1.600, pisos porcelanato 1.300, símil madera) · azulejos (azulejos para baño 1.000, azulejos 720, para cocina 720) · piso-vinilico (piso vinílico 880, vinílicos adhesivos 720, piso flotante 320, SPC) · piedra-laja (piedra laja 320, revestimiento de piedra 320, símil piedra) |
| aberturas | puerta-placa (puerta placa 480, puertas de madera 1.000 interior) · puertas-de-madera (exterior, 1.000 · bid 25,81) · puertas-de-chapa (puertas de metal 1.300, puertas metálicas 320) · ventanas-de-aluminio (ventanas 880, ventanas de aluminio 210, perfiles de aluminio 480, carpintería de aluminio) · vidrio-templado (vidrio 880, vidrio templado 590, blindex 880) · portones-y-rejas (portones de hierro 720, rejas 480+480+590, portón basculante 480 — captures the herrería lead) |
| impermeabilizantes | membrana-asfaltica (membrana para techo 1.300, membrana 880, membranas asfálticas 390) · membrana-liquida (590, para techos 260) · pintura-antihumedad (880, antihumedad 210) · hidrofugo (impermeabilizante 590, para techos 210) · selladores-y-siliconas (silicona 1.000, silicona fría 590, sikaflex 590, sikacryl 260) |
| yeso-y-durlock | placa-de-yeso (durlock 1.900, placas de yeso 170, cielorraso durlock 260) · yeso-en-polvo (yeso 880, yeso para pared 390) · perfiles-para-durlock (montante, solera 90) |
| canos-y-plomeria | tanque-de-agua (tanque de agua 1.300, 1000 litros 720, 500 litros 210, syopar 320 — sizes table) · cano-de-pvc (caño 480, caños pvc 210, tubos pvc 140) · cano-de-agua (termofusión/PPR — verify volume in §4 of the research before authoring; drop if none) |
| madera (already has 6 proxima entries) | flip to activa; add mdf-fibrofacil (material mdf 590, fibrofácil 590, madera mdf 480) · madera-dura (curupay 480, lapacho, eucalipto — species table) |

Sanitarios, herrajes, herramientas, pinturas, electricidad: NOT authored (§1.14). Every
new entry passes `tools/smoke.php` (closed content) and the slug-uniqueness check. Titles
≤ 60 chars, metas ≤ 155, voseo, no prices, no brands outside the §1.12 list.

**Phases 6–7 write the prose** to CONTENT-SPEC §5 structure (350–600 words per material,
250–450 per category, 600–900 per guía), flipping guide/category `status` per §4.13.
Phase 6 order = KEYWORDS-MATERIALES §2 "write-first list"; phase 7 order = §1.15.

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
| Now (go-live, independent of phases 9–15) | NAP, `config/vendercrm.php`, GA4/Pixel IDs, DNS, `staging_noindex => false`, Search Console — see `docs/IMPROVEMENT-REPORT.md` §5 |
| Phase 10 | Supplier pitch terms for `/proveedores/` (`data/site.php` `supplier_pitch`); empty ⇒ page says terms come by WhatsApp |
| Before phase 15 | Image files per `docs/imagery-brief.md` committed to `assets/img/cat/{slug}.jpg` + `assets/img/hero-home.jpg` (generate from your PC, or allow `*.cloudfront.net` in the Claude Code environment so phase 15 can run `higgsfield-image-pipeline`) |
| After phase 13 | Second Keyword Planner pull (KEYWORDS-MATERIALES §6) → `docs/keywords/` for the next planning session |

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

Index (phases 5–8 fill their line in the PR that merges them):

| PR | Window | Status | PR # | Entry |
|---|---|---|---|---|
| 5a spec amendments | Opus | ✅ | #8 | 2026-09-06 abajo |
| 5b new materials, synonyms, guías | Opus | ✅ | #9 | 2026-09-06 abajo |
| 5c category promotion | Opus | ✅ | #10 | 2026-09-06 abajo |
| 6 content wave 1 | Sonnet | ✅ | #11 | 2026-09-06 abajo |
| 7 content wave 2 | Sonnet | ✅ | #12 | 2026-09-06 abajo |
| 8 imagery + QA + launch | Sonnet | ✅ | #13 | 2026-09-06 abajo |
| 9 home & conversion | Opus | ✅ | #18 | `docs/log/9-home-conversion.md` |
| 10 proveedores | Opus | ⏳ | — | `docs/log/10-proveedores.md` |
| 11 cross-links & image slots | Opus | ⏳ | — | `docs/log/11-crosslinks-images.md` |
| 12 calculators foundation | Opus | ⏳ | — | `docs/log/12-calculadoras-foundation.md` |
| 13 calculators + guías wave 3 | Sonnet | ⏳ | — | `docs/log/13-calculadoras-guias.md` |
| 14 technical hardening | Sonnet | ⏳ | — | `docs/log/14-tech-hardening.md` |
| 15 link pass + images + launch QA | Sonnet | ⏳ | — | `docs/log/15-link-pass-launch.md` |

**2026-09-06 — Fase 8 · Imagery + QA + launch (Sonnet, PR #13, FINAL).**
- Preflight de `higgsfield-image-pipeline` Rule 0/2: no había manifest de imágenes, y
  `curl -sI` contra `*.cloudfront.net` devuelve 403 (bloqueado por la política de red del
  entorno). Regla del propio skill: "CDN unreachable: stop before spending credits and tell
  Anton the one-line fix." No se generó ninguna imagen con Higgsfield ni se gastaron créditos.
- En su lugar, `tools/generate-og-default.php` (GD + tipografía Bricolage Grotesque local)
  genera `public_html/assets/img/og-default.jpg` (1200×630): motivo de paleta con los tokens
  del sitio (fondo `#0e0e0f`, acento `#e8562a`), sin fotos ni rostros. `partials/header.php`
  ya lo sirve como fallback sitewide desde la fase 1 — no hace falta tocar plantillas.
  KNOWN-ISSUES #22 deja escrito el paso para generar fotografía real por página cuando el
  dominio esté permitido.
- QA SEO ejecutado a mano (los skills `seo-web-builds` y `web-design-system` no existen en
  esta sesión, mismo desvío que KNOWN-ISSUES #21): título ≤ 60 y meta ≤ 155 verificado por
  script en las 85 entradas de datos (13 categorías + 64 materiales + 8 guías); un `<h1>` por
  página, canonical y JSON-LD válido verificados en home, índice, categoría, material, índice
  y detalle de guía; sin precios, sin "tú", sin marca fuera de la lista cerrada de §11.2
  (verificado con grep); `robots.txt` + `sitemap.xml` correctos; ronda del formulario de leads
  verificada en modo sólo-log (sin `config/vendercrm.php` en el repo, como corresponde).
- Una pasada de capturas (`chromium --headless --screenshot`, 5 páginas × escritorio/mobile)
  pareció encontrar un bug de header desbordado en 390px — anotado como KNOWN-ISSUES #23.
  **Descartado en una sesión posterior (2026-09-06, misma tarde):** esa herramienta arma la
  ventana con `--window-size` en vez de fijar el viewport, así que medía un DOM más ancho que
  390px y la captura salía recortada. Repetido con Playwright (viewport real, 320–390px, home
  y una página de material): `scrollWidth === clientWidth` en los cuatro anchos, el nav ya
  wrappea con `flex-wrap: wrap` sin cortar nada. Sin cambios de CSS porque no hacía falta
  ninguno; KNOWN-ISSUES #23 queda tachado con la explicación.
- `staging_noindex` se deja en `true` (el dominio no está apuntando todavía). `php -l`,
  `tools/smoke.php` y `tools/render-check.sh` en verde.
- **Build de fases 5–8 cerrado.** No queda contenido ni código pendiente; lo que sigue son los
  pasos manuales de Anton (informe de cierre en la PR) y KNOWN-ISSUES #22 (fotografía real
  bloqueada por el entorno).

**2026-09-06 — Fase 7 · Content wave 2 (Sonnet, PR #12).**
- Prosa completa de las 6 categorías promovidas en 5c (`pisos-y-revestimientos`, `aberturas`,
  `impermeabilizantes`, `yeso-y-durlock`, `canos-y-plomeria`, `madera`) y sus 30 materiales
  activos — 36 archivos nuevos en `content/`. El catálogo entero queda escrito: 11 categorías,
  64 materiales, 8 guías, ninguna página activa muestra ya el aviso "estamos publicando el
  contenido". Mismo patrón de fan-out que la fase 6: un subagente Sonnet por categoría, en
  paralelo, cada uno con su porción de CONTENT-SPEC §11.1.
- Aberturas se escribió con el ángulo de producto a medida (§ propio de esta fase): el H2 de
  precio pide medida real de vano, no una unidad de venta fija, y ninguna página dice
  "instalamos" — el sitio conecta con el proveedor, no fabrica.
- Tabla de medidas obligatoria (§11.4) en `tanque-de-agua` (litrajes) y `terciada` (espesores);
  tabla recomendada en `ceramica-para-piso`, `porcelanato`, `cano-de-pvc` y `madera-dura`.
- Marcas-genérico (§11.2) verificadas una por una tras el fan-out: `durlock` sólo en
  `placa-de-yeso`, `blindex` sólo en `vidrio-templado`, `syopar` sólo en `tanque-de-agua`,
  `sikaflex` sólo en `selladores-y-siliconas`, `caño amanco` sólo en `cano-de-pvc`. Dos
  subagentes las habían repetido también en la prosa de categoría (`yeso-y-durlock.php` con
  "durlock", `aberturas.php` con "blindex") — las saqué de ahí, mismo criterio que "eternit" en
  la fase 6.
- Ningún material quedó `proxima` por prosa insuficiente (§ regla de "thin is a defect"): las
  30 páginas llegaron a 350+ palabras genuinamente distintas; sólo `listones.php` quedó corto
  (335) y lo extendí a 382 con contenido real, no relleno.
- `php -l`, `tools/smoke.php` y `tools/render-check.sh` en verde; conteo de palabras verificado
  por script (materiales 350–600, categorías 250–450).
- **La fase 8 empieza acá**: `prompts/sonnet-8-imagery-qa-launch.md` — imágenes OG, QA SEO y
  checklist de salida sobre el catálogo ya completo.

**2026-09-06 — Fase 6 · Content wave 1 (Sonnet, PR #11).**
- Prosa completa de las 5 categorías de lanzamiento (`hierro`, `cemento-y-cal`, `aridos`,
  `ladrillos-y-bloques`, `chapas-y-techos`) y sus 34 materiales activos, más las 8 guías —
  47 archivos nuevos en `content/`. Escribí `chapa-termoacustica.php` a mano como ejemplar
  (write-first list de KEYWORDS-MATERIALES §2) y repartí el resto en 6 subagentes Sonnet en
  paralelo, uno por categoría más uno para las guías, cada uno con su porción de
  CONTENT-SPEC §11.1 y los datos ya cerrados de `data/materials.php` — sin tocar ningún
  archivo de datos salvo `status` en `data/guides.php` (§4.13).
- Tablas de medidas obligatorias (§11.4) en `varilla-de-hierro`, `ladrillo-comun`,
  `ladrillo-hueco`, `chapa-trapezoidal` y `policarbonato`; tabla recomendada en
  `piedra-triturada`, `perfiles-metalicos` y `chapa-de-zinc`.
- Las 8 guías pasan de `proxima` a `activa` en `data/guides.php` junto con su prosa; las dos
  nuevas de la fase 5a (`como-revocar-una-pared`, `losa-de-hormigon-encofrado-y-hierro`)
  también cierran acá.
- QA post-fan-out: revisé a mano que ningún término de marca se colara fuera de su página
  dueña (saqué "eternit" de la prosa de la categoría `chapas-y-techos`, donde sólo
  `fibrocemento.php` puede usarlo) y corregí un guion mal puesto en `canaletas.php`. Conteo
  de palabras verificado por script: los 34 materiales entre 350–600, las 5 categorías entre
  250–450, las 8 guías entre 600–900 (extendí 3 guías que habían quedado justo debajo del
  mínimo).
- `php -l`, `tools/smoke.php` y `tools/render-check.sh` en verde; confirmé a mano que ninguna
  de las páginas tocadas sigue mostrando el aviso "estamos publicando el contenido" y que
  `sitemap.xml` lista las 8 guías.
- **La fase 7 empieza acá**: `prompts/sonnet-7-content-wave2.md`, prosa de las 6 categorías
  promovidas en 5c (pisos-y-revestimientos, aberturas, impermeabilizantes, yeso-y-durlock,
  canos-y-plomeria, madera) y sus materiales, en el orden de §1.15.

**2026-09-06 — Fase 5c · Promoción de categorías (Opus, PR #10).**
- 6 categorías a `activa` en el orden de §1.15 (pisos-y-revestimientos, aberturas,
  impermeabilizantes, yeso-y-durlock, canos-y-plomeria, madera) con 24 materiales nuevos
  escritos con el mismo estándar que 5b. Quedan 13 categorías (11 activas), 64 materiales
  (todos activos), 8 guías y 77 slugs únicos. `electricidad` y `pinturas` siguen `proxima`.
- `title`, `meta`, `intro`, `intro_keywords` y `faq` de las 6 promovidas reescritos a los
  términos de CONTENT-SPEC §11.1 (estaban escritos a ciegas en la fase 3: la meta de plomería
  prometía grifería, que está fuera del build). Las 5 de lanzamiento no se tocaron; la
  excepción queda anotada en CONTENT-SPEC §3.
- `cano-de-agua` se escribió pese a que `termofusión`/`PPR` no tienen volumen medido: lidera
  con `caño` (480, la puja más alta del clúster) y el desagüe y el agua a presión son productos
  distintos. Si el segundo pull vuelve vacío, se funde con `cano-de-pvc` (KNOWN-ISSUES #19).
- `tools/smoke.php` suma dos checks de contenido cerrado: una categoría `activa` necesita ≥ 3
  materiales activos, y la última FAQ de todo material tiene que ser la de precio. Verificados
  con un fallo provocado.
- Sin auto-merge en el repo (§7): las tres PR de la fase se mergearon a mano con CI en verde.
- **La fase 6 empieza acá**: `CONTENT-SPEC.md` §5 (estructura de prosa, con el segundo H2
  nuevo), §11.1 (qué término persigue cada página), §11.3 (la FAQ de precio ya está en el dato,
  no se reescribe) y §11.4 (qué páginas llevan tabla de medidas). Orden de escritura: la
  "write-first list" de KEYWORDS-MATERIALES §2.

**2026-09-06 — Fase 5b · Materiales nuevos, sinónimos y guías (Opus, PR #9).**
- 6 materiales nuevos con contenido cerrado, status `activa`: `cielorraso-de-pvc`,
  `policarbonato` y `canaletas` (chapas-y-techos), `tejido-de-alambre` (hierro),
  `ladrillo-refractario` y `adoquines` (ladrillos-y-bloques). 40 materiales, 34 activos,
  53 slugs únicos.
- **Las 40 entradas cierran su `faq[]` con `¿Cuánto cuesta …?`** (CONTENT-SPEC §11.3). Las 34
  que ya existían también la necesitaban: desde la fase 6 Sonnet sólo puede tocar `status`
  (§4.13), así que ésta era la única fase que podía agregarla.
- Sinónimos e intros corregidos según KEYWORDS §1/§5.1 (canto rodado abre `ripio`, ladrillo
  visto abre `ladrillo-prensado`, alambre dulce, tierra colorada, teja romana, chapa sandwich,
  chapa ondulada, perfil U/IPN/UPN, cemento blanco y mortero premezclado).
- 2 guías nuevas `proxima` (`como-revocar-una-pared`, `losa-de-hormigon-encofrado-y-hierro`) e
  `intro_keywords` de las 5 categorías activas alineadas con la tabla §11.1.
- Desvíos anotados: `ladrillo sapo` queda **sin dueño** por no poder verificar a qué ladrillo
  nombra (KNOWN-ISSUES #18); la FAQ de entrega de `cemento` se reemplazó porque el smoke
  limita a 5 FAQ y su contenido pasó a la respuesta de precio (#20); los skills
  `paraguay-local-site` y `seo-web-builds` no existen en esta sesión (#21).
- Dónde mirar primero en 5c: `data/categories.php` (las 5 a promover conservan title/meta pero
  cambian `intro_keywords` y `faq`) y `CONTENT-SPEC.md` §11.1, que ya lista los 21 materiales
  a escribir y qué término posee cada uno.

**2026-09-06 — Fase 5a · Enmiendas al CONTENT-SPEC (Opus, PR #8).**
- `CONTENT-SPEC.md` §11 nuevo: §11.1 tabla de propiedad de keywords con una fila por página
  que queda `activa` después de 5c (11 categorías + sus materiales, incluidos los de 5b/5c),
  con las columnas posee / teje / no es suya; §11.2 lista cerrada de ocho marcas-genérico;
  §11.3 patrón de la FAQ `¿Cuánto cuesta {material}?`; §11.4 regla de medidas; §11.5 esquemas
  de las dos guías nuevas. Fuente: `KEYWORDS-MATERIALES.md` §1, §2, §3.4 y §5.1.
- `CONTENT-SPEC.md` §5: el segundo H2 de material pasa a `Cómo se vende, cómo pedirlo y de qué
  depende el precio`.
- Sin cambios en datos, plantillas, router ni CSS. Smoke y `php -l` en verde sin variación.
- Decisiones registradas: `piso parquet` y `melamina` sin página (Backlog); `caño conduit` y
  `canaleta para cable` quedan en electricidad (`proxima`, sin dueño); la fila de `cano-de-agua`
  queda anotada porque `termofusión`/`PPR` no tienen volumen medido (KEYWORDS §4.2).
- Dónde mirar primero en 5b: `CONTENT-SPEC.md` §11.1 (qué término va en qué página), §11.3
  (FAQ de precio obligatoria) y `data/materials.php` → `varilla-de-hierro` como entrada modelo.

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

### 2026-09-01 — Phase 2 Lead pipeline (branch `phase/2-lead-pipeline`)

- **Exists now**: the §3 flow end-to-end. `partials/lead.php` (merged earlier as a library)
  is now driven by `partials/form.php` — material preselected, cantidad, ciudad, nombre,
  teléfono, mensaje, unticked consent box, honeypot and a signed render stamp — included on
  `/cotizar/` and on every category and material page. `cotizar/enviar.php` is the HTTP shell:
  bots (honeypot / stamp) get a silent 303 to `/gracias/` and post nothing; bad phone or
  unticked consent bounce back to the form with `?error=`; the happy path builds the payload,
  posts to VenderCRM, appends to `storage/leads.log` and 303s to `/gracias/?m=&k=`.
- **Analytics**: `partials/analytics.php` + `assets/js/analytics.js` inject GA4 only after
  statistics consent and the Meta Pixel only after marketing consent — nothing downloads
  before that. `cotizacion_form_submitted` and `Lead` fire once per `k` token (remembered in
  sessionStorage), so refreshing `/gracias/` cannot inflate conversions. `vc-attribution.js`
  loads sitewide per §3 and is now named in the privacy policy.
- **Privacy**: `/politica-de-privacidad/` names `vc_attr`, GA4 and Meta Pixel individually,
  and cites Ley 7593 for the data-subject rights. VenderCRM was already named as encargado.
- **Decisions/deviations**: error redirects carry only non-personal fields — a phone number in
  the URL would end up in `Referer` and in GA4's `page_location` (KNOWN-ISSUES #11); the
  one-use `k` token was added on top of the plan's `?m=` so analytics can dedupe; fixed a bug
  in the merged `lead_safe_path()` where `parse_url()` turned `https://evil.example/x` into
  `/x` instead of the fallback.
- **CI**: `tools/smoke.php` now unit-tests `lead.php` (phone formats, idempotency stability,
  stamp forgery/expiry, cookie-wins attribution, payload shape including the never-sent
  routing keys, `leads.log` write) and pins the consent text so changing it fails CI (§4.4).
  `tools/render-check.sh` drives 10 real POSTs through the handler.
- **Phase 3 starts here**: `data/materials.php` needs `price_band` (KNOWN-ISSUES #9), and
  `data/site.php` still needs the real NAP plus `ga4_id` / `meta_pixel_id` / `vc_attribution`
  (§7). No round trip against a live CRM has been run — `config/vendercrm.php` does not exist
  yet, so everything above is verified in leads.log-only mode.

**2026-09-01 — Fase 3 · Content spec (Opus, PR #5).**
- Contenido cerrado en datos: 13 categorías con keyword, intro, `intro_keywords` y 3–4 FAQ
  con respuesta; 34 materiales con keyword, `synonyms` (vocabulario real de obra paraguaya),
  `sale_unit`, `price_band` interno, intro, 3–4 FAQ y `related`; keyword en las 6 guías.
  Los title/meta de la fase 1 se conservaron tal cual.
- `CONTENT-SPEC.md`: copy literal de home, índice, plantillas, formulario (incluida la frase
  de consentimiento y los mensajes de error), `/cotizar/`, `/gracias/`, estructura de prosa
  por tipo de página y esquemas de las 6 guías con sus anclas obligatorias.
- Desvío deliberado: la plantilla de `/materiales/` ahora **muestra** las FAQ, la bajada, la
  unidad de venta y los relacionados. Se emitía `FAQPage` sin preguntas visibles, que es
  marcado inexacto (plan §6).
- `tools/smoke.php` exige contenido cerrado y `related` válidos: una regresión de contenido
  rompe CI. Verificado con un fallo provocado.
- La fase 2 (PR #4) estaba verde pero sin mergear porque el repo no tiene auto-merge
  habilitado (§7); se mergeó a mano antes de empezar esta fase.
- Dónde mirar primero en la fase 4: `CONTENT-SPEC.md` (copy cerrado, no se reescribe),
  `public_html/materiales/index.php`, `public_html/partials/` y `assets/css/site.css`.
  Estado vivo del build: `STATUS.md`.

### 2026-09-01 — Fase 4 · Design & pages (Sonnet, branch `phase/4-design-pages`)

- **Track resuelto**: INDUSTRIAL (web-design-system) adaptado — cáscara oscura con grano
  (`.band--dark`) en header, hero de cada página y pie; campo claro ("paper") para prosa/FAQ/
  formulario, porque el catálogo son ~50 páginas de texto largo, no una landing única (nota
  KNOWN-ISSUES #15). Acento único `#E8562A`, Bricolage Grotesque + Inter vía Google Fonts
  (`display=swap`, sin autohospedar — KNOWN-ISSUES #14), tokens/tarjetas/botones/motion tal
  como especifica el skill.
- **Reescrito**: `assets/css/site.css` completo (tokens, tipografía, tarjetas, tiles de
  catálogo, formulario, FAQ-acordeón, banner de cookies, pie en cinta P8), más
  `assets/js/motion.js` (copiado del skill, reveal + header sticky) y `assets/js/events.js`
  (shim `data-ev`/`data-ev-loc` de analytics-prep, inerte, no reemplaza `analytics.js`).
  `partials/header.php` y `partials/footer.php` reestructurados en bandas; `partials/form.php`
  con botón primario y `data-ev="form_submit"`.
- **Todas las plantillas de página** (home, `/materiales/`, categoría/material, `/guias/`
  índice y detalle, `/cotizar/`, `/gracias/`, `/contacto/`, política de privacidad, 404)
  llevan hero oscuro full-bleed + panel claro que sube sobre el borde (overlap, patrón P6);
  el H1 de cada plantilla se mantiene sin atributos porque `tools/render-check.sh` verifica
  la subcadena literal `<h1>` en `/guias/` y `/cotizar/`. Copy verbatim de `CONTENT-SPEC.md` y
  de los archivos de datos — cero copy nueva (se descartaron dos borradores propios: una bajada
  para `/guias/` y un panel de cifras en el home que no estaban en el spec).
- **Sin cambios** en router, esquemas de datos, contrato del handler de leads, taxonomía de
  URLs ni texto de consentimiento (verificado: `name="consentimiento"` y
  `action="/cotizar/enviar.php"` intactos, smoke + render-check en verde).
- **QA**: `php -l` en todos los `.php`, `php tools/smoke.php`, `bash tools/render-check.sh` en
  verde; capturas de pantalla mobile/desktop de las 11 rutas revisadas a ojo (home, índice,
  categoría, material, guías índice/detalle, cotizar, gracias, contacto, privacidad, 404).
  Un bug real encontrado y corregido en el propio QA: el `clamp()` del margen negativo de
  `.field` tenía el mínimo y el máximo invertidos (quedaba fijo en vez de escalar con el
  viewport). Sin imágenes todavía (fase 6, KNOWN-ISSUES #16).
- **Fase 5 empieza acá**: escribir `content/categorias/{slug}.php` y
  `content/materiales/{slug}.php` para las 5 categorías de lanzamiento (hierro, cemento-y-cal,
  aridos, ladrillos-y-bloques, chapas-y-techos) y sus materiales, más las 6 guías — todo
  envuelto automáticamente en `.prose` por la plantilla, sin tocar `materiales/index.php` ni
  `guias/index.php`. `content/README.md` documenta el contrato de esos archivos.

## §10 Backlog

- Automated supplier fan-out as a VenderCRM automation (not site PHP)
- WhatsApp-ping phone validation at volume
- `AggregateOffer` PYG ranges if public bands ever maintained
- ~~Materials calculator (m³/bolsas) as interactive guía~~ → phases 12–13
- `/proveedores/{empresa}/` public pages (the pivot) — the `/proveedores/` landing is phase 10, the per-supplier pages are still the pivot
- `/zonas/{ciudad}/` city pages — only if genuinely localized content ever exists (§1.23)
- CI screenshot job — deliberately not added (runner minutes); `tests/mobile-overflow.mjs` runs locally
- Cloudflare Turnstile if honeypot stops sufficing
- ~~Daily automated replay of failed CRM posts from leads.log (cron)~~ → phase 14
- **Sanitarios y grifería** category (~9k/mo: inodoro, canillas, cisternas, ducha higiénica) —
  parked per §1.14; revisit if the quote pipeline can route bathroom fit-outs
- **Pinturas** prose (~15k/mo, bids < 5 kr, DIY intent) — category stays próxima
- **Electricidad** (cinta aisladora, caño conduit, cable canal) — thin in the Materiales bucket
- **Aditivos para hormigón** page (sikadur / sikagrout / sika 1, ~700/mo) — brand-heavy
- Second keyword pull: the 100 phrases in KEYWORDS-MATERIALES §6, then a Productos /
  Profesionales bucket review
- Guías from intent clusters: colores de pintura (inspiration), tipos de ventanas / modelos
  de portones (gallery) — only once their categories are live

## §11 Improvement build — phases 9–15 (added 2026-09-11)

Source: `docs/IMPROVEMENT-REPORT.md`. Decisions §1.17–1.26 are locked. Two windows
(§1.16 pattern): Opus 9→10→11→12, then Sonnet 13→14→15. Each PR ≤ 90 min; a PR still
polishing at minute 60 stops polishing. Phase logs per §4.14.

### §11.1 Phase 9 — Home & conversion layer (Opus, `phase/9-home-conversion`)

Owns: `index.php`, `partials/header.php`, `partials/footer.php`, `partials/cta.php` (new),
`materiales/index.php`, `guias/index.php`, `gracias/index.php`, `assets/css/site.css`
(append `/* == 9 == */` block, may also edit existing rules), `assets/js/motion.js`,
`content/home/*.php` (new dir), `tools/render-check.sh` (add checks), `docs/log/9-*.md`.

Build:
- **Homepage** (CONTENT-SPEC §1 vocabulary; voseo; no prices; no brands): hero with H1
  owning `materiales de construcción` + `paraguay` and a facts strip using
  `.page-hero__facts` (counts computed from data: N materiales, N rubros, "hasta 3
  cotizaciones"); primary button → `/cotizar/`; "Cómo funciona" (3 steps: contás qué
  necesitás → hasta 3 proveedores verificados te escriben → elegís el mejor precio);
  rubros grid (existing); a 150–250-word prose block in `content/home/intro.php` that
  weaves KEYWORDS §4.3 head/local terms (Asunción, Luque, San Lorenzo, Lambaré, Capiatá,
  *corralón*, *venta de materiales de construcción*) naturally — no keyword lists, no city
  pages; "Guías" teaser (first 3 `activa` guías from data); the quote form (`partials/form.php`,
  no preselection) at the end; existing schema unchanged.
- **Hero CTA on money pages**: category, material and guía heroes get a `btn btn--primary`
  anchored to `#cotizar` (form is already on the page) — guías link `/cotizar/`.
- **`partials/cta.php`**: sticky bottom bar on `max-width: 39.99rem` only, hidden while
  the form is in view (IntersectionObserver in `motion.js`; no JS ⇒ bar always visible),
  "Pedir cotización" (anchor/link) first and "WhatsApp" second only when `site('whatsapp')`
  is set (§1.18). Included by `footer.php` on every page except `/cotizar/`, `/gracias/`.
- **`/gracias/`**: "Qué pasa ahora" 3-line list, secondary WhatsApp (when set), 3 guía tiles.
- `tools/render-check.sh`: `/` contains `Cómo funciona` and `name="consentimiento"`;
  `/materiales/hierro/` contains `href="#cotizar"`.

Exit: smoke + render-check + CI green; one screenshot pass (home, category, material ×
390/1280) via Playwright with a real viewport (never `chromium --screenshot`, KNOWN-ISSUES
#23); `scrollWidth === clientWidth` at 360/390; PR merged; log + §9 line.

### §11.2 Phase 10 — Supplier recruitment (Opus, `phase/10-proveedores`)

Owns: `proveedores/index.php` (new), `partials/form-proveedor.php` (new), `partials/lead.php`
(additive only), `cotizar/enviar.php` (additive only), `config.sample.php`, `data/site.php`
(new keys `consent_version_proveedor`, `supplier_pitch`, `supplier_categories_note`),
`politica-de-privacidad/index.php` (one new clause), `partials/header.php` + `footer.php`
(one nav link "Para proveedores"), `.htaccess` + `tools/router-cli.php` (only if the generic
`/{dir}/` rule does not already serve it — it does in router-cli; verify Apache serves
`proveedores/index.php` via `DirectoryIndex`, no rewrite needed), `tools/smoke.php`,
`tools/render-check.sh`, `sitemap.php` (add `/proveedores/`), `docs/log/10-*.md`.

Build (§1.17):
- Page `/proveedores/`: H1 `Recibí pedidos de cotización de tu rubro`, value prop (leads
  reales, con material, cantidad y zona; hasta 3 proveedores por pedido; pagás por lead,
  no por publicidad), "Cómo funciona para proveedores" (3 steps), category list (11
  activa, with the 5 launch categories marked "buscamos proveedores ahora"), FAQ (4 items,
  visible, `FAQPage`), the supplier form. Terms text from `site('supplier_pitch')`; empty ⇒
  "Te contamos las condiciones por WhatsApp". Title ≤ 60, meta ≤ 155, `BreadcrumbList`.
- `partials/form-proveedor.php`: empresa (required), rubros (checkboxes of activa
  categories, ≥ 1 required), ciudad, nombre de contacto, WhatsApp (required, PY
  validation), mensaje, consent checkbox with its OWN text: "Acepto que Materiales.com.py
  guarde mis datos para contactarme sobre pedidos de cotización de mi rubro. Ver la
  Política de privacidad." Honeypot + signed stamp reused. Hidden `tipo=proveedor`.
- Handler: when `tipo === 'proveedor'` → validate as above, payload `fields.tipo =
  'proveedor'`, `fields.empresa`, `fields.rubros` (comma-joined slugs), `fields.consent =
  consent_version_proveedor @ ts`, `message` = mensaje, no `material/categoria`; success →
  `/proveedores/?ok=1#gracias` (PRG, same idempotency rule). Buyer path byte-identical to
  today: smoke asserts the buyer payload has NO `tipo` key.
- Privacy policy: one clause "Datos de proveedores" (finalidad, base: consentimiento,
  encargado VenderCRM, plazo).
- smoke: units for the supplier payload shape + the consent text guard for
  `form-proveedor.php` (same pattern as the buyer guard). render-check: `/proveedores/`
  200 with `name="tipo"`, a supplier POST happy path → 303 to `/proveedores/?ok=1`, log line
  carries `tipo`.

Exit: all checks green; buyer path unchanged (render-check's existing lead block passes
untouched); PR merged; log + §9 line; STATUS "Para proveedores" row.

### §11.3 Phase 11 — Cross-link engine & image slots (Opus, `phase/11-crosslinks-images`)

Owns: `partials/init.php` (new helpers only), `partials/related.php` (new),
`partials/hero-image.php` (new), `partials/header.php` (og:image), `partials/schema.php`
(`Product.image`), `materiales/index.php`, `guias/index.php`, `index.php` (hero image
slot), `assets/css/site.css` (`/* == 11 == */`), `tools/smoke.php`, `data/*.php` (ONLY
adding the documented optional `image` key comment — no values), `docs/log/11-*.md`.

Build (§1.19, §1.20):
- `init.php`: `guides_for(string $slug, string $categorySlug): array` (guías whose
  `related[]` contains the slug or the category, `activa` only, ordered by `order`);
  `calculators_for(...)` same shape reading `data/calculators.php` IF the file exists
  (phase 12 creates it — guard with `is_file`, return `[]`); `image_for(array $entry, string
  $type): ?string` implementing the inheritance in §1.20 and returning null when the file
  is missing on disk.
- `partials/related.php`: renders "Guías relacionadas" and "Calculadoras relacionadas"
  tile lists (reuse `.tile-grid`), included on category and material pages between FAQ
  and the form; on guías, add "Calculadoras relacionadas" next to the existing related
  block. Empty ⇒ prints nothing.
- Hero image: `partials/hero-image.php` renders `<picture>` (JPG, `loading="eager"` on the
  hero, `width/height` set, es-PY `alt` = `"{name} — materiales de construcción en
  Paraguay"`) inside `.page-hero` when `image_for()` is non-null; CSS: split hero grid
  (`.page-hero__grid--split` already exists) with the image on the right on ≥ 64rem,
  above the text below that. `og:image` per page = image or `og-default.jpg`.
  `Product.image` = absolute URL when present.
- Smoke: for every entry with an `image` value, the file must exist; value must match
  `^assets/img/[a-z0-9/_-]+\.(jpg|webp)$`.
- render-check: `/materiales/cemento/` contains `Guías relacionadas` (cemento is in
  `cuantas-bolsas-de-cemento-por-m2.related`).

Exit: checks green; zero visual change on pages without images (screenshot diff of one
material page before/after within tolerance); PR merged; log + §9 line.

### §11.4 Phase 12 — Calculators foundation (Opus, `phase/12-calculadoras-foundation`)

Owns: `calculadoras/index.php` (new router: index + `/calculadoras/{slug}/`),
`data/calculators.php` (new), `content/calculadoras/{slug}.php` (new dir, one exemplar),
`assets/js/calc.js` (new), `assets/css/site.css` (`/* == 12 == */`), `.htaccess` +
`tools/router-cli.php` (both — mirror rule), `sitemap.php`, `partials/header.php` +
`footer.php` (nav link "Calculadoras"), `CONTENT-SPEC.md` §12 (new), `tools/smoke.php`,
`tools/render-check.sh`, `docs/log/12-*.md`.

Build (§1.21):
- `data/calculators.php` contract (English keys): slug ⇒ `{ name, status, order, title,
  meta, keyword, intro, inputs: [{id, label, unit, min, max, step, default}], outputs:
  [{id, label, unit}], formula_note (one sentence, visible), assumptions: [..], related:
  [material|category slugs], faq: [3–5, last = '¿Cuánto cuesta …?'], cta_material: slug,
  cta_quantity_template: 'string with {output_id}' }`. Formulas live in the content file
  as a `<script type="application/json" data-calc>` block consumed by `calc.js`, so one
  JS file serves every calculator; the prose in the same file explains the formula in
  words with a worked example (the no-JS fallback and the SEO content).
- Router mirrors `guias/index.php`; `BreadcrumbList` + `FAQPage` (+ `HowTo` NOT emitted —
  it no longer earns rich results); index page `ItemList`.
- `calc.js`: reads the JSON, evaluates the declared expression tree (no `eval`), updates
  outputs live, writes `cantidad` into the on-page form and preselects `cta_material`;
  `prefers-reduced-motion` respected; < 4 KB.
- Exemplar `bolsas-de-cemento-por-m2`: inputs m², espesor, tipo (contrapiso/revoque/
  carpeta); dosages from CONTENT-SPEC §12 (write §12 first: standard 1:3 / 1:4 mortar
  and 1:2:3 concrete dosages, rendimientos per 50 kg bag, with the textbook source line;
  no PY-specific invented numbers; every result "es una referencia — confirmá con tu
  proveedor"); ends in the form with `cemento` preselected and "N bolsas de 50 kg" as
  `cantidad`. Link the existing guía `cuantas-bolsas-de-cemento-por-m2` both ways
  (`related[]`).
- Smoke: calculators shape, slug uniqueness within the file, `related` targets exist,
  content file exists for `activa`, JSON block parses, last FAQ is the price FAQ.
  render-check: `/calculadoras/` 200 `ItemList`; `/calculadoras/bolsas-de-cemento-por-m2/`
  200 contains `data-calc` and `name="consentimiento"`; `/calculadoras/no-existe/` 404.

Exit: checks green; exemplar verified in Playwright (change an input → output updates →
form `cantidad` filled); PR merged; log + §9 line; STATUS updated; **window STOPS** and
reports the Sonnet line to paste.

### §11.5 Phase 13 — Calculators + guías wave 3 (Sonnet, `phase/13-calculadoras-guias`)

Owns: `data/calculators.php` (add entries), `content/calculadoras/*.php` (new files),
`data/guides.php` (add entries), `content/guias/*.php` (new files), `docs/log/13-*.md`.
HARD LIMITS: no router, template, JS, CSS, handler, smoke, or existing-entry changes; a
needed template fix goes to `docs/decisions-needed.md` and the phase works around it.

Build:
- Three calculators on the phase-12 shape, each with its `content/calculadoras/{slug}.php`
  (formula JSON + 350–500 words prose + worked example): `hormigon-por-m3` (cemento,
  arena, ripio, agua per m³ for a 1:2:3 dosage; cta `hormigon-elaborado` alt `cemento`),
  `ladrillos-por-m2` (común / hueco 8 / hueco 12 / bloque, with junta; cta
  `ladrillo-comun`), `revoque-y-mortero` (cal + cemento + arena per m² by espesor; cta
  `cal-hidratada`). Formulas only from CONTENT-SPEC §12; if §12 lacks a dosage, use the
  standard textbook one and ADD it to §12 in the same PR with its source line — never
  invent a PY-specific figure.
- Six guías (600–900 words, CONTENT-SPEC §6 shape, voseo, no prices, no brands outside
  §11.2), fan-out to Sonnet subagents after ONE exemplar: `de-que-depende-el-costo-de-
  construir-en-paraguay` (owns *cuánto cuesta construir una casa en paraguay* — factors
  only, never a figure), `como-hacer-un-computo-metrico` (*cómputo métrico*, *presupuesto
  de obra*), `como-elegir-un-corralon` (*corralón*, *venta de materiales*), `chapa-o-teja-
  que-techo-conviene`, `cuanto-hierro-lleva-una-columna` (links the hierro calculator
  idea → Backlog, and `varilla-de-hierro`), `como-impermeabilizar-una-losa`. Each links
  ≥ 3 money pages + ≥ 1 calculator with descriptive anchors; `related[]` set so §1.19
  surfaces them on the money pages.
- Titles ≤ 60, metas ≤ 155, every page `activa` in the same PR.

Exit: smoke + render-check + CI green; word counts by script; PR merged; log + §9 line.

### §11.6 Phase 14 — Technical hardening (Sonnet, `phase/14-tech-hardening`)

Owns: `.htaccess` (headers/deflate/expires blocks only — NEVER the rewrite block),
`assets/fonts/**` (new), `assets/css/site.css` (`@font-face` block replacing the Google
Fonts `<link>`), `partials/header.php` (font link removal + `og:type`), `sitemap.php`,
`404.php` (canonical removal), `tools/replay-leads.php` (new), `DEPLOY.md` (cron + headers
verification), `tests/mobile-overflow.mjs` (new), `.gitignore`, `docs/log/14-*.md`.
HARD LIMITS: no rewrite rules, no router, no handler contract, no data keys.

Build (§1.24–1.26):
- `.htaccess`: `mod_deflate` for html/css/js/xml/svg/json; `Cache-Control` `no-cache` for
  PHP responses (add in `header.php`, not htaccess — PHP output), 7d css/js, 30d images
  (existing); headers `X-Frame-Options: SAMEORIGIN`, `Permissions-Policy:
  camera=(), microphone=(), geolocation=()`, `Strict-Transport-Security` **commented with
  the one-line instruction to enable after SSL is confirmed** (never enable blind on
  shared hosting). No CSP (inline JSON-LD + GA4/Pixel injection make a strict CSP a
  project of its own → Backlog).
- Fonts: download Bricolage Grotesque (500, 600, opsz 12..96 variable if available) and
  Inter (400/500/600) `.woff2` (latin + latin-ext), ≤ 200 KB total; `@font-face` with
  `font-display: swap`; `<link rel="preload">` for the two display files; remove the
  Google Fonts `<link>`s and `preconnect`s. If the download is blocked by the sandbox,
  write the exact file list + URLs to `docs/decisions-needed.md` and skip — do not leave
  the site font-less.
- `sitemap.php`: `lastmod` from `updated` key only (§1.25); document the key in the three
  data files' header comments (comment-only edits are allowed here).
- `404.php`: no canonical; guías + calculators `og:type=article`.
- `tools/replay-leads.php` (§1.26): CLI, reads `storage/leads.log`, replays lines with
  `outcome` = crm failure and no entry in `storage/replayed.log`, uses `lead_send()` with
  the logged payload verbatim, appends result; `--dry-run`; exit codes; smoke unit with a
  fixture log in a temp dir (no network: mock by asserting the selection logic only).
  DEPLOY.md: hPanel cron line, hourly.
- `tests/mobile-overflow.mjs`: Playwright (`executablePath` from
  `PLAYWRIGHT_BROWSERS_PATH`/`/opt/pw-browsers/chromium` fallback), visits `/`,
  `/materiales/hierro/`, `/materiales/cemento/`, `/guias/`, `/calculadoras/bolsas-de-
  cemento-por-m2/`, `/proveedores/` at 320/360/390/1280, asserts `scrollWidth ===
  clientWidth`, saves screenshots to a git-ignored `docs/screenshots/`. Not in CI.

Exit: checks green; `tests/mobile-overflow.mjs` green locally; Lighthouse ONE run on
`/materiales/cemento/` mobile reported in the log (no target number — record only);
PR merged; log + §9 line.

### §11.7 Phase 15 — Link pass + images + launch QA (Sonnet, `phase/15-link-pass-launch`)

Owns: `content/**` (link edits only, no rewrites), `data/categories.php` + `data/materials.php`
+ `data/guides.php` (`image` values + `related[]` additions only), `assets/img/**`,
`KNOWN-ISSUES.md`, `STATUS.md`, `README.md`, `docs/log/15-*.md`, `docs/decisions-needed.md`.
HARD LIMITS: same as 13 + 14 combined.

Build:
- Editorial cross-links: every material prose gets ≥ 1 in-prose link to a related guía
  or calculator (descriptive anchor, inside an existing sentence or the closing
  paragraph — never a "see also" list; the tiles already do that); every category prose
  links its guías; guías link the calculators. Script-verify: 0 material pages without a
  `/guias/` or `/calculadoras/` link.
- Images: if `assets/img/cat/*.jpg` / `hero-home.jpg` exist on `main` (Anton's §7 step),
  set `image` on the 11 categories + home (home reads `site('hero_image')` — add the key
  read in `index.php` only if phase 11 did not), verify ≤ 150 KB each, alt text per
  §11.3; if they do not exist and `*.cloudfront.net` is reachable, run
  `higgsfield-image-pipeline` for the 12 prompts in `docs/imagery-brief.md` (one attempt;
  403 ⇒ log and move on). No images ⇒ ship without; not a failure.
- QA: re-run the phase-8 checklist on every NEW page type (home, proveedores,
  calculators, new guías): one `<h1>`, canonical, JSON-LD valid, titles/metas, no prices,
  no brands outside §11.2, voseo; `tests/mobile-overflow.mjs` green.
- Housekeeping: promote still-open items from `docs/log/9..15` to `KNOWN-ISSUES.md`; STATUS
  phase table 9–15; README "Calculadoras" + "Proveedores" lines; `docs/log/README.md`
  index.

Exit: checks green; PR merged; log + §9 line; **closing report** to Anton: what shipped,
open KNOWN-ISSUES, the go-live checklist status (which §7 items are still empty), and the
suggested next Fable planning input (second keyword pull).
