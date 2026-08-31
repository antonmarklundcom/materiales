# Phase 4 — Design & pages. Paste into a fresh SONNET session, ONLY after phase 3 is merged.

Read `plan.md` FIRST, in full — plus §9 build log, `KNOWN-ISSUES.md`, and `CONTENT-SPEC.md`.
Implement the visual layer for every page type. Autonomy protocol §4 applies.

HARD LIMITS (plan §4.7): do NOT change the router, data-file schemas, lead handler,
URL taxonomy, or consent text. Blocked by one of them ⇒ workaround + Backlog note.

Phase rules:
- Branch `phase/4-design-pages` off latest main. Phase 3 unmerged ⇒ finish it first.
- Load `web-design-system` (tokens, layout patterns, motion, QA gate) and
  `conversion-design` BEFORE styling anything; `seo-web-builds` §4–§5 for CTA/CWV rules.
  Missing skill ⇒ nearest equivalent, note in build log, keep going.
- Style homepage, `/materiales/` index, category template, material template, guía template,
  `/cotizar/`, `/gracias/`, `/contacto/`, privacy page, 404 — copy VERBATIM from
  CONTENT-SPEC.md and the data files. You write zero new copy.
- Form is the PRIMARY CTA sitewide ("Pedí cotización a proveedores verificados");
  WhatsApp secondary (green #25D366 only on WhatsApp elements, prefill names the material).
  Footer trust stack + cookie banner (Necesarias on; Estadísticas/Marketing default off,
  scripts gated on consent).
- Mobile-first 360px base, page weight ≤500 KB, one font pair, WebP, hero never lazy,
  clamp() type, zero horizontal scroll at 360/390/768/1024/1440.
- JSON-LD per plan §6 (service-area LocalBusiness, WebSite+SearchAction, ItemList,
  BreadcrumbList, FAQPage, minimal Product without offers). No aggregateRating, ever.

Exit: CI green; all page types render styled with real data; web-design-system QA gate +
seo-web-builds §6 checklist pass (staging noindex allowed to remain); PR merged.

## After this phase — hand off to the next (fresh session)
Same four gates as plan §4.9, merge verified via `mcp__github__*` tools, build-log entry
committed. Spawn a NEW session via `create_session`, model **Sonnet** (never Fable), prompt:
`Read prompts/sonnet-5-content-wave1.md in this repo and execute it.`
Fallback without `create_session`: continue in this window. Never hand off unmerged.
