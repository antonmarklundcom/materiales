# Phases 13–15 — Calculators + guías, technical hardening, link pass + launch QA. SONNET window. Ships THREE PRs in sequence (13 → 14 → 15), then STOPS. Start ONLY after PR 12 is merged.

Read ONLY: this file, `plan.md` §1 (esp. 17–26), §4, §11.5–§11.7, the phase table and §9
index, `CONTENT-SPEC.md` §4, §5, §6, §11.2, §11.3, §12, `docs/log/9..12-*.md`, and
`docs/decisions-needed.md` if it exists. Do not read the rest. Autonomy protocol §4
applies (§4.12, §4.14, §4.15). Never Fable, never spawn a session.

HARD LIMITS (plan §4.7 + §1.22): no router, template, partial, CSS-rule, JS, handler,
consent, data-KEY or smoke changes except where a PR's Owns list below explicitly names the
file. You MAY add entries to `data/guides.php` and `data/calculators.php` (same shape as the
exemplars) and set `image` / `related[]` / `status` values. Anything else you need →
`docs/decisions-needed.md` + workaround, keep building.

Budget: ≤ 90 min per PR; open the PR the turn its exit criteria pass; no re-polishing.
Verify before every push:

```sh
find . -path ./.git -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
php tools/smoke.php && ./tools/render-check.sh
```

## PR 13 — `phase/13-calculadoras-guias` (plan §11.5)
- Three calculators on the phase-12 shape; six guías. Same-shaped units: write ONE
  exemplar yourself (one calculator, one guía), then fan out the rest to parallel Sonnet
  subagents per `fable-directs-sonnet-builds` §Fan-out, each with its slug, head terms,
  `related[]`, and the CONTENT-SPEC §5/§6/§12 rules pasted in. One verify, one PR.
- Load `paraguay-business-apps` for vocabulary; missing skill ⇒ nearest equivalent, note
  it, continue. Voseo, no prices, no brands outside §11.2, no invented PY figures.
- Exit: §11.5. Log `docs/log/13-calculadoras-guias.md`.

## PR 14 — `phase/14-tech-hardening` (plan §11.6, decisions §1.24–1.26)
- `.htaccess` header/deflate blocks only (the rewrite block is untouchable), self-hosted
  fonts, sitemap `lastmod` from `updated`, 404 canonical, `og:type`,
  `tools/replay-leads.php` + DEPLOY cron note, `tests/mobile-overflow.mjs`.
- Font download blocked ⇒ `docs/decisions-needed.md` with the exact URLs, keep the Google
  Fonts link; never ship font-less.
- Exit: §11.6. Log `docs/log/14-tech-hardening.md`.

## PR 15 — `phase/15-link-pass-launch` (plan §11.7)
- Editorial cross-links in prose (script-verified: 0 material pages without a guía or
  calculator link), image placement if files exist on `main` (else one pipeline attempt
  if the CDN is reachable, else log and move on), QA of every new page type,
  KNOWN-ISSUES promotion, STATUS, README, `docs/log/README.md`.
- Exit: §11.7. Log `docs/log/15-link-pass-launch.md`.

## After PR 15 — STOP. Do not spawn anything.
Closing report to Anton: what shipped (PR links), open KNOWN-ISSUES, which §7 go-live
items are still empty in `data/site.php` / on the server, the `docs/decisions-needed.md`
questions verbatim, and the suggested next planning input (second Keyword Planner pull).
