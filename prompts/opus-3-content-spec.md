# Phase 3 — Content spec. Paste into a fresh OPUS session, ONLY after phase 2 is merged.

Read `plan.md` FIRST, in full — plus §9 build log and `KNOWN-ISSUES.md`.
This phase moves every copy and keyword DECISION into the repo so Sonnet phases execute
without deciding anything. Autonomy protocol §4 applies.

Phase rules:
- Branch `phase/3-content-spec` off latest main. Phase 2 unmerged ⇒ finish it first.
- Load `seo-web-builds` (+ its `references/locales.md`, `references/schema-templates.md`)
  and `paraguay-local-site` (voseo/vocabulary rules, §10 vertical patterns). Missing skill ⇒
  nearest equivalent, note in build log, keep going.
- Fully populate `data/categories.php` and `data/materials.php` for ALL categories in plan
  §5 (recruited + próximas): per entry — title ≤60, meta ≤155 (ad-copy voseo with a
  conversion verb), primary keyword, `synonyms[]` (authentic PY vocabulary — piedra bruta,
  tejuelón, 4ta/5ta/6ta, cal viva…), sale_unit, 3–5 FAQ Q&As WITH answers, related[].
- Write `CONTENT-SPEC.md`: locked verbatim copy for homepage (H1, hero, sections, CTAs),
  category-page template copy, material-page template copy, gracias page, form microcopy +
  the exact consent sentence from plan §8.6; per-guía outlines for the 4–6 wave-1 guías
  (title, H2s, money-page links with anchor text).
- One page = one primary intent. Synonyms NEVER become pages. No invented stats, reviews,
  years, or supplier counts — anti-fabrication rules are absolute.
- Data files must pass the phase-1 smoke (valid PHP, unique slugs).

Exit: CI green; every activa+próxima category and every wave-1/wave-2 material has complete
metadata + FAQs; CONTENT-SPEC.md leaves zero copy decisions open (test: if a later phase
must decide a wording, this phase failed — fix the line); PR merged.

## After this phase — hand off (MODEL SWITCH → Sonnet)
Same four gates as plan §4.9, merge verified via `mcp__github__*` tools, build-log entry
committed. Spawn a NEW session via `create_session`, model **Sonnet** (never Fable), prompt:
`Read prompts/sonnet-4-design-pages.md in this repo and execute it.`
If `create_session` is unavailable, STOP and report: the next phase needs a Sonnet window —
tell Anton to paste that line into a fresh Sonnet session.
