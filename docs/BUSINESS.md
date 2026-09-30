# BUSINESS — materiales.com.py (phase 1, 2026-09-30)

Quote-request marketplace: the buyer sends material, quantity and zone; up to 3 verified
suppliers reply on WhatsApp. Baseline: SEO 8 · Design 5 · Copy 7 · Conversion 8.5 · Technical 6.5.

## 0. What the audit found first

- **Repo checks pass on `main` (351807c):** php -l, `smoke.php` (13 active categories, 78
  materials, 21 guides, 10 calculators), `check-rewrites.php`, `render-check.sh`.
- **Production looks older than `main`.** The baseline says no favicon, 108 sitemap URLs and
  1 image. But `main` ships favicons (#46), a 133-URL sitemap and 12 `<img>` on the home page
  (#58 redesign). The sandbox cannot reach materiales.com.py, so this is unconfirmed. If it
  holds, part of the Design 5 and Technical 6.5 scores is deploy lag, not a code problem.
- **The core promise is not provable today.** The words "verificado(s)" appear 232 times across
  33 files, and "(dentro del / en el) día" 97 times. The repo holds no suppliers by design
  (plan §2). There is no `config/vendercrm.php`, so every lead since 2026-09-16 sits in
  `leads.log` in log-only mode (`solo_log`). `supplier_pitch` is empty.
- **The keyword-library MCP is not connected in this session.** The ranking below uses the
  Keyword Planner pull in `KEYWORDS-MATERIALES.md` (PY, de-duplicated). Bids are in SEK.

## 1. Category ranking

Score = de-duplicated monthly volume (thousands) × ln(1 + median real bid) × quote fit. Bids
that come from a single advertiser (19,15 on cemento/perfiles) are left out. Quote fit is 1.0
for bulk goods bought by quote and delivered, 0.8 for goods chosen in a showroom but sold by
m², and 0.5 for retail or DIY goods.

| # | Category | Vol/mo | Bid | Fit | Score | Who supplies it |
|---|---|---|---|---|---|---|
| 1 | Hierro (varillas, perfiles, mallas, tejido) | ~10.8k | ~8 | 1.0 | **23.7** | Corralón + hierro specialist |
| 2 | Chapas (termoacústica, trapezoidal, zinc) | ~10.5k | 7.6 | 1.0 | **22.6** | Chapería / roofing factory |
| 3 | Pisos (porcelanato, cerámica, vinílico) | ~12.0k | 5.9 | 0.8 | **18.6** | Casa de cerámicos |
| 4 | Ladrillos (+ adoquines, refractario) | ~6.6k | 7.1 | 1.0 | **13.8** | Corralón / olería |
| 5 | Pinturas | ~15.0k | 4.6 | 0.5 | **12.9** | Pinturería (retail, DIY) |
| 6 | Cemento (+ cal, hormigón) | ~5.2k | ~8* | 1.0 | **11.4** | Corralón |
| 7 | Arena (áridos, tierra) | ~4.9k | 5.0 | 1.0 | **8.8** | Corralón / arenera |
| 8 | Sanitarios | ~9.0k | ~1.8 | 0.5 | **4.6** | Showroom (retail) |

\*imputed. **Recruit in this order: supplier types, not categories.** One general corralón
covers 4 of the 8 (hierro, cemento, ladrillos, arena), so recruit corralones first, then
chaperías, then casas de cerámicos. Pinturas and sanitarios rank low because people buy them
off the shelf in a store. Keep their content, but don't recruit for them until the top 3
supplier types have coverage.

## 2. Business model

**Recruitment (plan §8.4, still valid):** 2–3 *founding* suppliers per launch category, each
given 5–10 free leads in exchange for fast replies, feedback and a reference. Paid packs come
only after the free leads have shown they work. Never sell against projected SEO traffic.
Sources:
- corralones on Google Maps in the 11 `area_served` cities;
- the costeo.com.py supplier directory and Construex exhibitors;
- WhatsApp price-list broadcasts (KNOWN-ISSUES #18 proves they exist);
- **obra.com.py's own buying relationships**, the warmest intro we have.

**Payment: pay per lead delivered, as manual prepaid packs.** This is already decided (plan
§1.6) and it is what `/proveedores/` says.
- *Per closed quote:* rejected. The sale happens on WhatsApp where we can't see it, so it
  invites under-reporting.
- *Subscription:* later, and only as a monthly allowance of N leads per category and zone,
  once volume is steady. Selling volume we can't deliver breaks trust (the same rule as the
  ARQ brief §6.5). Benchmark: Revista COSTOS charges 85,000 Gs/month or 750,000 Gs/year for
  price data plus a supplier guide, so PY construction firms do pay recurring fees.
- **Lead price** = average ticket × supplier gross margin × win rate (≈1/3) × our share. The
  inputs have to come from the founders. Don't publish a price until 3 founders have agreed to it.
- **Credit policy (needed before charging):** credit back leads with a wrong number, a
  duplicate, an out-of-zone address, or a category the supplier didn't sign up for.

**Current supplier count: unknown from the repo.** Suppliers live in Anton's spreadsheet and
VenderCRM (plan §2) → *Anton to supply: verified suppliers per category and zone.*

**What must be true for "verified suppliers" to be honest:**
1. A written check, run and dated for every supplier: RUC active in SET (Marangatu), company
   name matches, depot address exists (visit or Street View), the WhatsApp number answers,
   the supplier sells the category (price list or catalog), delivery zones, and can issue a
   legal invoice (factura legal). Keep the record in VenderCRM. Re-check every 12 months and
   drop anyone who stops replying.
2. **At least 1 verified supplier in each active category** before its form says
   "verificados". Otherwise the copy falls back to "te contactamos nosotros". This needs a
   per-category flag (a count only, no names). Today 13 categories make the promise.
3. **"Normalmente dentro del día"** stays only if it is measured: time from lead to first
   supplier reply, tracked in VenderCRM. Until then, soften it.
4. **Leads are actually forwarded:** VenderCRM connected, the backlog replayed, and a
   forwarding SLA owned by a named person.
5. **A named data controller:** company name and RUC in the privacy policy (KNOWN-ISSUES #7).
   Plus a one-page supplier agreement: use the lead only for that quote, no resale, no spam.

## 3. Competitors

| Competitor | Model | Threat / lesson |
|---|---|---|
| [Construex PY](https://www.construex.com.py/) | Directory + RFQ, exhibitor plans | Big and generic ("1M suppliers"), no quantity or zone qualification. We beat it on lead quality. |
| [Costeo](https://www.costeo.com.py/) | Publishes prices (cemento CP-32 Gs 1,352/kg) + supplier directory + calculator | Wins "precio" queries that we answer only with factors. Decision D1 (price index) matters. |
| [Revista COSTOS](https://www.costos.com.py/) | 35-year magazine; paid price data; supplier guide in its Plus plan | Authority among professionals; a possible partner or backlink, not a buyer-side rival. |
| [Red Materiales (AR)](https://redmateriales.com.ar/) | Closest analog: verified corralones, list upload, **price with delivery included**, city pages | Validates D3 city pages and the "Pegá tu lista" feature (C11). Its gap vs us: it shows prices. |
| [CONSTRUMAT PY](https://mbaapopy.com/catalogos/materiales-premium/index.html) | Single seller: prices, stock, WhatsApp orders | Shows what PY buyers expect: price and stock on WhatsApp, fast. |

The real incumbent is the buyer phoning 3 corralones. Our pitch against that: one form, 3
comparable quotes, no calls.

## 4. Sister sites: cross-referral flow

All sites share **one WhatsApp (+595 992 279 599)**, so referral is a tag in VenderCRM, not a
hand-off between companies.
- **obra** is the family builder (llave en mano).
- **arq** matches clients with architects (planos, carpeta municipal).
- **carpinteria** does made-to-measure wood, aluminium and blindex, installed.
- **edificio: no repo or brief exists.** Needs Anton's definition.

| From → To | Trigger | Where |
|---|---|---|
| materiales → obra | "construir mi casa", whole-build quantities, "no tengo quién construya" | /gracias/, cost guides, WhatsApp triage |
| materiales → arq | planos, aprobación municipal, cómputo métrico | "cuánto cuesta construir" guides, triage |
| materiales → carpinteria | aberturas or madera **a medida + instalación** (supply-only stays with suppliers) | aberturas, madera and vidrio-templado pages |
| obra / arq / carpinteria → materiales | client buys their own materials; material list after planos | their /gracias/ + "Pegá tu lista" link |

Rules:
- **Separate consent.** Today's consent covers only suppliers of the requested category, so an
  extra opt-in is required before any lead reaches obra, arq or carpinteria.
- **Disclose** that obra is the same group.
- **One lead record**, tagged `ref=<site>`, never sold twice.
- **Contextual links only.** No sitewide network footer.

## 5. What gets each parameter to 9 (priority order)

1. **Deploy parity (Tech, Design, SEO):** confirm production runs `main` 351807c, run
   `tools/prod-check.sh`, then re-score. The redesign, favicons and 25 missing URLs may already
   close much of the gap.
2. **Make the promise true (Copy, Conv):** §2 checklist, founders recruited, per-category
   fallback copy, the "dentro del día" line measured or softened.
3. **Connect VenderCRM + forward leads (Conv):** config, backlog replay, crons, forwarding SLA.
4. **Measurement (SEO, Conv):** GA4, Search Console via DNS, sitemap submitted. A 9 can't be
   proven without data.
5. **Identity and E-E-A-T (Copy, Tech):** company name, RUC, email, opening hours, data
   controller in the privacy policy, a named technical reviewer for guides and calculators.
6. **Design to 9:** photos on the 78 material pages (0 today), real supplier or depot photos,
   proof blocks (verified count per category, founder quotes, real only).
7. **SEO to 9:** second Keyword Planner pull (G7); decide D1 (price index vs Costeo); D3 city
   pages only where suppliers cover the zone; the badge on every verified supplier; CAPACO and
   FIUNA links.
8. **Conversion to 9:** server-captured WhatsApp leads, supplier response-time tracking,
   bundle mode on more calculators, sister-site opt-in on /gracias/.
9. **Technical to 9:** HSTS and 301s confirmed on prod, UptimeRobot, CrUX/Core Web Vitals field
   data, CSP (backlog R7).
