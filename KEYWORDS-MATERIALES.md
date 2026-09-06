# Keyword research — bucket "Materiales" (1.029 kw, Google Keyword Planner, Paraguay)

Input: `materiales-keywords.md` (1.029 phrases, 242.380 raw monthly searches, 142 with a
top-of-page bid). Purpose: decide what the phase 5–6 content prioritizes, which taxonomy
pages are missing, and which keywords are noise from the rule-based classifier.

Two caveats that apply to every number below:

- **Keyword Planner reports one volume per keyword *group*.** Plural, accent and spacing
  variants share the same figure (`chapa termoacustica` and `chapas termoacusticas` both
  3.600; `cerámica`/`ceramica`/`cerámicos`/`ceramicos` all 1.600; four spellings of
  `malla electrosoldada` all 590). Summing them double-counts. The de-duplicated total is
  roughly 200k, not 242k. Every cluster figure below is the de-duplicated one.
- **The bid columns are dominated by a handful of Paraguayan advertisers with manual
  bids.** The flat 76,59 kr on every `cerradura*` phrase, the flat 19,15 on `perfiles`,
  `cemento`, `hormigón`, `hormigón armado`, and the flat 14,36 on `cal agrícola` and
  `puertas` are single-advertiser artefacts. Treat bids as an ordinal "someone pays for
  this" signal, never as a market price.

---

## 1. Clusters → taxonomy mapping

Status legend: **A** = category already `activa` in `data/categories.php`; **P** = category
exists but is `proxima`; **NEW** = no page in the taxonomy today; **OUT** = not a
materiales page (belongs in Productos / Profesionales or is noise).

### 1.1 Clusters that map to ACTIVE categories

| Cluster | Dedup vol | Head terms (vol · bid high) | Maps to | Gap |
|---|---|---|---|---|
| Chapas metálicas | ~10.500 | chapa termoacústica 3.600 · 7,58 · chapa trapezoidal 1.600 · 11,01 · zinc 1.600 · 6,56 · chapa 1.300 · 8,60 · chapa zinc 880 · chapa sandwich 720 · 5,49 · chapa galvanizada 390 · 30,83 · techos de chapa 480 · chapa acanalada 480 · techo termoacústico 480 · aislante térmico para techo 390 · 11,33 | chapas-y-techos **A** → chapa-termoacustica, chapa-trapezoidal, chapa-de-zinc | None. Best-covered cluster in the file. `chapa sandwich`/`techo sandwich` → synonym of termoacústica (already `panel sándwich`). |
| Tejas | ~1.400 | teja francesa 320 · tejas 260 · teja española 170 · teja colonial 170 · teja romana 140 · techo de teja 140 | chapas-y-techos **A** → teja-espanola, teja-francesa | `teja romana` not a synonym anywhere; add to teja-espanola. |
| Fibrocemento | ~400 | fibrocemento 140 · eternit 140 · placa cementicia exterior 40 · placas de fibrocemento 30 · 38 brand long-tails at 10 | chapas-y-techos **A** → fibrocemento | Collapse all 42 phrases into the one page. Most long-tails are foreign brands (see §3). |
| Perfiles / hierro | ~8.500 | perfiles 4.400 · 19,15 · hierro 1.600 · 4,79 · acero 720 · 7,27 · varillas 480 · perfiles de aluminio 480 · 9,95 · perfiles c / en c 480 · 7,20 · perfiles en u 390 · varilla de 8 320 · perfiles de hierro 260 · varillas de hierro 260 · ángulo de hierro 210 · perfiles ipn 170 · perfiles upn 140 · varilla conformada 140 · vigas 170 · viga de hierro 170 | hierro **A** → perfiles-metalicos, varilla-de-hierro | perfiles-metalicos must carry H2s for C, U, IPN/UPN and ángulo; `perfiles` alone is ambiguous (social profiles) but the 2.300 of qualified long-tail confirms real demand. `perfiles de aluminio` belongs to aberturas, not hierro. |
| Mallas y alambre | ~3.600 | tejido de alambre 1.300 · 8,97 · malla 880 · alambre 720 · malla electrosoldada 590 (4 spellings) · alambre de púas 480 · malla metálica 390 · alambre dulce 390 · alambre galvanizado 210 · 13,77 · metal desplegado 320 | hierro **A** → malla-electrosoldada, alambre-negro | **Missing material: cercos** (`tejido de alambre` + `alambre de púas` + `alambre galvanizado` ≈ 2.000). `alambre dulce` is the Paraguayan word for alambre negro/recocido → add as synonym. `metal desplegado` has no home. |
| Cemento y hormigón | ~3.900 | cemento 1.000 · 19,15 · hormigón 880 · 19,15 · mortero 720 · concreto 320 · cemento precio 320 · cemento blanco 320 · cemento gris 320 · hormigón armado 320 · 19,15 · encofrado 320 · 14,16 · revoque 320 (+4 variants) · hormigón elaborado 70 | cemento-y-cal **A** → cemento, hormigon-elaborado | `cemento blanco` and `mortero` (premezclado) have no page: fold into cemento with H2s. `hormigón armado`, `encofrado`, `zapatas`, `losas` are construction-knowledge searches → guide, not material (see §5). |
| Cal | ~2.100 | cal 1.600 · cal hidratada 320 · cal agrícola 320 · 14,36 · cal precio 70 · cal para pintar 70 · cal dolomita 70 | cemento-y-cal **A** → cal-viva, cal-hidratada | **Not actually missing:** the taxonomy already has two cal pages. The 1.600 head term is split three ways (construction, agro, pintura a la cal). Only construction is ours; the category page should be the one targeting bare `cal`. `cal agrícola`/`dolomita`/`para plantas` are agro → ignore. |
| Áridos | ~3.400 | arena 880 · 5,01 · piedra 880 · canto rodado 1.000 · ripio 590 · gravas 320 · piedra caliza 320 · piedra triturada 260 · arena fina 110 · arena gruesa 40 · cascajo 20 | aridos **A** → arena-lavada, arena-gorda, ripio, piedra-triturada, piedra-bruta | `canto rodado` (1.000) outranks `ripio` (590) but is only a synonym — fine per spec, but make it the first word of the ripio intro. `la piedra` 1.300 is noise. |
| Tierra / relleno | ~1.500 | tierra colorada 1.300 · tosca 210 | aridos **A** → tierra-gorda, escombro-relleno | `tierra colorada` is the everyday PY term for fill dirt → add as synonym of tierra-gorda (verify: also a song/place name). `tosca` (sub-base) has no page. |
| Ladrillos y bloques | ~5.300 | ladrillo visto 1.300 · ladrillo hueco 1.000 · ladrillo refractario 880 · 7,06 · ladrillo 720 · bloques 590 · ladrillo prensado 390 · ladrillo común 390 · ladrillo sapo 260 · ladrillo común medidas 210 · bloques de cemento 170 · ladrillo hueco medida 170 · precio del ladrillo común 170 | ladrillos-y-bloques **A** → ladrillo-comun, ladrillo-hueco, ladrillo-prensado, bloque-de-hormigon | **Missing material: ladrillo refractario** (880, real bid, parrilla/horno demand). `ladrillo visto` (1.300) is bigger than `ladrillo prensado` (390): it is already a synonym, lead the intro with it. `ladrillo sapo` → verify, likely a hueco synonym. |
| Adoquines | ~1.300 | adoquines 1.000 · adoquinado 1.000 (same group) · adoquines de cemento 170 · adopasto 110 | ladrillos-y-bloques **A** (or aridos) | **Missing material: adoquines** (pavers, incl. adopasto). |

### 1.2 Clusters that map to PRÓXIMA categories (argument for promoting them, in this order)

| Cluster | Dedup vol | Head terms | Maps to | Why promote |
|---|---|---|---|---|
| Pisos y revestimientos | ~12.000 | cerámica 1.600 · porcelanato 1.600 · 12,18 · pisos porcelanato 1.300 · 5,93 · azulejos para baño 1.000 · 19,40 · revestimientos para pared 880 · piso vinílico 880 · 4,81 · azulejos 720 · azulejos para cocina 720 · vinílicos adhesivos 720 · piso para baño 480 · baldosa piso 480 · piso parquet 480 · piso flotante 320 · 5,58 · piedra laja 320 · revestimiento de piedra 320 · pisos de cerámica 320 · piso spc 170 | pisos-y-revestimientos **P** | Largest commercial cluster not yet live. Needs materials: cerámica-para-piso, porcelanato, azulejos, piso-vinílico (incl. SPC/flotante), revestimiento-de-piedra (laja). `pegamento para cerámica/porcelanato` already lives in cemento-y-cal → adhesivo-para-ceramica. |
| Aberturas | ~9.000 | puertas de metal 1.300 · puertas de madera 1.000 · **25,81** · puertas 1.000 · 14,36 · ventanas 880 · 14,79 · ventanas de blindex 880 · 10,24 · vidrio 880 · 15,51 · aluminio 590 · 9,91 · vidrio templado 590 · 11,77 · puerta placa 480 · portón basculante 480 · puertas metálicas 320 · 14,36 · aberturas 320 · 9,41 · ventanas de madera 260 · **18,72** · puertas de PVC 260 · 13,22 · ventanas de aluminio 210 · carpintería de aluminio 170 · 11,43 | aberturas **P** | Highest bids in the whole file outside the artefacts. Materials: puerta-placa, puertas-de-madera, puertas-de-chapa/metal, ventanas-de-aluminio, vidrio-templado (blindex), portones. Rejas/portones a medida shade into herrería (Profesionales). |
| Impermeabilizantes | ~5.000 | membrana para techo 1.300 · 5,78 · pintura antihumedad 880 · 4,94 · membrana 880 · impermeabilizante 590 · 5,93 · membrana líquida 590 · 4,20 · membranas asfálticas 390 · 8,38 · recuplast techos 320 · pintura impermeabilizante 260 · impermeabilizante para techos 210 · 6,48 | impermeabilizantes **P** | Clean transactional intent, consistent bids, no noise. Materials: membrana-asfaltica, membrana-liquida, pintura-antihumedad, hidrofugo. Cheap category to launch (4 pages). |
| Yeso y durlock | ~3.700 | durlock 1.900 · 3,88 · yeso 880 · yeso para pared 390 · cielorraso durlock 260 · placas de yeso 170 · techo durlock 110 · soleras para durlock 90 | yeso-y-durlock **P** | One dominant head term. Materials: placa-de-yeso (durlock), yeso-en-polvo, perfiles-montante-solera. |
| Tanque de agua (plomería) | ~2.700 | tanque de agua 1.300 · 15,03 · tanque de agua 1000 litros 720 · syopar tanques 320 · 16,01 · tanque de agua 500 litros 210 · tanque de 1000 litros 110 · **33,15** · 1000 litros precio 140 · syopar 1000 litros precio 140 | canos-y-plomeria **P** | **Missing material: tanque-de-agua** (category intro_keywords already name it, no material exists). Syopar is the local brand; sizes (500/1000 l) are the modifiers. |
| Caños y PVC | ~1.700 | caño 480 · **24,93** · caño conduit 480 · caños pvc 210 · tubos pvc 140 (×4 variants) · codos 30 | canos-y-plomeria **P** (conduit → electricidad) | Modest volume, high bid. Materials: cano-de-pvc (desagüe), cano-de-agua. |
| Madera | ~6.500 | madera 1.000 · terciado 1.000 · machimbre 880 · MDF 590 (3 variants) · fibrofácil 590 · curupay 480 · madera terciada 480 · tirantes 390 · vigas de madera 260 · tablas de madera 260 · precio terciado 170 · listones 140 | madera **P** | Materials already planned cover terciada, machimbre, tirantes, listones. **Missing: MDF/fibrofácil** (~1.100) and **madera dura paraguaya** (curupay 480; check lapacho, eucalipto, pino). `melamina` 1.300 + `mueble de melamina` 320 is mueblería, borderline. |
| Pinturas | ~15.000 | pintura para pared 2.900 · 1,81 · pintura 2.400 · 4,85 · pintura para piso 1.600 · 3,38 · acrílico 1.300 · pintura acrílica 1.000 · colores de pintura para casa 1.000 · barniz para madera 880 · pintura antihumedad 880 · taka pinturas 880 · 5,20 · sellador para pared 880 · 4,34 · barniz 720 (4 variants) · pintura para madera 590 · pintura para pisos de cemento 480 · 6,22 · pintura sintética 390 · pintura corona 390 | pinturas **P** | Biggest raw cluster but lowest bids (retail DIY, buys at a pinturería, not via a quote). Promote last among the P categories. Materials: latex-interior, latex-exterior, pintura-para-piso, barniz, esmalte-sintetico, sellador-fijador. |
| Electricidad | ~2.000 | cinta aisladora 880 · caño conduit 480 · canaleta para cable 390 (+ cable canaleta 320, canaletas para cables 70) · balastro 140 | electricidad **P** | Most electrical demand went to the Productos bucket. Not enough here to prioritize. |

### 1.3 Clusters with NO page anywhere (candidate new materials / categories)

| Candidate | Dedup vol | Head terms | Put it under | Note |
|---|---|---|---|---|
| **cielorraso-de-pvc** | ~2.900 | pvc para techos 1.300 · 11,49 · cielorraso de pvc 1.000 · 7,73 · cielo raso pvc 390 · 3,94 · cielorrasos 320 · pvc techo 210 · techo de pvc 170 · pvc para techos precios 140 · machimbre de pvc 110 | chapas-y-techos | Biggest true gap in an active category. One page; `cielorraso durlock` links to yeso-y-durlock. |
| **policarbonato** | ~1.200 | techos de policarbonato 590 · 13,85 · policarbonato techo 390 · 14,27 · claraboyas 170 · policarbonato alveolar 50 · compacto 30 · 9,37 · 6/8/10 mm variants | chapas-y-techos | Strong bids for the volume. |
| **canaletas (desagüe pluvial)** | ~2.000 (excl. `canaleta 4`) | canaletas 590 · canaleta embutida 590 (×2) · canaletas de PVC 260 (×2) · canaletas para techos 110 · canaletas para techos de chapa 40–50 · precio de canaleta por metro 40 | chapas-y-techos | `canaleta 4` at 3.600 with no bid is unexplained (see §3) — do not size the page on it. |
| **tejido-de-alambre / cercos** | ~2.000 | tejido de alambre 1.300 · alambre de púas 480 · alambre galvanizado 210 · alambre tejido 90 · malla para cercar 10 | hierro | Paraguayan term is `tejido` (romboidal). Poste de hormigón for alambrado probably has volume too (§4). |
| **ladrillo-refractario** | ~900 | ladrillo refractario 880 · 7,06 | ladrillos-y-bloques | Parrilla/horno culture; real bid. |
| **adoquines** | ~1.300 | adoquines 1.000 · adoquines de cemento 170 · adopasto 110 | ladrillos-y-bloques | |
| **tanque-de-agua** | ~2.700 | see §1.2 | canos-y-plomeria | Can go live before the rest of the category if the router allows a material under a `proxima` category. |
| **selladores-y-siliconas** | ~2.800 | silicona 1.000 (3 variants) · sikaflex 590 · silicona fría 590 · silicona líquida 390 · sikacryl 260 · silicona para vidrio 260 · silicona transparente 140 | impermeabilizantes or a small "Adhesivos y selladores" category | Absorbs the Sika long-tail (§3). CONTENT-SPEC forbids brand names in prose → policy decision needed to rank for `sikaflex`. |
| **aditivos-para-hormigon** | ~700 | sikadur 31/32 380 · sikagrout 170 · sika 1 70 · hidrófugo sika 90 · sikalatex 30 | cemento-y-cal | Same brand-policy issue. |
| **piedra-laja / revestimiento de piedra** | ~1.200 | piedra laja 320 (×2) · revestimiento de piedra 320 · piedra de revestimiento 320 · muros de piedra 170 · símil piedra 140 · piedra para fachada ~80 | pisos-y-revestimientos | |
| **mdf-fibrofacil** | ~1.100 | material mdf 590 · fibrofácil 590 · madera mdf 480 · plywood 140 | madera | |
| **Sanitarios y grifería** (category) | ~9.000 | inodoro 1.900 · canillas para cocina 1.000 · cisternas de baño 1.000 · sanitarios 880 · ducha higiénica 590 · 1,80 · llave para ducha 480 · canilla para lavatorio 390 · grifería 260 · inodoro deca 260 · canilla eléctrica 170 · monocomando 170 · canillas FV 110 · inodoro inteligente 90 · 5,65 | **NEW category** — or reassign to the Productos bucket | Second-largest gap. Almost no bids (retail showroom purchase). The classifier put it in Materiales because of "baño". Decide whether the quote pipeline can route bathroom fit-outs; if not, move the whole cluster to Productos. |
| **Rejas y portones** | ~3.000 | portones de hierro 720 · rejas para ventanas 590 · rejas 480 · rejas para frentes 480 · portón basculante 480 · rejas y portones 260 · portones 170 · 32,74 · portones corredizos 170 · portones eléctricos 90 | aberturas, or Profesionales (herrero) | Made-to-measure herrería; the searcher wants a fabricator. Suggest a single aberturas page `portones-y-rejas` that captures the lead and routes to a herrero. |
| **Herrajes y cerraduras** | ~4.500 | cerraduras para puertas 1.300 · bisagra 1.000 · cerraduras 880 · bisagras para puertas 880 · tornillo autoperforante 390 · cerradura electrónica 260 · manija 260 · topes 170 | **OUT → Productos** | Misclassified; hardware-store SKUs. The 76,59 bids are one advertiser. |

---

## 2. Top 40 by combined volume × bid value

Score = volume × ln(1 + bid_high), bid capped at 20 kr to neutralise the two outliers,
cluster-median bid imputed where Keyword Planner shows none (marked *imp*). Variant
spellings collapsed; noise removed (see §3). "Page" is where the demand should land;
"Write" is the recommended order for phases 5–6 given that only 5 categories are active.

| # | Keyword group (dedup) | Vol | Bid high | Page | Status | Write |
|---|---|---|---|---|---|---|
| 1 | chapa termoacústica (+ techo termoacústico 480, chapa sandwich 720, aislante térmico 390) | 3.600 | 7,58 | chapa-termoacustica | A | **1** |
| 2 | perfiles (+ perfiles C/U/IPN/UPN/hierro ~1.400) | 4.400 | 19,15 | perfiles-metalicos | A | **2** |
| 3 | chapa trapezoidal (+ medidas 110, precio 70) | 1.600 | 11,01 | chapa-trapezoidal | A | **3** |
| 4 | porcelanato (+ pisos porcelanato 1.300, para cocina 260, símil madera 170) | 1.600 | 12,18 | porcelanato | P (pisos) | promote 1st |
| 5 | tanque de agua (+ 1000 l 720, syopar 320, 500 l 210) | 1.300 | 15,03 | tanque-de-agua | NEW | new material |
| 6 | pvc para techos + cielorraso de pvc | 1.300 + 1.000 | 11,49 | cielorraso-de-pvc | NEW | new material |
| 7 | cemento (+ precio 320, blanco 320, gris 320) | 1.000 | 19,15 | cemento | A | **4** |
| 8 | puertas de madera (+ puerta placa 480, principales 110) | 1.000 | 25,81 | puertas-de-madera | P (aberturas) | promote 2nd |
| 9 | azulejos para baño (+ azulejos 720, cocina 720) | 1.000 | 19,40 | azulejos | P (pisos) | promote 1st |
| 10 | cerámica / cerámicos (+ pisos de cerámica 320, para piso 140) | 1.600 | *imp* | ceramica-para-piso | P (pisos) | promote 1st |
| 11 | zinc + chapa zinc + chapas de zinc | 1.600 + 880 | 6,56 | chapa-de-zinc | A | **5** |
| 12 | hierro (+ varillas 480, varilla de 8 320, varillas de hierro 260) | 1.600 | 4,79 | varilla-de-hierro / hierro | A | **6** |
| 13 | chapa (+ chapas para techo 260, chapa acanalada 480) | 1.300 | 8,60 | chapas-y-techos (category) | A | **7** |
| 14 | tejido de alambre (+ alambre de púas 480) | 1.300 | 8,97 | tejido-de-alambre | NEW | new material |
| 15 | pintura + pintura para pared | 2.400 + 2.900 | 4,85 / 1,81 | pinturas (category) | P | promote last |
| 16 | durlock (+ yeso 880, placas de yeso 170) | 1.900 | 3,88 | placa-de-yeso | P (yeso) | promote 4th |
| 17 | hormigón (+ hormigón elaborado 70, concreto 320) | 880 | 19,15 | hormigon-elaborado | A | **8** |
| 18 | ladrillo visto + ladrillo prensado | 1.300 + 390 | *imp* | ladrillo-prensado | A | **9** |
| 19 | ladrillo hueco (+ medida 170, precio 110, por m2 90) | 1.000 | *imp* | ladrillo-hueco | A | **10** |
| 20 | puertas de metal + puertas metálicas + puertas | 1.300 + 320 + 1.000 | 14,36 | puertas-de-chapa | P (aberturas) | promote 2nd |
| 21 | vidrio + vidrio templado + ventanas de blindex | 880 + 590 + 880 | 15,51 | vidrio-templado | P (aberturas) | promote 2nd |
| 22 | ventanas (+ de madera 260 · 18,72, de aluminio 210) | 880 | 14,79 | ventanas-de-aluminio | P (aberturas) | promote 2nd |
| 23 | membrana para techo + membrana + membranas asfálticas | 1.300 + 880 + 390 | 5,78 / 8,38 | membrana-asfaltica | P (imperm.) | promote 3rd |
| 24 | techos de policarbonato + policarbonato techo | 590 + 390 | 13,85 | policarbonato | NEW | new material |
| 25 | inodoro (+ cisternas 1.000, sanitarios 880) | 1.900 | *imp* | — | NEW cat / Productos | decision |
| 26 | adhesivo + pegamento para cerámica/porcelanato | 880 + 120 | 19,21 | adhesivo-para-ceramica | A | **11** |
| 27 | chapa galvanizada + galvanizado + alambre galvanizado | 390 + 260 + 210 | 30,83 / 42,82 | chapa-de-zinc (synonym) | A | fold into 5 |
| 28 | arena (+ arena fina 110, gruesa 40) | 880 | 5,01 | arena-lavada | A | **12** |
| 29 | terciado + madera terciada + precio terciado | 1.000 + 480 + 170 | *imp* | terciada | P (madera) | promote 5th |
| 30 | cal (+ cal hidratada 320) | 1.600 | *imp* | cemento-y-cal (category) + cal-hidratada | A | **13** |
| 31 | pintura para piso (+ pisos de cemento 480 · 6,22) | 1.600 | 3,38 | pintura-para-piso | P (pinturas) | promote last |
| 32 | ladrillo refractario | 880 | 7,06 | ladrillo-refractario | NEW | new material |
| 33 | pintura antihumedad + antihumedad + impermeabilizante | 880 + 210 + 590 | 4,94 / 5,93 | pintura-antihumedad / impermeabilizante | P (imperm.) | promote 3rd |
| 34 | caño + caños pvc + tubos pvc | 480 + 210 + 400 | 24,93 | cano-de-pvc | P (plomería) | promote 4th |
| 35 | adoquines + adoquinado | 1.000 | *imp* | adoquines | NEW | new material |
| 36 | silicona + silicona fría + sikaflex | 1.000 + 590 + 590 | *imp* | selladores-y-siliconas | NEW | policy decision |
| 37 | malla electrosoldada (4 spellings, + precio 30) | 590 | *imp* | malla-electrosoldada | A | **14** |
| 38 | encofrado (+ tablas 480?) | 320 | 14,16 | tabla-de-encofrado / puntales | P (madera) | promote 5th |
| 39 | piso vinílico + vinílicos adhesivos + piso flotante + SPC | 880 + 720 + 320 + 170 | 4,81–5,58 | piso-vinilico | P (pisos) | promote 1st |
| 40 | canaletas + canaleta embutida + canaletas de PVC | 590 + 590 + 260 | *imp* | canaletas | NEW | new material |
| 41 | ripio + canto rodado + piedra triturada | 590 + 1.000 + 260 | *imp* | ripio, piedra-triturada | A | **15** |
| 42 | machimbre (+ de madera 390) | 880 | *imp* | machimbre | P (madera) | promote 5th |
| 43 | aluminio + perfiles de aluminio + carpintería de aluminio | 590 + 480 + 170 | 9,91–11,43 | ventanas-de-aluminio | P (aberturas) | promote 2nd |
| 44 | ladrillo común (+ medidas 210, precio 170) | 390 | *imp* | ladrillo-comun | A | **16** |

**Write-first list for phase 5 (active categories only), in order:** chapa-termoacustica →
perfiles-metalicos → chapa-trapezoidal → cemento → chapa-de-zinc → varilla-de-hierro →
chapas-y-techos (category) → hormigon-elaborado → ladrillo-prensado → ladrillo-hueco →
adhesivo-para-ceramica → arena-lavada → cemento-y-cal (category, owns "cal") →
malla-electrosoldada → ripio + piedra-triturada → ladrillo-comun. Then the guide
`que-chapa-conviene-para-techo` (it sits under the three biggest material terms).

**Promotion order for próxima categories, by money on the table:** pisos-y-revestimientos
(~12k, bids 5–19) → aberturas (~9k, bids 10–26) → impermeabilizantes (~5k, clean intent,
4 pages) → yeso-y-durlock + canos-y-plomeria with tanque-de-agua (~6k combined) → madera
(~6.5k) → pinturas (~15k volume, bids under 5, DIY intent) → electricidad (thin here).

**New materials to add to active categories now (no category change needed):**
cielorraso-de-pvc, policarbonato, canaletas (chapas-y-techos); tejido-de-alambre (hierro);
ladrillo-refractario, adoquines (ladrillos-y-bloques). Together ≈ 10k dedup volume.

---

## 3. Noise and misclassification

### 3.1 Not construction materials at all (drop)

| Keyword | Vol | What it actually is |
|---|---|---|
| adobe | 3.600 | Adobe software (Acrobat/Photoshop). Bid 0,48–3,12 confirms software-support intent. Adobe bricks in PY are real but this number is not them; check `ladrillo de adobe` separately. |
| canaleta 4 (+ canaleta 3, canaleta 100) | 3.600 | Unexplained. No bid, numeric suffix, sits far above every real gutter term. Most likely a cable-trunking size or a close-variant grouping artefact ("canal 4"). Run a SERP check before believing it. |
| la piedra | 1.300 | Navigational/media ("La Piedra"). `piedra` alone (880) is the material term. |
| melamina / mueble de melamina | 1.300 / 320 | Furniture boards; mueblería, not obra. Borderline — only if madera category wants it. |
| membrana celular / célula membrana | 390 ×2 | Biology homework. Classifier matched "membrana". |
| zinc 50 mg (and part of `zinc` 1.600) | 170 | Dietary supplement. `chapa zinc` 880 is the safe roofing term. |
| acero quirúrgico | 390 | Jewellery / piercings. |
| acero inoxidable / inox / 304 / láminas / tubos | ~700 | Metalworking, kitchens; bid 29,25 is industrial. Not a materiales page. |
| perfiles (partly) | 4.400 | Mixed with social-media profiles. Keep because the qualified long-tail is large. |
| columnas | 720 | Ambiguous (newspaper columns, spine). No qualified long-tail. |
| tablas / tablas de | 480 ×2 | Almost certainly `tablas de multiplicar`. `tablas de madera` 260 is the real one. |
| mortero de madera | 170 | Kitchen pestle. `mortero` 720 is partly the same. |
| tirantes hombre | 10 | Suspenders. |
| mesas de madera, repisas de madera, palets de madera, casa(s) de madera, pérgolas de madera | ~2.500 | Furniture, logistics, prefab housing, carpentry services. |
| silicona para autos, silicona caliente/en barra, tela adhesiva, pistola de silicona | ~500 | Auto care, crafts, medical, tools. |
| pintura para tela / textil / a la tiza / gabinetes de cocina / electrostática / intumescente | ~500 | Crafts and industrial coatings. |
| cal agrícola / dolomita / para plantas / para árboles, tanque australiano, malla sombra / gallinero / ganadera / antipájaros / para gatos / ocultación | ~900 | Agro and pets. Bid 14,36 on cal agrícola is an agro advertiser. |
| tanques de combustible, vidrios para carros | 180 | Automotive. |
| tosca opera, la tosca | 20 | Opera. `tosca` 210 itself is plausible sub-base material. |
| pinturería(s), cerámica cerca de mi, vidrios para ventanas… cerca de mi | ~1.100 | Store-finder / local intent; a national content site cannot rank. |
| aluminio san lorenzo, casa de la cerradura, alambre san martín, taka pinturas, pintura corona, syopar tanques, inodoro deca, canillas FV, ducha FV, recuplast techos | ~3.100 | Navigational brand/business searches. Syopar, Deca, FV, Taka are legitimate synonyms inside prose *if* the brand rule in CONTENT-SPEC is relaxed; today they are prohibited. |

### 3.2 Foreign-market leakage (10–30 vol, wrong vocabulary — drop all)

Spain: grifo, lavabo, fregadero, bañera, puertas de garaje, blindadas, acorazadas,
seccionales, correderas, cristal, uralita, velux, hormigón impreso "7 euros", cerramientos.
Chile: termopanel, malla acma, malla hércules, pizarreño, internit, piso flotante "easy",
cedral siding. Mexico: regadera, rotoplas, taza de baño, loseta, cascajo, sanitarios
portátiles. Colombia: superboard, siding de fibrocemento. Ecuador: piso flotante quito.
Argentina-only spellings: varilla 3/8 (imperial), concreto premezclado (Mex/Col).
Roughly 120 phrases, under 1.500 volume combined, but they will drag the fibrocemento,
puertas, ventanas, malla and piso flotante clusters toward the wrong words if used as
synonyms. Rule of thumb: any 10-volume phrase carrying a foreign brand or a Spain-only
noun is a Google "geo-adjacent" suggestion, not Paraguayan demand.

### 3.3 Wrong bucket (belongs in Productos or Profesionales, not Materiales)

| Group | Vol | Should be |
|---|---|---|
| cerraduras / cerradura / bisagra(s) / manija / topes / bombines / cerrojo / yale / kallay / papaiz / soprano | ~4.500 | Productos (herrajes). The identical 76,59 bid on all 20 phrases is one advertiser. |
| tornillo autoperforante, tornillos, clavos (90), varilla roscada, cinta aisladora, cinta teflón | ~2.300 | Productos (ferretería). `clavos` already has a material page; keep it. |
| autocle, sacabocado, fratacho, pistola de silicona, pistola de clavos, router para madera, hormigoneras precios | ~2.100 | Productos (herramientas). |
| techos (880 · 80–126 kr), automatización de portones, motores para portones, empresas de impermeabilización, plastificado de pisos, corte de hormigón/acero | ~1.100 | Profesionales (techista, herrero, impermeabilizador). The `techos` bid is a roofing-contractor campaign. |
| hormigón armado, encofrado, zapatas de hormigón, losas de hormigón, hormigón pulido/impreso/estampado/texturado, pavimentos | ~1.300 | Construction-service or guide intent, not a material SKU. |

### 3.4 Brand long-tails that must collapse into one page

| Family | Phrases | Vol | Collapse into |
|---|---|---|---|
| Sika | sikaflex, sikaflex 1a / 221 / pro 3 / 11fc / precio, sikacryl, sikadur 31 / 32, sikagrout, sika 1, sikalatex, sikalastic, sikasil, sikafill, sika zero salitre, sika impermeabilizante, hidrófugo sika, impermeabilizante sika | 19 phrases, ~2.000 | `selladores-y-siliconas` (sikaflex/sikacryl/sikasil) and `aditivos-para-hormigon` (sikadur/sikagrout/sika 1/sikalatex). Product codes (1a, 221, 11fc) are FAQ lines at most. |
| Fibrocemento brands | eternit, internit (5/8 mm, ranurado, plancha), superboard (8 mm, lámina), uralita, cedral, pizarreño, siding ×4, placa/placas/lámina/panel fibrocemento ×12, teja eternit/fibrocemento ×4 | 38 phrases, ~350 | `fibrocemento` (exists). Only `fibrocemento` 140 and `eternit` 140 matter. |
| Piso flotante / vinílico variants | 34 phrases at 10–50 (click, spc, autoadhesivo, en rollo, barato, precio, 8 mm, alto tránsito…) | ~600 | `piso-vinilico` with an H2 per type (vinílico en rollo, autoadhesivo, click/SPC, laminado flotante). |
| Malla electrosoldada spellings | electrosoldada, electro soldada, microsoldada, termosoldada, 6 6 10 10 (+precio), galvanizada | 7 phrases, 590 real | `malla-electrosoldada` (exists). Only synonyms. |
| Canaleta long-tail | 45 phrases; gutter (embutida, PVC, chapa galvanizada, alero, limahoya, pecho paloma, bajadas) vs cable (para cable, cable canal, ranurada, plástica) vs floor drain (para piso, de piso, ranurada) | ~4.800 incl. `canaleta 4` | Three pages max: `canaletas` (roof), electricidad (cable canal), and nothing for floor drains. |
| Policarbonato thickness | 6 / 8 / 10 mm, alveolar 4 / 6 / 8 / 10 mm, compacto | 9 phrases, ~150 | `policarbonato` with a thickness table. |
| Puertas / ventanas long-tail | ~90 phrases at 10–30 | ~800 | Ignore; mostly Spain vocabulary (§3.2). |

---

## 4. New phrases to check in Keyword Planner next

Pattern in this data: the modifiers that turn a head term into a second keyword with real
volume are **precio**, **medidas**, a **size** (1000 litros, 60x60, 8 mm, "de 8"/"de 12"),
and an **application** (para techo / pared / baño / piso / cocina). Two strong local words
appear nowhere: **precio paraguay** and city names. Check the lists below in this order.

### 4.1 Price and size modifiers on the pages we are writing first

| Check | Why |
|---|---|
| chapa termoacústica precio, chapa termoacústica precio m2, isopanel precio, isopanel paraguay | Head term 3.600; `isopanel` is the PY word and never appears in the pull. |
| chapa trapezoidal precio metro, chapa de zinc precio, chapa n° 25 / n° 26, chapa acanalada precio, cumbrera, chapa lisa, chapa negra, chapa plegada | Gauge and accessory words drive quotes. |
| precio del cemento en paraguay, bolsa de cemento precio, cemento inc precio, cemento yguazú, cemento cpi, cemento de albañilería | INC and Yguazú are the local brands; `cemento precio` already has 320. |
| hierro de 8 precio, hierro de 10 precio, hierro de 12, varilla de 12 precio, hierro precio por kilo, hierro de construcción precio paraguay, hierro acindar | `varilla de 8` 320, `varilla de 10 precio` 110 exist → other diameters will too. |
| ladrillo de 8 precio, ladrillo de 12 precio, ladrillo hueco de 12, ladrillo común precio paraguay, ladrillo por millar, bloque de cemento precio, ladrillo prensado precio | PY sells by millar; "de 8 / de 12" are already your synonyms. |
| camionada de arena precio, arena lavada precio, arena gorda, metro de arena, piedra triturada precio, piedra 6ta precio, piedra sexta, piedra bruta precio, ripio precio, camionada de tierra, tierra para relleno precio, escombro para relleno, tosca precio | Áridos sell by camionada / m³; none of those units appear in the pull. |
| cal viva precio, cal en bolsa, cal hidratada precio | To size the two existing cal pages. |
| hormigón elaborado precio m3, hormigón premezclado paraguay, mixer de hormigón, precio m3 de hormigón | `hormigón elaborado` only 70 — the buying word may be different. |
| malla sima precio, malla electrosoldada precio paraguay, alambre de atar, alambre negro precio, tejido romboidal, tejido de alambre precio, alambre de púas precio, poste de hormigón, poste de cemento para alambrado, poste de eucalipto | Cercos are a rural PY staple; `poste` never appears. |
| perfil c precio, caño estructural precio, tubo estructural, hierro ángulo, planchuela, perfil ipn precio | Confirms the perfiles-metalicos H2 split. |

### 4.2 Gaps and próxima categories

| Check | Why |
|---|---|
| cielorraso de pvc precio m2, cielo raso de pvc precio, policarbonato precio, policarbonato alveolar precio, chapa de policarbonato, canaleta de zinc, canaleta para techo precio, caño de bajada | Size the three new chapas-y-techos materials. |
| tanque de agua 1000 litros precio paraguay, tanque syopar 500 litros precio, tanque de agua tricapa, tanque de agua 2000 / 5000 litros, tanque de agua bicapa | Syopar sizes are the modifiers here. |
| caño de pvc precio, caño de 110, caño de 100 pvc, caño amanco, caño tigre, caño de termofusión, ppr, cañería de agua precio | Amanco and Tigre are the PY pipe brands. |
| porcelanato precio m2, porcelanato 60x60 precio, cerámica para piso precio, cerámica 45x45, piso cerámico precio, porcelanato paraguay, pastina, pegamento para porcelanato precio, zócalo | Pisos is the next category to promote. |
| puerta placa precio, puerta de madera precio paraguay, puerta de chapa precio, portón de chapa precio, ventana de aluminio precio, ventana de aluminio 1x1, vidrio templado precio, blindex precio | Aberturas has the best bids; confirm the precio variants exist. |
| membrana asfáltica precio rollo, membrana aluminizada, pintura asfáltica, hidrófugo precio, impermeabilizante para losa | Impermeabilizantes launch set. |
| durlock precio paraguay, placa de yeso precio, perfil montante, masilla para durlock, enduido, fondo blanco | Yeso launch set. |
| terciado 18 mm precio, terciado fenólico precio, machimbre precio, tirante de madera precio, madera curupay precio, lapacho madera, madera de eucalipto, postes de eucalipto, madera de pino precio, tabla de encofrado precio, puntal, melamina precio, mdf precio | Madera launch set; hardwood species are PY-specific. |
| ladrillo refractario precio, cemento refractario, mortero refractario, adoquín precio, adoquines para patio, adoquín ecológico, bloque de vidrio, ladrillo de vidrio, ladrillo de adobe | New ladrillos materials. |
| lana de vidrio, aislante térmico precio, poliestireno expandido, telgopor, ladrillo de telgopor, espuma de poliuretano, geomembrana, steel framing, vigueta pretensada, vigueta y bovedilla, losa pretensada, columna premoldeada | Adjacent categories with zero keywords in this file. `fibra de vidrio` 720 and `aislante térmico para techo` 390 hint that insulation has demand. |

### 4.3 Head and local terms for the home page and city pages

materiales de construcción, materiales de construcción precios, venta de materiales de
construcción, lista de precios materiales de construcción paraguay, corralón, corralón
cerca de mi, materiales de construcción asunción / luque / san lorenzo / lambaré /
capiatá / ciudad del este / encarnación, cuánto cuesta construir una casa en paraguay,
precio del metro cuadrado de construcción paraguay, presupuesto de obra, cómputo métrico.

---

## 5. SEO / IA red flags

### 5.1 Cannibalization risks

| Pages | Overlap | What to do |
|---|---|---|
| chapa-de-zinc vs chapa-trapezoidal vs chapa-termoacustica vs chapas-y-techos | `zinc`, `galvanizada`, `trapezoidal`, `acanalada` all describe overlapping sheets; `chapa zinc trapezoidal` (40) literally straddles two pages. | Fix the angle per page: zinc = acanalada/sinusoidal/galvanizada; trapezoidal = perfil T101 for galpones; termoacústica = con aislación / isopanel. Bare `chapa`, `chapas para techo`, `techos de chapa` belong to the **category** page, never to a material. |
| cemento-y-cal (category) vs cal-viva vs cal-hidratada | Three pages can chase bare `cal` (1.600). | Category page owns `cal` + `cal para construcción`; cal-hidratada owns `cal hidratada`/`cal apagada`/`cal para revoque`; cal-viva owns `cal viva`/`cal en terrón`. |
| arena-lavada vs arena-gorda vs aridos | Bare `arena` (880). | arena-lavada owns `arena` + `arena fina`; arena-gorda owns `arena gruesa`; category owns `arena y ripio`, `áridos`, `camionada`. |
| ripio vs piedra-triturada | `canto rodado` 1.000, `gravas` 320, `piedra triturada` 260 — the biggest term is a synonym of the smaller page. | Lead the ripio intro with "canto rodado". Keep triturada on `piedra 4ta/5ta/6ta` and `basalto`. |
| ladrillo-prensado vs ladrillo-comun | `ladrillo visto` 1.300 vs `prensado` 390; `ladrillo rústico` and `ladrillo de campo` sit between them. | ladrillo-prensado's first sentence must say "ladrillo visto"; ladrillo-comun owns `rústico`, `de campo`, `macizo`. |
| cielorraso-de-pvc vs a future policarbonato vs yeso-y-durlock | `pvc para techos`, `techo de pvc`, `cielo raso pvc`, `cielorraso durlock`, `techo durlock`. | One PVC page; `cielorraso durlock` is an H2 on placa-de-yeso that cross-links. |
| canaletas (roof) vs electricidad (cable) | Same word, two products. | The roof page targets `canaletas para techo` / `desagüe pluvial`, not bare `canaleta`. |
| hormigon-elaborado vs cemento vs guide | `hormigón` 880, `concreto` 320, `mortero` 720, `hormigón armado` 320. | hormigon-elaborado owns `hormigón`/`concreto`/`premezclado`; `mortero` is an H2 on cemento; `hormigón armado` goes to the guide (§5.3). |
| perfiles-metalicos vs a future aberturas page | `perfiles de aluminio` 480, `aluminio` 590. | Aluminium profiles go to aberturas, not hierro. |
| Four spellings each of malla electrosoldada, barniz, revocado, cerámica | Google already groups them. | Synonyms only, as the spec says. Do not create spelling variants as H2s. |

### 5.2 Intent mismatches

| Signal | Vol | Actual intent | Page type |
|---|---|---|---|
| `[x] precio` phrases (cemento precio, chapa trapezoidal precio, tanque 1000 litros precio, ladrillos huecos precio, varilla de 10 precio, cal precio, pvc para techos precios, inodoros precios, puertas de madera precios…) | ~2.500 | Wants a number. CONTENT-SPEC §0 forbids publishing prices. | Keep the rule, but every material page needs a visible FAQ `¿Cuánto cuesta [material]?` that answers with the factors (unidad, cantidad, flete, zona) and the CTA. Without it these searchers bounce. |
| `[x] medidas` (chapa trapezoidal medidas 110, ladrillo común medidas 210, medida de ladrillo hueco 90/170, medidas de chapa trapezoidal 50) | ~700 | Informational, table-shaped. | Spec H2 `Qué mirar antes de comprar` should carry a measures table; the ladrillo guide already exists. |
| colores de pintura para casa 1.000, colores de pintura 390, colores de pintura para pared 260, colores de barniz 140, barniz color cedro 140 | ~2.000 | Inspiration. | Guide/gallery, not a category. Low bid (none). |
| revoque 320 + revocado/revoco de pared(es) 320×4 + pared revocada 140 | ~1.800 | How-to ("cómo revocar"). | A new guide `como-revocar-una-pared` linking cal-hidratada, arena-lavada, cemento. |
| hormigón armado 320, encofrado 320, zapatas 170, losas 110, columnas 720, vigas 170 | ~1.900 | Construction knowledge. | Existing guide `que-diametro-de-hierro-para-que-uso` can absorb `hormigón armado`; a second guide on losa/encofrado would link hormigon-elaborado, tabla-de-encofrado, puntales. |
| ladrillo hueco por m2 90, tanque de agua 1000 litros 720, cuántos… | — | Calculation. | Calculators / existing "cuántas bolsas" guide pattern. |
| tipo de ventanas 140, diseños de puertas de madera 90, modelos de portones 90, catálogo rejas 110, rejas modernas 2022 90 | ~600 | Browsing / catalogue. | Only worth it once aberturas exists; then an image-heavy category page. |
| techos 880 (80–126 kr), empresas de impermeabilización, automatización de portones | ~1.000 | Hire someone. | Profesionales bucket. |
| pinturería 880, cerámica cerca de mi 170, aluminio san lorenzo 590 | ~1.700 | Find a store. | Not rankable for a content site; skip. |
| `chapa galvanizada` 30,83 and `galvanizado` 42,82 bids | 650 | Bids far above other chapa terms suggest industrial galvanizing / wholesale advertisers, not roofing buyers. | Treat as synonyms on chapa-de-zinc; do not build a page. |

### 5.3 Page-type surprises

- **`chapa termoacústica` is the real head of chapas-y-techos**, not `chapa de zinc`. The
  category grid order and the guide `que-chapa-conviene-para-techo` should lead with it.
- **`perfiles` (4.400) is bigger than `hierro` (1.600).** The hierro category currently reads
  as varillas-first; perfiles-metalicos deserves the same prominence and its own H2 per
  profile type.
- **`durlock` is a brand used as a generic** (1.900 vs `placas de yeso` 170). The yeso
  page cannot rank without saying "durlock" — the brand-name prohibition in CONTENT-SPEC
  needs an explicit exception list (durlock, isopanel, blindex, eternit, syopar, sikaflex
  are all brand-as-generic in Paraguay).
- **`tanque de agua` outranks every plumbing pipe term combined**; it should launch before
  the rest of canos-y-plomeria.
- **Sanitarios y grifería (~9k) is the largest cluster with no home** and no clear owner
  between the Materiales and Productos buckets. Decide before phase 5 whether the quote
  pipeline accepts bathroom fit-outs.
- **Long-tail depth is a lie in three clusters.** Fibrocemento (42 phrases), piso flotante
  (34) and canaleta (45) look deep but are 10-volume foreign suggestions; phrase count is
  not a prioritization signal in this file.
