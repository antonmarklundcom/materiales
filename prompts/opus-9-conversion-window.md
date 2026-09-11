# Phases 9–12 — Conversion, supplier recruitment, cross-links, calculators. OPUS window. Ships FOUR PRs in sequence (9 → 10 → 11 → 12), then STOPS.

Read ONLY: this file, `plan.md` §1 (esp. 17–26), §4, §11.1–§11.4, the phase table and
§9 index, `CONTENT-SPEC.md` §1, §4, §5, §7, §11.2, §11.3, `docs/IMPROVEMENT-REPORT.md` §2–§4,
and `docs/log/` (empty today — fine). Do not read the rest. Autonomy protocol §4 applies
(§4.12 for the PR sequence, §4.14 phase logs, §4.15 prompt re-read). Never Fable, never
spawn a session.

Each PR's **Owns** list is in its plan section and is the only place you write, plus
`docs/log/<phase>.md`, the §9 index line and `STATUS.md`. Never touch `content/materiales`,
`content/categorias`, `content/guias` prose, the buyer form's fields or consent text, or
`data/*.php` values (adding a documented optional key with no values is allowed in 11/12).

Budget: ≤ 90 min per PR; open the PR the turn its exit criteria pass; no re-polishing.
Branch names and exit criteria: plan §11.1–§11.4. Verify locally before every push:

```sh
find . -path ./.git -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
php tools/smoke.php && ./tools/render-check.sh
```

Screenshots: Playwright with a real viewport only (`/opt/pw-browsers/chromium`), ONE pass
per PR after the last code change, files in git-ignored `docs/screenshots/`, never committed.

## PR 9 — `phase/9-home-conversion` (plan §11.1)
- Homepage rebuild, hero CTA on money pages, `partials/cta.php` sticky mobile bar
  (form first, WhatsApp only when set — §1.18), `/gracias/` next steps.
- Quality bar: the homepage must read as a page a Paraguayan contractor would trust, not
  a landing-page template. Voseo, no prices, no brands, no "somos líderes".
- Exit: §11.1. Log `docs/log/9-home-conversion.md`.

## PR 10 — `phase/10-proveedores` (plan §11.2, decision §1.17)
- `/proveedores/` landing + `partials/form-proveedor.php` + handler branch on
  `tipo=proveedor` + `proveedor-v1` consent + privacy clause + nav link.
- The buyer path stays byte-identical: render-check's existing LEAD HANDLER block must pass
  without edits, and smoke must assert the buyer payload has no `tipo`.
- Exit: §11.2. Log `docs/log/10-proveedores.md`.

## PR 11 — `phase/11-crosslinks-images` (plan §11.3, decisions §1.19–1.20)
- `guides_for()`, `calculators_for()` (guarded — `data/calculators.php` does not exist
  yet), `image_for()`, `partials/related.php`, `partials/hero-image.php`, per-page
  `og:image`, `Product.image`, smoke validation of declared images.
- Zero visual change on pages without an image. No image files are added in this PR.
- Exit: §11.3. Log `docs/log/11-crosslinks-images.md`.

## PR 12 — `phase/12-calculadoras-foundation` (plan §11.4, decision §1.21)
- Write CONTENT-SPEC §12 FIRST (dosages with source lines, wording rules, the JSON formula
  schema `calc.js` evaluates — an expression tree, never `eval`). Then route (`.htaccess`
  AND `tools/router-cli.php`), `data/calculators.php`, template, `calc.js`, the exemplar
  `bolsas-de-cemento-por-m2`, sitemap, nav, smoke, render-check.
- Exit: §11.4. Log `docs/log/12-calculadoras-foundation.md`. Update `STATUS.md` (9–12
  merged, 13–15 pending).

## After PR 12 — STOP. Do not spawn anything.
Report to Anton: the four PR links, anything written to `docs/decisions-needed.md`, and
this exact line to paste in a fresh **Sonnet** window with auto-accept permissions:

`Read prompts/sonnet-13-content-window.md in this repo and execute it.`
