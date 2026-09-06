# Phase 8 — Imagery + QA + launch. SONNET window (PR 3 of 3, FINAL). Start ONLY after PR 7 is merged.

Read ONLY: this file, `plan.md` §1, §4, §6, §7, the phase table and §9 index, `DEPLOY.md`,
`KNOWN-ISSUES.md` in full, and the §9 lines of 6 and 7. Autonomy protocol §4 applies. Never Fable.

HARD LIMITS (plan §4.7): no router, data-key, handler, taxonomy, consent or design-system
changes. Fixes found by QA that need any of those → KNOWN-ISSUES.md + closing report, not
a commit.

Owns: `public_html/assets/img/**`, `content/**` (QA fixes only), `data/site.php` values
that QA proves wrong (never NAP — that is Anton's), `KNOWN-ISSUES.md`, `STATUS.md`,
`plan.md` §9 line, `README.md` launch section if it exists.

Budget: ≤ 90 min. Branch `phase/8-imagery-qa-launch` off latest main.

Phase rules:
- Imagery: load `higgsfield-image-pipeline` FIRST (it overrides the model/network sections
  of `higgsfield-web-imagery` and `webimg-pipeline`), then those two. OG image 1200×630 per
  money page (activa categories + materials + guías) and the hero/image slots phase 4 left
  empty. WebP, es-PY alt text, descriptive filenames via the webimg CLI. No faces, no fake
  "our work" photos. If the CDN download is blocked (known 403), ship the palette-motif
  fallback the design system provides, list the blocked files in the closing report as a
  §7 manual step, and move on — do not retry more than once.
- QA: run the FULL `seo-web-builds` §6 pre-launch checklist and the `web-design-system` QA
  gate; fix every failure inside your Owns. Verify sitemap/robots/canonicals, OG tags,
  JSON-LD validity (FAQPage only where FAQs are visible), one `<h1>` per page, titles ≤ 60 /
  metas ≤ 155, no "estamos publicando" notice on any activa page, no brand outside
  CONTENT-SPEC §11, no price anywhere.
- Form round-trip per `vendercrm-lead-capture` "Verify" (if CRM config is on the server;
  else assert the leads.log path and say so).
- Leave `staging_noindex => true` UNLESS the §9 log says the domain is live.
- ONE screenshot pass (≤ 5 pages × 2 widths) after the last change; CI artifact, not git.

Exit: checklist passes; every activa page has prose + OG; render-check + smoke + CI green;
PR merged; §9 line; STATUS shows phases 5–8 merged.

## After this PR — STOP. Do not spawn anything.
Post the closing report to Anton: what is live, the KNOWN-ISSUES summary, and the exact
numbered manual steps — (1) point materiales.com.py DNS at the Hostinger slot, (2) upload
`config/vendercrm.php` with the real CRM URL + key, (3) set GA4/Pixel IDs and NAP in
`data/site.php`, (4) set `staging_noindex => false`, (5) submit the sitemap in Search
Console, (6) run the KEYWORDS-MATERIALES §6 check list in Keyword Planner, (7) start
founding-supplier outreach per plan §8.4.
