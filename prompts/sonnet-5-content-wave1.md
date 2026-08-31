# Phase 5 — Content wave 1. Paste into a fresh SONNET session, ONLY after phase 4 is merged.

Read `plan.md` FIRST, in full — plus §9 build log, `KNOWN-ISSUES.md`, and `CONTENT-SPEC.md`.
Write the prose for the 5 recruited launch categories. Autonomy protocol §4 applies.

HARD LIMITS (plan §4.7): no changes to router, data-file schemas, lead handler, URL
taxonomy, consent text, or the design system from phase 4. Content includes only.

Phase rules:
- Branch `phase/5-content-wave1` off latest main. Phase 4 unmerged ⇒ finish it first.
- Load `seo-web-builds` and `paraguay-local-site` (voseo, anti-fabrication). Missing skill ⇒
  nearest equivalent, note in build log, keep going.
- Write `content/categorias/{slug}.php` for the 5 recruited categories and
  `content/materiales/{slug}.php` for every wave-1 material in plan §5, following each
  entry's CONTENT-SPEC outline: real uses in PY construction, sale units, how it's quoted,
  synonyms woven into copy and FAQ phrasing, internal links (category ↔ materials ↔ guías)
  with descriptive anchors. 150+ genuinely different words minimum per page — a page that
  reads templated with the noun swapped is a defect, not a deliverable.
- Write the 4–6 wave-1 guías from their CONTENT-SPEC outlines; every guía links to money
  pages. Quantities/technical claims must be standard construction knowledge stated
  conservatively — no invented prices, brands, stats, or supplier claims.
- Voseo everywhere; es-PY vocabulary; no English; no "tú" forms.

Exit: CI green; every wave-1 category/material/guía page renders full prose with FAQs and
internal links; sitemap lists them; no visible placeholders; PR merged.

## After this phase — hand off to the next (fresh session)
Same four gates as plan §4.9, merge verified via `mcp__github__*` tools, build-log entry
committed. Spawn a NEW session via `create_session`, model **Sonnet** (never Fable), prompt:
`Read prompts/sonnet-6-content-wave2-qa.md in this repo and execute it.`
Fallback without `create_session`: continue in this window. Never hand off unmerged.
