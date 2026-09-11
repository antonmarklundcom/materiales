# Improvement report — materiales.com.py (Fable review, 2026-09-11)

Read once, then follow `plan.md` §11 and the two prompt files it names. Every decision below
is locked in `plan.md` §1 (items 17–26); build sessions never reopen them.

## 1. Where the repo stands

Verified on `main` at `ee8e2a0` in this session: `php -l` clean on every file,
`tools/smoke.php` green (13 categories / 11 active, 64 materials / 64 active, 8 guías, 77
unique slugs), `tools/render-check.sh` green including all lead-handler POST paths. No open
PRs, no open issues. The content is real: material prose 343–490 words (median 389), guías
584–724, 14 pages carry measures tables, every material closes with the price FAQ.

What is finished and must not be rebuilt: router, data contract, lead pipeline + consent
proof, design system, taxonomy, keyword ownership (CONTENT-SPEC §11), all prose.

**The site can go live today.** Nothing in this report blocks launch; the launch blockers
are the human inputs in §5 below (NAP, CRM config, DNS, `staging_noindex`). Ship those in
parallel with the build — do not hold DNS for phase 9.

## 2. Findings, ranked by business impact

| # | Finding | Evidence | Impact |
|---|---|---|---|
| 1 | **No supplier-side page.** The business sells leads to suppliers, but the site has no way for a supplier to find out, sign up, or be contacted. Recruitment (plan §8.4) is the cold-start bottleneck and today runs entirely off-site. | No `/proveedores/` route; `fields.tipo` does not exist in the payload. | Highest. Every quote request from an unrecruited category is wasted until a supplier exists. |
| 2 | **Homepage is a stub.** Hero + 13 tiles + one link. No "cómo funciona", no expectation-setting, no form, no guías, no head/local terms (KEYWORDS §4.3: *materiales de construcción asunción / luque / corralón*). `.page-hero__facts` exists in CSS and is unused. | `index.php` is 50 lines. | High. It is the page that will rank for the head term and the one paid traffic lands on. |
| 3 | **CTA is buried on money pages.** Category/material heroes have no button; the form sits below prose + FAQ + related tiles. On mobile that is 3–5 screens of scrolling. No sticky mobile CTA, no WhatsApp secondary CTA anywhere except the footer (and only once NAP is set). | `materiales/index.php`, `guias/index.php`. | High. Direct conversion-rate lever. |
| 4 | **Internal linking is one-directional.** Guías link to 3 materials each; **0 of 64** material pages and **0 of 11** category pages link to any guía. `related[]` on materials only points at materials. The informational tail feeds money pages, but nothing feeds the tail. | grep counts in this session. | High for SEO. Guías are the cheapest pages to rank and today are near-orphans (only `/guias/` index links them). |
| 5 | **Zero images.** No hero, no per-page `og:image` (one palette fallback sitewide), no `Product.image`. WhatsApp previews are the ad in PY (plan §6). Blocked by the sandbox's 403 on `*.cloudfront.net` (KNOWN-ISSUES #22), and the templates have no image slot to receive files even when they exist. | `grep -l '<img'` → 0 files; no `image` key in data. | Medium-high. Two halves: template slot (build work) and files (human step). |
| 6 | **No interactive tools.** The plan's backlog already lists a materials calculator; the keyword research shows the modifiers that convert are quantities (*cuántas bolsas*, *m³*, *por millar*). Static guías answer these; a calculator that ends in a pre-filled quote is a stronger page and a link magnet. | Backlog §10. | Medium-high. Differentiator in a market of price-list PDFs. |
| 7 | **Technical SEO/perf gaps.** No `mod_deflate`, no `Cache-Control` for HTML, no HSTS / `X-Frame-Options` / `Permissions-Policy`; fonts from Google CDN (third-party request on every page, Ley 6534 exposure); `sitemap.xml` `lastmod` uses `filemtime`, which on Hostinger's git deploy resets to deploy time for every file (so every page claims to change on every push); 404 page emits a canonical; guías use `og:type=website`. | `.htaccess`, `sitemap.php`, `404.php`. | Medium. Each is small; together they are one Sonnet PR. |
| 8 | **Failed CRM posts are logged but never replayed.** Backlog item; `leads.log` carries the full payload including `idempotency_key`, so a replay is safe by construction. | `partials/lead.php`. | Medium. Only matters on the first CRM outage, which is exactly when a manual fix is slowest. |
| 9 | **Thin guías set.** 8 guías vs. the intent clusters in KEYWORDS §4.3 (*cuánto cuesta construir una casa en paraguay*, *cómputo métrico*, *presupuesto de obra*, *corralón*). | `data/guides.php`. | Medium. Six more guías is one fan-out. |
| 10 | **Repo memory is spread over four files.** `plan.md` (54 KB, logs inline), `STATUS.md`, `KNOWN-ISSUES.md`, `docs/`. Sessions read all of it. | — | Low, but it costs every future session context. The method now uses `docs/log/<phase>.md`. |

## 3. What I decided NOT to do (and why)

- **City pages `/zonas/{ciudad}/`** — no localized content exists; they would be doorways.
  Local terms go on the homepage and in the form's `ciudad` field instead. Backlog.
- **Sanitarios / pinturas / herrajes / electricidad** — stays per plan §1.14.
- **OTP / WhatsApp-ping validation, Turnstile** — no evidence of spam yet; honeypot + stamp
  + manual QA stand (plan §8.1).
- **`AggregateOffer` / any price** — plan §1.5 stands.
- **A CI screenshot job** — runner minutes are budgeted and the one screenshot bug so far was
  the tool, not the site. A local Playwright overflow check under `tests/` instead.
- **Node build step, framework, DB** — plan §1.1–1.2 stand. Calculators are plain PHP + one
  vanilla JS file.
- **Automated supplier fan-out** — still a VenderCRM automation, not site PHP (plan §3).
- **Rewriting existing prose** — it is good. Cross-links are added by a link pass, not by
  rewrites.

## 4. The plan — two windows, Opus first, then Sonnet

Model rule (plan §4 and the phased-autonomous-build method): Opus builds anything other
phases build on — shared partials/templates, routes, handler contract, math, data-key
contracts. Sonnet fills existing shapes with content and does bounded hardening. Fable is
never a build model. All Opus work ships first so every Sonnet PR starts from a finished
foundation.

| Phase | Model | PR | What | Fixes finding |
|---|---|---|---|---|
| 9 Home & conversion layer | Opus | `phase/9-home-conversion` | Homepage rebuild (facts strip, cómo funciona, rubros, guías teaser, form, head/local terms); hero CTA on category/material/guía pages; sticky mobile CTA bar with WhatsApp secondary; `/gracias/` next steps. | 2, 3 |
| 10 Supplier recruitment | Opus | `phase/10-proveedores` | `/proveedores/` landing + `partials/form-proveedor.php` + handler `tipo=proveedor` + `proveedor-v1` consent + privacy-policy clause + nav/footer link + smoke/render units. | 1 |
| 11 Cross-link engine & image slots | Opus | `phase/11-crosslinks-images` | Data-driven "Guías relacionadas" on category/material pages (inverse of `guides[].related`), "Calculadoras relacionadas" hook; optional `image` key on categories/materials/guides with hero `<picture>`, per-page `og:image`, `Product.image`, smoke validation. | 4, 5 (template half) |
| 12 Calculators foundation | Opus | `phase/12-calculadoras-foundation` | `/calculadoras/` route (+ `.htaccess`, `router-cli.php`, sitemap), `data/calculators.php` contract, template, `assets/js/calc.js`, CONTENT-SPEC §12 (formulas + wording rules), one exemplar (`bolsas-de-cemento-por-m2`) ending in a pre-filled quote. | 6 |
| 13 Calculators + guías wave 3 | Sonnet | `phase/13-calculadoras-guias` | 3 more calculators on the phase-12 template; 6 new guías (KEYWORDS §4.3 clusters) via subagent fan-out. | 6, 9 |
| 14 Technical hardening | Sonnet | `phase/14-tech-hardening` | `.htaccess` deflate/cache/security headers; self-hosted fonts; sitemap `lastmod` fix; 404 canonical; `og:type`; `tools/replay-leads.php` + DEPLOY cron note; `tests/mobile-overflow.mjs`. | 7, 8 |
| 15 Link pass + images + launch QA | Sonnet | `phase/15-link-pass-launch` | Editorial cross-links in prose (material→guía/calculadora, category→guías); place image files if present; KNOWN-ISSUES promotion; `docs/log/` index; STATUS; closing report. | 4 (prose half), 5 (files half), 10 |

Estimated cost: Opus window ≈ $50–60 (4 PRs), Sonnet window ≈ $30–35 (3 PRs).
Wall-clock ≈ 2.5 h + 2 h. Anything over that is a process failure to log.

## 5. Human inputs (Anton) — none of these wait for the build

| When | What | Where |
|---|---|---|
| Now (go-live) | Real NAP: razón social, RUC, IVA, dirección, horarios, email, teléfono, WhatsApp | `data/site.php` |
| Now (go-live) | `config/vendercrm.php` on the server (URL + API key); GA4 + Pixel IDs | server / `data/site.php` |
| Now (go-live) | DNS → Hostinger, SSL, then `staging_noindex => false`, submit sitemap in Search Console | registrar / `data/site.php` |
| Before phase 15 | **Images**: generate the 12 images in `docs/imagery-brief.md` from your PC (or allow `*.cloudfront.net` in the Claude Code environment and let phase 15 run `higgsfield-image-pipeline`). Drop them in `assets/img/cat/{category-slug}.jpg` + `assets/img/hero-home.jpg` (1200×630 JPG ≤ 150 KB) and commit to `main`. Phase 15 wires them; if absent it ships without and logs it. | repo |
| Phase 10 | Supplier deal terms to state on `/proveedores/` (free first leads → prepaid packs). If unknown, the page says "te contamos las condiciones por WhatsApp" — no numbers invented. | `data/site.php` `supplier_pitch` (phase 10 documents the keys) |
| After phase 13 | Second Keyword Planner pull (KEYWORDS §6, 100 phrases) → drop the CSV in `docs/keywords/` for a future Fable planning session. | repo |
| Parallel, human | Founding-supplier outreach (plan §8.4) — now with `/proveedores/` as the link to send. | WhatsApp |

## 6. Where Fable comes back in

Only in a conversation Anton opens himself, at most twice: (a) optionally after PR 12
merges, one read of the merged foundation to adjust `prompts/sonnet-13-content-window.md`;
(b) after phase 15, the post-build review and the next keyword-driven plan (using the
second pull). Fable never watches a running window.
