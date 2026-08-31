# Phase 6 — Content wave 2 + launch QA. Paste into a fresh SONNET session, ONLY after phase 5 is merged. FINAL PHASE.

Read `plan.md` FIRST, in full — plus §9 build log, `KNOWN-ISSUES.md`, and `CONTENT-SPEC.md`.
Finish the remaining content, imagery, and the launch QA pass. Autonomy protocol §4 applies.

HARD LIMITS (plan §4.7): no changes to router, data-file schemas, lead handler, URL
taxonomy, consent text, or the design system.

Phase rules:
- Branch `phase/6-content-wave2-qa` off latest main. Phase 5 unmerged ⇒ finish it first.
- Load `seo-web-builds` (full §6 checklist) and `higgsfield-web-imagery` for image slots +
  OG images (1200×630 per money page — WhatsApp previews are the ad in PY). Missing skill ⇒
  nearest equivalent (for imagery: ship palette motif panels, note it), keep going.
- Write prose for remaining próxima categories + wave-2 materials (madera set first) to the
  same bar as phase 5 — publish only pages with genuinely different content; thin ones stay
  status=proxima rather than shipping thin.
- Fill all image slots (WebP, es-PY alt text, descriptive filenames); no generated faces,
  no fake "our work" photos.
- Run the FULL `seo-web-builds` §6 pre-launch checklist + `web-design-system` QA gate and
  fix every failure. Verify sitemap/robots/canonicals, OG tags, JSON-LD validity, form
  round-trip per `vendercrm-lead-capture` "Verify" section (if CRM config present on the
  server; else assert leads.log path and say so in the report).
- Leave staging `noindex` in place UNLESS the build log says the domain is live.

Exit: CI green; checklist passes; all activa pages have prose + images + OG; PR merged.

## After this phase — STOP. Do not spawn anything.
Verify the merge (plan §4.9 gates), append the final build-log entry, then post the closing
report to Anton: what's live, KNOWN-ISSUES summary, and the exact numbered manual steps —
(1) point materiales.com.py DNS at the Hostinger slot, (2) upload `config/vendercrm.php`
with the real CRM URL + key, (3) set GA4/Pixel IDs in `data/site.php`, (4) remove noindex,
(5) submit sitemap in Search Console, (6) start founding-supplier outreach per plan §8.4.
