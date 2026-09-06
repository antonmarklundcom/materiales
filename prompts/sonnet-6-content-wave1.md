# Phase 6 — Content wave 1. SONNET window (PR 1 of 3: 6 → 7 → 8). Start ONLY after PR 5c is merged.

Read ONLY: this file, `plan.md` §1, §4, §5 (incl. §5.1), the phase table and §9 index,
`CONTENT-SPEC.md` in full (esp. §5 structure and §11 keyword ownership), `KEYWORDS-MATERIALES.md`
§2 (write-first list) and §5, and the §9 lines of 5a–5c. Autonomy protocol §4 applies
(§4.12 PR sequence, §4.13 what you may change in data files). Never Fable.

HARD LIMITS (plan §4.7): no changes to router, templates, data-file keys, lead handler, URL
taxonomy, consent text, CSS/design system. You write `content/**` files and flip `status`
values per §4.13 — nothing else.

Owns: `content/categorias/{hierro,cemento-y-cal,aridos,ladrillos-y-bloques,chapas-y-techos}.php`,
`content/materiales/{every activa material in those 5 categories}.php`, `content/guias/*.php`
(all 8), `data/guides.php` status values only, `plan.md` §9 line, `STATUS.md`.

Budget: ≤ 90 min. Branch `phase/6-content-wave1` off latest main. WIP commit every 30 min.

Phase rules:
- Load `paraguay-local-site` (voseo, anti-fabrication) and `seo-web-builds`. Missing ⇒
  nearest equivalent, note it, keep going.
- Order = KEYWORDS-MATERIALES §2 write-first list: chapa-termoacustica, perfiles-metalicos,
  chapa-trapezoidal, cemento, chapa-de-zinc, varilla-de-hierro, chapas-y-techos, hormigon-
  elaborado, ladrillo-prensado, ladrillo-hueco, adhesivo-para-ceramica, arena-lavada,
  cemento-y-cal, malla-electrosoldada, ripio, piedra-triturada, ladrillo-comun, then the rest
  of the 5 categories (incl. the 5b additions), then the 8 guías.
- Write chapa-termoacustica yourself as the exemplar (CONTENT-SPEC §5 structure, 350–600
  words, synonyms from `data/materials.php` woven in, the medidas table where §11 requires
  it, the precio FAQ visible, closing links to category + 2 related with descriptive
  anchors). Then fan out the remaining materials as parallel Sonnet subagents, one per
  category, per `fable-directs-sonnet-builds` §Fan-out: each subagent gets the exemplar,
  CONTENT-SPEC §5 + §11 rows for its pages, and its data entries. One verify, one PR.
- A page that reads templated with the noun swapped is a defect. No prices, no supplier
  names, no brands outside CONTENT-SPEC §11's list, no invented figures, no "tú".
- Guías: 600–900 words, H2s and mandatory links from CONTENT-SPEC §6 and §11(e); flip each
  guide to `activa` in `data/guides.php` when its prose exists.
- Re-runnable: check what exists on the branch first. Minor issues → KNOWN-ISSUES.md.

Exit: every activa page of the 5 categories + all 8 guías render full prose (no
"estamos publicando" notice), `bash tools/render-check.sh` + `php tools/smoke.php` + CI
green, sitemap lists the guías, PR merged, §9 line, STATUS updated.

## After this PR — continue in THIS window
Verify the merge via `mcp__github__*`, then read and execute `prompts/sonnet-7-content-wave2.md`.
