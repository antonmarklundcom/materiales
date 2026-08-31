# Phase 1 — Foundation. Paste into a fresh OPUS session.

Read `plan.md` FIRST, in full — plus §9 build log and `KNOWN-ISSUES.md` (if present).
Execute plan §1–§2 (structure, router, data layer) under the autonomy protocol §4.
Build nothing outside the plan.

Phase rules:
- Branch `phase/1-foundation` off latest main.
- Load these skills at the matching step: `budgeted-runner-deploy` (before creating anything
  under `.github/`), `seo-web-builds` (canonical/robots/sitemap conventions). If a named
  skill is missing, use the nearest equivalent, note it in the build log, keep going.
- Deliver: repo layout from plan §2; `.htaccess` rewrites (`/materiales/{slug}/` → router,
  `sitemap.xml` → `sitemap.php`); `partials/` (header, footer trust stack placeholder,
  schema blocks, cookie banner shell); `data/site.php`, `data/categories.php`,
  `data/materials.php` seeded with the plan §5 launch taxonomy (slugs + names + status only —
  copy comes in phase 3); `config.sample.php`; 404; robots.txt; sitemap.php.
- CI: ONE workflow, ≤1 min — `php -l` over all PHP + a smoke script asserting data files
  load and slugs are unique across categories+materials. This is the required check.
- Deploy is Hostinger Git webhook on main — GitHub Actions is NEVER in the deploy path.
- Placeholder pages render with real `<title>`/meta from data files; no invented NAP/RUC —
  placeholders clearly marked in `data/site.php` comments only, never visible on-page.
- Re-runnable; minor issues → `KNOWN-ISSUES.md`; stop only per plan §4.4.

Exit: CI green; router serves `/`, `/materiales/`, one category, one material, 404;
sitemap.php lists only status=activa pages; slug-uniqueness check passes; PR merged.

## After this phase — hand off to the next (fresh session)
Verify the merge via `mcp__github__*` tools (PR merged, origin/main contains the commit,
checks green), pass the four gates of plan §4.9 (incl. pre-handoff audit + build-log entry),
then spawn a NEW session via `create_session`: inherit environment and permission mode
(never `plan`), model **Opus** (never Fable), prompt exactly:
`Read prompts/opus-2-lead-pipeline.md in this repo and execute it.`
If `create_session` is unavailable, continue in this window (same model). Never hand off on
an unmerged or unverifiable PR — report the blocker instead.
