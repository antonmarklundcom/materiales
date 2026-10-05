Materialval låg efter långa texter och kalkylatorerna kunde föra vidare mängder utanför sina visade intervall. Denna PR gör vägen kategori → material → mängd → offertlista tydligare och stoppar ogiltiga kalkylatorresultat, med befintlig PHP-sajt, katalog, guider och offert-/leverantörsflöden bevarade.

## Ändringar

- Materialval och nästa steg tidigare på startsida och kategorier; enheter och befintliga relaterade kalkylatorer syns vid förberedelsen.
- Progressivt katalogfilter med synonymer, accentoberoende matchning och nollresultat. Samtliga 93 materiallänkar ligger kvar i serverrenderad HTML.
- Samtliga tio kalkylatorer respekterar intervall, tomma/ogiltiga värden och ändliga resultat. Offertpaket och manuellt skriven mängd bevaras; formler, spill och antaganden är oförändrade.
- Synliga formulär-/tacktexter skiljer mottagen konsultation från lager, köp och vidarebefordran. Befintlig CRM-adapter, payload, samtycken, signering och servervalidering är bevarade.
- Relevanta regressionstester i det befintliga enda CI-jobbet; Windowsportabilitet i smoke-testet.

## SEO och validering

Live granskades först och jämfördes med `main` på `83f4023`. Bas och live ger samma inventerade SEO/internlänkar. Ingen äldre serverversion har ersatts.

- SEO-jämförelse mot både bas och live: **149 sitemapadresser / 151 sidor; alla title/H1/meta/canonical/JSON-LD, robots och sitemap samt alla befintliga länkar kvar**. 124 länktillägg räknat över sidorna, inga URL-flyttar.
- PHP-lint: 191 filer. Befintliga smoke-, rewrite- och renderkontroller godkända.
- 270 kalkylatorkontroller över tio verkliga formelspecifikationer.
- Lokal CRM-mock: köparlista, leverantör, idempotens, 201/200 och 422/500. Inga produktionsleads.
- 261 interna sid-/resursmål, 409 ankare, 14 privata vägar och tre slashredirects kontrollerade.
- 27 faktiska webbläsarvyer på 320/390/1440 px utan overflow. Filter, meny Enter/Escape, offertvalidering och kalkylator → paket verifierade.
- Higgsfield **0 av 50 krediter**, befintliga responsiva bilder återanvänds. Inga Lighthouse-poäng påstås.

[Full svensk rapport, mobil-/offertbilder, testutskrifter, publiceringsunderlag och nästa-session-prompt](https://github.com/antonmarklundcom/materiales/blob/codex/materiales-catalogo-offer-review/docs/review-2026-10-05/RAPPORT.md).

**GitHub CI-status:** Actions är avstängt för repot (`actions/permissions: enabled=false`), så inga CI-körningar startas på denna PR. Workflowfilen finns och är aktiv. Ovanstående tester är faktiskt körda och godkända lokalt; inga gröna GitHub-checks påstås. Actions-inställningen har inte ändrats.

## Före / efter

Före är live, efter lokal preview, 1440 × 1000. Startsidan före har ursprunglig cookiepanel; efter har valfria cookies avvisade.

| Vy | Före | Efter |
|---|---|---|
| Startsida | ![Före](https://raw.githubusercontent.com/antonmarklundcom/materiales/903a06fd22e5c0f7f71381d38b86b7c74e4098d5/docs/review-2026-10-05/before-home-desktop.jpg) | ![Efter](https://raw.githubusercontent.com/antonmarklundcom/materiales/903a06fd22e5c0f7f71381d38b86b7c74e4098d5/docs/review-2026-10-05/after-home-desktop.jpg) |
| Cemento y cal | ![Före](https://raw.githubusercontent.com/antonmarklundcom/materiales/903a06fd22e5c0f7f71381d38b86b7c74e4098d5/docs/review-2026-10-05/before-category-desktop.jpg) | ![Efter](https://raw.githubusercontent.com/antonmarklundcom/materiales/903a06fd22e5c0f7f71381d38b86b7c74e4098d5/docs/review-2026-10-05/after-category-desktop.jpg) |

## Inför ägarens mergebeslut

PR:n är **ready, inte draft**, enligt Antons senaste instruktion. Ingen merge/deploy/DNS-/produktionsconfigändring har gjorts. Merge till `main` kan trigga befintlig Hostinger-deployment enligt DEPLOY.md.

Ägaren behöver fortfarande bekräfta aktuell CRM-mottagning och bevakning, verifierade leverantörer/zoner, godkänd befintlig WhatsApp-mottagare +595992279599 och ansvariguppgifter. Lokala mocktester bevisar inte produktionens anslutning eller bemanning. Äldre affärspåståenden i SEO-data/schema och redaktionella texter är uttryckligen bevarade enligt ägarens SEO-instruktion och måste verifieras före publicering eller hanteras i en separat godkänd revision.

Läsande produktionskontroll hittade en **befintlig** tvåhoppsredirect `http://www` → `https://www` → HTTPS-apex (slutligen 200); övriga kontrollerade publika/privata vägar och säkerhetsrubriker klarade kontrollen. Ingen routing-/hostingändring ingår här.
