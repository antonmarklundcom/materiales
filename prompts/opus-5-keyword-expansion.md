# Phase 5 — Keyword expansion. OPUS window. Ships THREE PRs in sequence (5a → 5b → 5c), then STOPS.

Read ONLY: this file, `plan.md` §1, §4, §5 (incl. §5.1), the phase table and §9 index,
`CONTENT-SPEC.md` in full, `KEYWORDS-MATERIALES.md` §1, §2, §3.4 and §5, and the §9 build-log
entry of phase 3. Do not read the rest. Autonomy protocol §4 applies (§4.12 for the PR
sequence). Build nothing outside plan §5.1. Never Fable, never spawn a session.

Owns: `CONTENT-SPEC.md`, `data/materials.php`, `data/categories.php`, `data/guides.php`,
`tools/smoke.php` (only if a new closed-content check is needed), `plan.md` §9 lines,
`STATUS.md`. No template, router, CSS, handler or `content/` changes.

Budget: ≤ 90 min per PR. Open each PR the turn its exit criteria pass; no re-polishing.

## PR 5a — `phase/5a-spec-amendments`
- Append CONTENT-SPEC §11 exactly as plan §5.1 "PR 5a" lists (a)–(e); amend §5's second H2.
- The keyword-ownership table covers every page that will be `activa` after 5c. Source:
  KEYWORDS-MATERIALES §5.1 + §1 tables. One head term → one page; conflicts resolved there.
- Exit: `php -l` green, CI green, PR merged, §9 line written.

## PR 5b — `phase/5b-new-materials`
- Six new `data/materials.php` entries + synonym edits + two `data/guides.php` entries +
  `intro_keywords[]` updates, exactly per plan §5.1 "PR 5b". Match the existing entry shape
  (see `varilla-de-hierro`); every entry gets the `¿Cuánto cuesta …?` FAQ (§1.13).
- Load `paraguay-local-site` (voseo, anti-fabrication) and `seo-web-builds` (titles ≤ 60,
  metas ≤ 155). Missing skill ⇒ nearest equivalent, note it, keep going.
- Vocabulary check: every synonym must be a word a Paraguayan buyer uses. KEYWORDS §3.2
  lists foreign words to avoid (grifo, garaje, termopanel, acma, superboard, rotoplas…).
- Exit: `php tools/smoke.php` green (closed content, slug uniqueness, activa-in-activa),
  `bash tools/render-check.sh` green, CI green, PR merged, §9 line.

## PR 5c — `phase/5c-category-promotion`
- Flip the five categories in plan §5.1 "PR 5c" to `activa` and author every listed
  material entry (status `activa`), same bar as 5b. Verify `cano-de-agua` demand against
  KEYWORDS §4.2 wording before authoring; drop it if you cannot justify it — note why.
- Update each promoted category's `intro_keywords[]`, `title`, `meta`, `faq[]` to the owned
  head terms (they were written blind in phase 3). Do NOT touch the 5 launch categories'
  titles/metas.
- Exit: smoke + render-check + CI green; every activa category has ≥ 3 activa materials;
  PR merged; §9 line; `STATUS.md` phase table updated (5a–5c merged, 6–8 pending).

## After PR 5c — STOP. Model switch.
Do not spawn anything (§4.12). Post the phase report: the three PR links, the final slug
count, anything logged in KNOWN-ISSUES, and this exact line for Anton to paste into a fresh
**Sonnet** window with auto-accept permissions:
`Read prompts/sonnet-6-content-wave1.md in this repo and execute it.`
