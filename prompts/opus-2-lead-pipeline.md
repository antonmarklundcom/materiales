# Phase 2 — Lead pipeline. Paste into a fresh OPUS session, ONLY after phase 1 is merged.

Read `plan.md` FIRST, in full — plus §9 build log and `KNOWN-ISSUES.md`.
Execute plan §3 (lead flow end-to-end) under the autonomy protocol §4.

Phase rules:
- Branch `phase/2-lead-pipeline` off latest main. Phase 1 unmerged ⇒ finish it first.
- Load `vendercrm-lead-capture` BEFORE writing the form or handler, and follow its
  `references/php.md` exactly. If missing, follow plan §3 verbatim — it carries the contract.
- Deliver: `partials/form.php` (material preselected per page, cantidad, ciudad, nombre,
  teléfono required, mensaje, unticked consent checkbox, honeypot, min-time trap);
  `/cotizar/` page; `/cotizar/enviar.php` handler per plan §3 (idempotency key, 10s timeout,
  try/catch, leads.log JSONL, 303 → `/gracias/?m=`); `/gracias/` with GA4
  `cotizacion_form_submitted` + Meta Pixel `Lead` gated on marketing consent; vc-attribution
  snippet sitewide + `vc_attr` cookie merge server-side; `/politica-de-privacidad/` naming
  the responsable, supplier sharing, and VenderCRM as encargado (Ley 7593).
- NEVER send pipeline/stage/owner/tag. Missing CRM config ⇒ degrade to leads.log-only,
  document in `config.sample.php`, keep shipping.
- Consent text and payload contract are foundation — changing them later is a §4.4 stop.
- Verify with a real round trip only if `config/vendercrm.php` exists on a reachable CRM;
  otherwise assert the leads.log path and payload shape via the smoke script.

Exit: CI green (smoke extended: handler validates phone, rejects unchecked consent, writes
leads.log, honeypot short-circuits, duplicate submit produces identical idempotency_key);
PR merged.

## After this phase — hand off to the next (fresh session)
Same four gates as plan §4.9, merge verified via `mcp__github__*` tools, build-log entry
committed. Spawn a NEW session via `create_session`, model **Opus** (never Fable), prompt:
`Read prompts/opus-3-content-spec.md in this repo and execute it.`
Fallback without `create_session`: continue in this window. Never hand off unmerged.
