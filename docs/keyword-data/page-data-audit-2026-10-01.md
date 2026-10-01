# Datos detrás de cada página de material (auditoría 2026-10-01)

93 páginas de material. Para cada una: el término de la página (`keyword`), la frase medida que
la respalda, las búsquedas por mes en Paraguay, de qué pull salió y si es sólido (100 o más),
débil (menos de 100) o sin número.

**Resumen: 86 sólidas · 4 débiles · 3 sin número.**

Cómo se hizo: se cruzó cada `keyword` y cada título con los tres pulls guardados (pull 1:
`KEYWORDS-MATERIALES.md`; pull 2: `materiales.com.py-keywords-for-ai-2026-10-01.md`, sólo el top
300; pull 3: `pull-2026-10-01-title-check.md`). Cuando Keyword Planner agrupa variantes (plural,
con o sin tilde) cuenta como la misma frase. **No** se cruzó contra el CSV completo (no está en el
repo), así que una frase por debajo del corte del top 300 puede figurar como "sin número" o
"débil" aunque tenga cifra en el CSV. No incluye categorías, guías ni calculadoras. Un número no
dice qué intención tiene la búsqueda (ver KNOWN-ISSUES #34).

| Página | `keyword` | Frase medida | Búsquedas/mes | Fuente | Estado |
|---|---|---|---|---|---|
| `varilla-de-hierro` | varilla de hierro | varilla de hierro | 260 | pull 1 | sólido |
| `malla-electrosoldada` | malla electrosoldada | malla electrosoldada (4 grafías) | 590 | pull 1 | sólido |
| `alambre-negro` | alambre negro | solo "alambre dulce" 390 (sinónimo local); "alambre negro" no vino en el pegado | — | — | sin número |
| `clavos` | clavos para construcción | Clavos | 90 | pull 1 | débil |
| `perfiles-metalicos` | perfiles metálicos | perfiles (long tail calificada ~2.300) | 4.400 | pull 1 | sólido |
| `tejido-de-alambre` | tejido de alambre | tejido de alambre | 1.300 | pull 1 | sólido |
| `metal-desplegado` | metal desplegado | metal desplegado | 320 | pull 1 | sólido |
| `cemento` | cemento | cemento | 1.000 | pull 1 | sólido |
| `cal-viva` | cal viva | cal viva | 210 | pull 3 (título) | sólido |
| `cal-hidratada` | cal hidratada | cal hidratada | 320 | pull 1 | sólido |
| `hormigon-elaborado` | hormigón elaborado | hormigón elaborado | 70 | pull 1 | débil |
| `adhesivo-para-ceramica` | adhesivo para cerámica | adhesivo / pegamento para cerámica | 880 | pull 1 | sólido |
| `arena-lavada` | arena lavada | arena | 880 | pull 1 | sólido |
| `arena-gorda` | arena gorda | arena gorda | 260 | pull 3 (título) | sólido |
| `ripio` | canto rodado | canto rodado | 1.000 | pull 1 | sólido |
| `piedra-triturada` | piedra triturada | piedra triturada | 260 | pull 1 | sólido |
| `piedra-bruta` | piedra bruta | piedra bruta | 260 | pull 3 (título) | sólido |
| `tierra-gorda` | tierra colorada | tierra colorada | 1.300 | pull 1 | sólido |
| `escombro-relleno` | escombro | escombro | 260 | pull 3 (título) | sólido |
| `ladrillo-comun` | ladrillo común | ladrillo común | 390 | pull 1 | sólido |
| `ladrillo-hueco` | ladrillo hueco | ladrillo hueco | 1.000 | pull 1 | sólido |
| `ladrillo-prensado` | ladrillo visto | ladrillo visto | 1.300 | pull 1 | sólido |
| `bloque-de-hormigon` | bloque de hormigón | bloques de cemento | 170 | pull 1 | sólido |
| `tejuelon` | tejuelones | tejuelones | 480 | pull 2 | sólido |
| `ladrillo-sapo` | ladrillo sapo | ladrillo sapo | 260 | pull 1 | sólido |
| `ladrillo-refractario` | ladrillo refractario | ladrillo refractario | 880 | pull 1 | sólido |
| `adoquines` | adoquines | adoquines | 1.000 | pull 1 | sólido |
| `chapa-de-zinc` | chapa ondulada | chapa ondulada | 320 | pull 2 | sólido |
| `chapa-trapezoidal` | chapa trapezoidal | chapa trapezoidal | 1.600 | pull 1 | sólido |
| `chapa-termoacustica` | chapa termoacústica | chapa termoacústica | 3.600 | pull 1 | sólido |
| `teja-espanola` | teja española | teja española | 170 | pull 1 | sólido |
| `teja-francesa` | teja francesa | teja francesa | 320 | pull 1 | sólido |
| `fibrocemento` | chapa de fibrocemento | fibrocemento / eternit | 140 | pull 1 | sólido |
| `cielorraso-de-pvc` | cielorraso de PVC | cielorraso de PVC | 1.000 | pull 1 | sólido |
| `policarbonato` | techos de policarbonato | techos de policarbonato | 590 | pull 1 | sólido |
| `canaletas` | canaletas para techo | canaletas para techo | 110 | pull 1 | sólido |
| `tirantes` | tirantes de madera | Tirantes | 390 | pull 1 | sólido |
| `puntales` | puntales de madera | Puntales | 140 | pull 3 (título) | sólido |
| `tabla-de-encofrado` | tabla de encofrado | solo "encofrado" 320; "tabla encofrado" no vino en el pegado | — | — | sin número |
| `terciada` | terciado | terciado | 1.000 | pull 1 | sólido |
| `machimbre` | machimbre | machimbre | 880 | pull 1 | sólido |
| `listones` | listones de madera | Listones | 140 | pull 1 | sólido |
| `mdf-fibrofacil` | MDF | mdf | 590 | pull 1 | sólido |
| `madera-dura` | curupay | curupay | 480 | pull 1 | sólido |
| `ceramica-para-piso` | cerámica para piso | cerámica para piso | 140 | pull 1 | sólido |
| `porcelanato` | porcelanato | porcelanato | 1.600 | pull 1 | sólido |
| `azulejos` | azulejos para baño | azulejos para baño | 1.000 | pull 1 | sólido |
| `piso-vinilico` | piso vinílico | piso vinílico | 880 | pull 1 | sólido |
| `piedra-laja` | piedra laja | piedra laja | 320 | pull 1 | sólido |
| `piso-parquet` | piso parquet | piso parquet | 480 | pull 1 | sólido |
| `puerta-placa` | puerta placa | puerta placa | 480 | pull 1 | sólido |
| `puertas-de-madera` | puertas de madera | puertas de madera | 1.000 | pull 1 | sólido |
| `puertas-de-chapa` | puertas de metal | puertas de metal | 1.300 | pull 1 | sólido |
| `ventanas-de-aluminio` | ventanas de aluminio | ventanas de aluminio | 210 | pull 1 | sólido |
| `vidrio-templado` | vidrio templado | vidrio templado | 590 | pull 1 | sólido |
| `portones-y-rejas` | portones de hierro | portones de hierro | 720 | pull 1 | sólido |
| `membrana-asfaltica` | membrana para techo | membrana para techo | 1.300 | pull 1 | sólido |
| `membrana-liquida` | membrana líquida | membrana líquida | 590 | pull 1 | sólido |
| `pintura-antihumedad` | pintura antihumedad | pintura antihumedad | 880 | pull 1 | sólido |
| `hidrofugo` | hidrófugo | hidrófugo | 390 | pull 3 (título) | sólido |
| `selladores-y-siliconas` | silicona | silicona | 1.000 | pull 1 | sólido |
| `placa-de-yeso` | placa de yeso | placa de yeso | 170 | pull 1 | sólido |
| `yeso-en-polvo` | yeso | yeso | 880 | pull 1 | sólido |
| `perfiles-para-durlock` | perfiles para durlock | soleras para durlock 90, perfil montante 30, solera durlock 20, perfiles durlock 10 | — | — | sin número |
| `tanque-de-agua` | tanque de agua | tanque de agua | 1.300 | pull 1 | sólido |
| `cano-de-pvc` | caños de PVC | caños pvc | 210 | pull 1 | sólido |
| `cano-de-agua` | caño de agua | caño de agua | 90 | pull 3 (título) | débil |
| `inodoro` | inodoro | inodoro | 1.900 | pull 1 | sólido |
| `griferia-de-cocina` | grifería de cocina | grifería | 260 | pull 1 | sólido |
| `llave-de-ducha` | llave de ducha | llave para ducha | 480 | pull 1 | sólido |
| `canilla-de-lavatorio` | canilla de lavatorio | canilla para lavatorio | 390 | pull 1 | sólido |
| `ducha-electrica` | ducha eléctrica | ducha eléctrica | 1.000 | pull 3 (título) | sólido |
| `ducha-higienica` | ducha higiénica | ducha higiénica | 590 | pull 1 | sólido |
| `pintura-para-pared` | pintura para pared | pintura para pared | 2.900 | pull 1 | sólido |
| `pintura-para-piso` | pintura para piso | pintura para piso | 1.600 | pull 1 | sólido |
| `barniz` | barniz para madera | barniz para madera | 880 | pull 1 | sólido |
| `esmalte-sintetico` | pintura sintética | pintura sintética | 390 | pull 1 | sólido |
| `sellador-para-pared` | sellador para pared | sellador para pared | 880 | pull 1 | sólido |
| `bomba-de-agua` | bomba de agua | bomba de agua | 1.600 | pull 2 | sólido |
| `presurizador-de-agua` | presurizador de agua | presurizador de agua | 1.000 | pull 2 | sólido |
| `llave-de-paso` | llave de paso | llave de paso | 390 | pull 2 | sólido |
| `camara-septica` | cámara séptica | cámara séptica | 720 | pull 2 | sólido |
| `bacha-de-cocina` | bacha de cocina | bacha de cocina | 880 | pull 2 | sólido |
| `termotanque-y-calefon` | termotanque | termotanque | 390 | pull 2 | sólido |
| `pintura-epoxi` | pintura epoxi | pintura epoxi | 390 | pull 2 | sólido |
| `tablero-electrico` | tablero eléctrico | tablero eléctrico | 480 | pull 2 | sólido |
| `cano-conduit` | caño conduit | caño conduit | 480 | pull 1 (caño conduit 480, archivo anterior) | sólido |
| `microcemento` | microcemento | microcemento | 390 | pull 2 | sólido |
| `cano-galvanizado` | caño galvanizado | caño galvanizado | 480 | pull 2 | sólido |
| `tornillos-para-chapa` | tornillo para chapa | tornillo para chapa | 210 | pull 2 | sólido |
| `claraboyas` | claraboya | claraboya | 170 | pull 2 | sólido |
| `babeta-y-limahoya` | babeta | babeta | 320 | pull 2 | sólido |
| `cable-electrico` | cable eléctrico | cable eléctrico | 70 | pull 2 | débil |
