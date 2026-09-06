# Phase 7 — Content wave 2. SONNET window (PR 2 of 3). Start ONLY after PR 6 is merged.

Read ONLY: this file, `plan.md` §1, §4, §5.1, the phase table and §9 index, `CONTENT-SPEC.md`
§5 and §11, `KEYWORDS-MATERIALES.md` §1.2 and §5, and the §9 lines of 5c and 6. Autonomy
protocol §4 applies (§4.12, §4.13). Never Fable.

HARD LIMITS (plan §4.7): content includes and `status` flips only. No router, template,
data-key, handler, taxonomy, consent or CSS changes.

Owns: `content/categorias/{pisos-y-revestimientos,aberturas,impermeabilizantes,yeso-y-durlock,
canos-y-plomeria,madera}.php`, `content/materiales/{their activa materials}.php`,
`data/materials.php` + `data/categories.php` status values only (madera's six materials flip
proxima → activa here as their prose lands), `plan.md` §9 line, `STATUS.md`.

Budget: ≤ 90 min. Branch `phase/7-content-wave2` off latest main. WIP commit every 30 min.

Phase rules:
- Same skills, same exemplar (chapa-termoacustica from phase 6), same bar as phase 6.
- Order = plan §1.15: pisos-y-revestimientos → aberturas → impermeabilizantes →
  yeso-y-durlock → canos-y-plomeria → madera. Fan out one Sonnet subagent per category.
- Aberturas pages describe made-to-measure products: the "Cómo se vende…" H2 explains what
  the fabricator needs (medidas, vano, apertura) — no prices, no "we install".
- Tanque-de-agua, policarbonato-type pages carry their sizes table (CONTENT-SPEC §11(d)).
- Thin is a defect: a promoted material whose prose cannot reach 350 genuinely different
  words stays `proxima` (flip nothing) and gets a KNOWN-ISSUES line saying why.
- Re-runnable; minor issues → KNOWN-ISSUES.md.

Exit: every activa page in the six categories renders full prose; no activa material sits
in a proxima category; render-check + smoke + CI green; PR merged; §9 line; STATUS updated.

## After this PR — continue in THIS window
Verify the merge via `mcp__github__*`, then read and execute `prompts/sonnet-8-imagery-qa-launch.md`.
