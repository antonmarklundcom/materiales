# Prompt to continue the improvement build (paste into a new Claude Code session)

Works in a **cloud session on the repo `antonmarklundcom/materiales`** (recommended — nothing
to install) or **locally in a clone** (needs PHP 8.x + curl; Playwright/Chromium optional for
visual checks). Model: Opus 5.5, effort **high** for PR D-E-F-G, medium is fine for PR B-C.

---

```
Repo: antonmarklundcom/materiales (materiales.com.py — PHP on Hostinger, repo root = docroot,
deploy = Hostinger webhook on push to main; CI = one ~15 s job: php -l + tools/smoke.php +
tools/render-check.sh).

Continue the improvement build defined in docs/IMPROVEMENT-REPORT-2.md. Item IDs (B1, S3, C2,
G1…) refer to that report. PR A (Tier 0: B1–B8 + R6) is DONE and merged — see git log.

Work through the remaining batches IN ORDER, one PR per batch. For each batch:
  1. branch from the latest main (or reuse your designated branch, reset onto main after each
     merge), implement, and keep the repo's conventions (Spanish/voseo comments & copy, the
     anti-fabrication rules in CONTENT-SPEC.md — never invent prices, NAP, brands, reviews
     or supplier facts; if a fact is unknown, leave it out and list it for Anton).
  2. Before pushing, run: find . -name '*.php' -not -path './.git/*' -print0 | xargs -0 -n1 php -l
     && php tools/smoke.php && bash tools/render-check.sh — extend smoke/render-check with a
     check for every behaviour you add. Any change to a rewrite rule goes in BOTH .htaccess
     and tools/router-cli.php.
  3. Open the PR (squash-merge convention, title ends up as "… (#NN)"), wait for CI green,
     merge it yourself, then start the next batch. If CI fails, fix and re-push.

Batches still to do:
  PR B — Ops/reliability: R1 (replay-leads.php: retry cap for permanent 4xx, non-zero-exit
         alert via lead_notify, docs for the cron + the "--max-age-hours=0 --dry-run" backlog
         run when the CRM goes live), a tools/lead-digest.php daily summary (counts by outcome
         incl. retenido) for cron, R3 (CI step that diffs the [F] folder lists of .htaccess vs
         tools/router-cli.php + tools/prod-check.sh that curls the expected 403s), R4 (FilesMatch
         deny for *.bak *.orig *~ *.sh *.log *.sql at any path), R5 (leads.log monthly rotation +
         retention + storage/throttle cleanup, documented in DEPLOY.md and the privacy policy),
         R8 (refresh STATUS.md numbers: 70 materials, 12 active categories; archive plan.md and
         prompts/ only if nothing references them). Update DEPLOY.md with the new
         notify_email / telegram keys that PR A added to config.sample.php.
  PR C — Technical SEO: S1 (https + non-www 301s at the top of .htaccess; router-cli can
         ignore them; then HSTS), S2 (favicon.svg + 48px PNG + favicon.ico + apple-touch-icon,
         from the brand palette — safety yellow + black, see assets/css/site.css tokens), S3
         (remove the Product JSON-LD block), S4 (/materiales/ gets its own title/H1 + 150–250
         word intro), S5 (hero <img>: sizes + fetchpriority="high"), S6 (metric-matched
         fallback @font-face + inline html.js-nav class in <head>), S7 (drop Disallow /gracias/),
         S8 (ExpiresByType avif/svg, ?v=filemtime on CSS/JS, 1-year expiry), S9 (proxima
         categories rendered as non-links), S10 (LocalBusiness → Organization + WebSite with
         publisher). Measure CLS before/after with Playwright if available.
  PR D — Conversion: C1 code side (Consent Mode v2 default-denied stub before gtag, updated on
         the consent change event; events already renamed in PR A: cta_click, form_start,
         form_submit_attempt, whatsapp_click — add calculator_use), C2 (button "Cotizá estas N
         …" under .calc__outputs + add page-calculadora to $ctaHasForm in partials/cta.php), C4
         (compact mobile cookie strip), C5 (client-side phone + consent validation, red error
         style, scroll-margin-top on #cotizar), C6 (wa.me links with ?text= per material), C7
         (/proveedores/ sticky bar → "Sumate como proveedor", unlink rubro tiles), C8 (/gracias/
         next steps with related materials prefilled), C9 (guide CTAs with ?m=), C10 (required
         markers). Do NOT change the consent text or form field names (smoke enforces it).
  PR E — On-page targeting (Anton approved unlocking the title lock): S11 titles/H1/intros
         lead with the measured keyword (tierra colorada, canto rodado, ladrillo visto, puertas
         de metal, membrana para techo, tanque 1000 litros — volumes in KEYWORDS-MATERIALES.md),
         S12 replace every "Precio por …" title that shows no price with "Cotizá por …", S13
         richer material H1s + "Paraguay" in guide/calculator titles, S14 give guides and
         calculators distinct H1s/angles, S15 in-prose links to orphaned money pages, G8 hub
         intros. Keep titles ≤ 60 / metas ≤ 155 (smoke enforces). Update CONTENT-SPEC.md.
  PR F — S16 freshness & E-E-A-T: `published`/`updated` on every data entry (take the dates
         from git log of each content file, never invent), visible "Actualizado", lastmod in
         the sitemap, Article JSON-LD on guides/calculators with the organization as author,
         /nosotros/ and /como-trabajamos/ pages written only from facts already in the repo.
         Then C3: a 2-field mini form in the hero of material/category pages that expands into
         the full form (same handler, same consent text).
  PR G+ — Content growth, one PR per 2–3 items, parallel subagents (Opus, NOT Fable) for the
         writing, you review: G1 calculators (cerámica/porcelanato por m², chapas para techo,
         hierro kg/barra + estribos, durlock por m², membrana rollos por m², tanque litros por
         personas — follow CONTENT-SPEC §12 and calc.js's JSON expression tree), G2 promote the
         Pinturas category (≥ 3 active materials, §11.1 keyword rows), G3 missing pages (ducha
         higiénica, piso parquet, metal desplegado…), G4 comparison guides, G6 glossary, A1
         "proveedor verificado" badge page with an embeddable snippet, G5 depth pass (tables +
         worked examples) category by category. Fix "mirá la hormigón elaborado" in
         content/materiales/cemento.php.

Out of scope (needs Anton, list them in the final report): D1 price index data, D4 NAP data,
D5 server config (config/vendercrm.php incl. notify_email, GA4 ID, Search Console, crons),
G7 Keyword Planner pull, A2–A4 off-site work.

When every PR is merged, reply with a short report: PRs (links) and what each changed, what
was verified, and the remaining human to-do list.
```
