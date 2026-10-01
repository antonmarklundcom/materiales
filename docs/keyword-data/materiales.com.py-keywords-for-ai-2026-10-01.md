# Keyword data for AI — materiales.com.py

## Prompt (already pasted in the chat)

Improve the SEO of this site using my Google Keyword Planner data for materiales.com.py (location: Paraguay, language: Español). The data is in the attached file materiales.com.py-keywords-for-ai.md. It has these parts, read them in order:

1. Summary — totals, places, brands, top distinct searches.
2. Meaning groups — every group of phrases that mean the same thing, with searches/mo.
3. Group details — the top 42 groups in full (every phrase, volume, CPC). Use these when you plan and write each page.

Rules for the data:
- Totals are deduplicated: "curriculum vitae (12,100) = cv, cv vitae" is ONE search of 12,100. Use the first phrase as the main keyword and the others as variants on the same page.
- Never plan pages targeting competitor or brand names (none listed).
- One meaning group = one page or one section. Don't make separate pages for variants.

Step 1 (plan only, no code): list the site's pages (URL, title, H1); map the top ~40 groups by searches/mo to an existing page or NEW; give a table: group | searches/mo | page or NEW | main keyword | 3-5 variants | changes (title, H1, meta description, headings, FAQ, internal links). Quick wins first: existing pages whose title or H1 misses their main keyword. Place pages only where Places in summary.md shows real volume. Then stop and wait for my OK.

Step 2 (after my OK): implement in priority order, reading each page's group in Group details first. Natural Español copy, no keyword stuffing, internal links between related groups. Verify the site builds and runs before you report.

---

# Part 1 — Summary

- Country / location of the data: **Paraguay** · language: Español
- Updated 2026-10-01 01:51 UTC by Keyword Library. Source: Google Keyword Planner exports in `raw/`.

## How to use this (for AI assistants)

- This file is the summary. The full data is `keywords.csv` in this folder: 15088 unique phrases, columns `phrase, monthly_searches, low_cpc, high_cpc`, sorted by monthly searches. CPC currency: SEK.
- Phrases grouped by meaning (local embedding model): `C:\Claude 1\Google KWP\projects\materiales.com.py\clusters.md` — read it next.
- One topic in full: export a group in Keyword Library ("Export"), it lands in `C:\Claude 1\Google KWP\projects\materiales.com.py\groups` as `<group>.md` (all phrases, volumes, CPC). Read that instead of filtering the CSV.
- Do **not** read `keywords.csv` in full. Filter it (grep or a short script) for the topic you need.
- **Deduplicated:** Keyword Planner gives close variants identical numbers ("curriculum vitae" / "cv" = same searches, same CPC). Within one meaning group, phrases with the same searches and the same low and high CPC are one search: shown as "phrase = variant, variant" and counted once in every total. `keywords.csv` keeps all rows.
- **Brands / competitors** (none) are listed in their own section and left out of all other totals — don't plan pages for competitor names.
- Rows containing the exclusion list are left out of this summary but stay in `keywords.csv`. Folder: `C:\Claude 1\Google KWP\projects\materiales.com.py`

## Totals

- Files: Keyword Stats 2026-09-30 at 22_46_56.csv (9800 rows), Keyword Stats 2026-09-30 at 22_47_53.csv (2411 rows), Keyword Stats 2026-09-30 at 22_50_04.csv (1506 rows), Keyword Stats 2026-09-30 at 22_50_06.csv (2411 rows), Keyword Stats 2026-09-30 at 22_50_07.csv (879 rows), Keyword Stats 2026-09-30 at 22_50_10.csv (808 rows)
- Rows read: 17815 · unique phrases: 15088 (325 spelling variants merged) · distinct searches: 15080 (8 same-number variants folded)
- Monthly searches: **117370 (deduplicated)** — 121610 before deduplication, 0 of them brand/competitor phrases (not counted)
- Excluded from this summary: nothing

## Themes (deduplicated)

| Theme | Searches | Searches/mo | Top |
| --- | --- | --- | --- |

## Places (deduplicated)

| Place | Searches | Searches/mo | Top |
| --- | --- | --- | --- |
| pilar | 2 | 20 | caño galvanizado para pilar de luz (10); caño galvanizado para pilar de luz trifasico (10) |
| altos | 2 | 10 | pintura epoxica de altos solidos (10); epoxico altos solidos (0) |

## Brands / competitors (not in any other total)

_No brand phrases._

## Top words and word pairs (weighted by searches)

bomba 14330 · agua 14130 · llave 11650 · paso 11220 · precio 8840 · pintura 8090 · electrico 6380 · cable 6190 · tablero 5960 · cocina 5330 · 1 5080 · chapa 4950 · epoxi 4460 · cemento 4280 · 2 4260 · termotanque 4180 · microcemento 4070 · ladrillo 3720 · litros 3520 · techos 3090

llave paso 9930 · bomba agua 5100 · tablero electrico 2640 · 1 2 2480 · pintura epoxi 2460 · paso agua 2100 · pileta cocina 1840 · bomba agu 1600 · bomba sumergible 1600 · termotanque electrico 1480 · bacha cocina 1470 · 3 4 1410 · pintura epoxica 1380 · cano galvanizado 1320 · resinas epoxicas 1310 · acero inoxidable 1230 · presurizador agua 1220 · ladrillo hueco 1150 · camara septica 1080 · 2 hp 1030

## Top 300 distinct searches (phrase — searches/mo · low–high CPC = same-number variants)

1. bomba de agu — 1600 · 0.45–5.51 = bomba de agua, bombeador de agua
2. resinas epoxicas — 1300 · 1.97–4.51
3. ladrillo hueco — 1000
4. presurizador de agua — 1000
5. bacha de cocina — 880
6. bachas de cocinas — 880
7. pileta para cocina — 880
8. camaraseptica — 720
9. bachas tramontina — 590 · 0.24–1.56
10. calefones — 590 · 0.30–2.64
11. paver — 590
12. caño galvanizado — 480 · 5.09–35.20
13. motobomba de agua — 480
14. pintura epoxico para piso — 480 · 0.59–2.29
15. tablero eléctrico — 480
16. tableros electricidad — 480
17. tejuelones — 480
18. bachas para cocina — 390
19. calefon electrico — 390 · 0.30–2.56
20. llave de paso — 390
21. llaves de paso — 390
22. microcemento — 390
23. pintura epoxi — 390 · 2.01–4.84
24. recuplast techos — 390 · 0.58–2.17
25. termotanque — 390 · 0.30–4.12 = termotanques
26. babeta — 320
27. centrífugas — 320 · 0.40–2.65
28. chapa ondulada — 320
29. llave de paso de agua — 320
30. motobomba — 320 · 2.48–12.54
31. resina epóxica — 320 · 0.52–2.45
32. bomba de agua sumergible — 260 · 1.94–5.81
33. bomba presurizadora — 260 · 0.40–2.33
34. bomba sumergible — 260
35. epoxi para pisos — 260 · 0.66–4.31
36. membrana liquida para techos — 260 · 1.08–4.24
37. pileta de cocina — 260
38. bomba centrifuga — 210 · 2.64–4.81 = bombas centrifugas, electrobomba centrifuga
39. camara septica para baño — 210
40. cemento de portland — 210
41. cemento portland — 210
42. llave de paso para ducha — 210
43. motor para agua — 210
44. pisos pavers — 210
45. tornillo para chapa — 210
46. bomba periferica — 170
47. cable de 4 mm precio por 100 metros — 170
48. chapa perforada — 170
49. claraboya — 170 · 82.08–128.09 = claraboyas
50. epoxica pintura — 170
51. mesada de cocina con pileta — 170
52. termotanque electrico — 170 · 0.20–1.13
53. ducha calefon electrico — 140
54. electrobomba — 140
55. mesadas de cemento revestidas — 140
56. motor de agua precio — 140
57. termo calefon 50 litros — 140 · 0.20–3.40
58. termo calefon 80 litros — 140 · 0.30–4.13
59. adopasto — 110
60. autorroscante para chapa — 110
61. bomba sumergible para pozo — 110
62. bombas de agua 1 hp precios — 110
63. bombas de agua precios — 110
64. cable cordon 2x1 — 110
65. fv llave de paso — 110
66. limahoya — 110
67. manta térmica para techo — 110
68. motor de agua para pozo — 110
69. pintura engomada para techos — 110 · 2.42–4.07
70. pintura para techo — 110
71. pintura para tejas — 110
72. pisos epoxi — 110 · 2.37–8.55
73. precio de cable de 6 mm por metro — 110
74. bacha de cocina tramontina — 90 · 0.54–4.11
75. bachas de acero inoxidable — 90
76. bachas para lavadero — 90
77. bomba de agua con presurizador — 90
78. bomba de agua de motor — 90
79. cable de alimentación — 90
80. cables de alimentación — 90
81. calefones lorenzetti — 90
82. camara septica de baño — 90
83. camara septica pozo ciego — 90
84. ladrillo rustico precio — 90
85. motobomba valco — 90
86. motor bomba de agua — 90
87. motor bombeador de agua — 90
88. motor de bomba de agua — 90
89. parrilla de ladrillo — 90
90. perfil omega — 90
91. pintura para chapa de zinc — 90
92. pisos de cemento alisado — 90
93. resina epóxica precio — 90
94. revoque texturado — 90
95. tablero electrico domiciliario — 90
96. toma electrica — 90
97. alisados de cemento — 70
98. autoperforante para chapa — 70
99. bacha de cocina moderna — 70
100. bachas de cocina modernas — 70
101. bomba de desagote — 70
102. cable de alta tension — 70
103. cable eléctrico — 70
104. cable para alta tension — 70
105. cables electricos — 70
106. campana llave de paso — 70
107. catálogo chapa perforada decorativa — 70
108. ducha calefon — 70
109. ladrillo para parrilla — 70
110. lucernarios — 70
111. mesada con pileta para cocina — 70
112. motobomba sumergible — 70
113. motor para tanque de agua — 70
114. pileta de acero inoxidable precio — 70
115. pileta inox — 70
116. pintura epoxica para pisos — 70
117. pintura impermeabilizante para techos — 70 · 1.10–3.75
118. pintura para techo de chapa — 70
119. precio de cable de 10mm por metro — 70
120. precio de tejuelas — 70
121. prolongador eléctrico — 70
122. rustico parrillas de ladrillo — 70
123. techo tragaluz — 70 · 9.77–104.39
124. tirafondo para chapa — 70
125. viga reticulada — 70
126. acelerante para hormigon — 50
127. bacha tramontina medidas — 50
128. bomba de achique — 50
129. bomba de agua manual — 50
130. bomba de agua presurizadora — 50
131. bomba de presion de agua — 50
132. bomba manual de agua — 50
133. bomba para agua — 50
134. bomba para el agua — 50
135. bomba periferica 1 2 hp — 50
136. bomba presurizadora de agua — 50
137. bombas para agua — 50
138. chapa microperforada — 50
139. ladrillo cerámico — 50
140. ladrillo macizo — 50
141. ladrillo rústico blanco — 50
142. llave de paso 1 2 — 50
143. llave de paso fusion — 50
144. llave de paso termofusion — 50
145. microcemento para pisos — 50
146. microcemento pisos — 50
147. microcemento sinteplast — 50 · 0.70–1.87 = sinteplast microcemento
148. motor presurizador de agua — 50
149. parrilla de ladrillo visto — 50
150. pegamento para porcelanico — 50
151. pileta para cocina precio — 50
152. pileta para lavar cubiertos — 50
153. presurizador de agua precio — 50
154. resina epoxi para madera — 50
155. tablero trifasico — 50
156. tableros electricos industriales — 50 · 1.22–7.19
157. teja cerámica — 50
158. tejuela — 50
159. tejuela ceramica — 50
160. tornillos autoperforante — 50
161. tragaluz para techo — 50 · 9.59–126.99
162. alambre electrico — 40
163. bacha de acero inoxidable precio — 40
164. bacha lavadero — 40
165. bachas de lavadero — 40
166. bomba centrifuga 1 hp — 40
167. bomba de agua 1 hp — 40
168. bomba de agua dañada — 40
169. bomba de agua electrica — 40
170. bomba de agua para casa — 40
171. bomba de agua periferica — 40
172. bomba de alta presion — 40
173. bomba de ariete — 40
174. bomba electrica de agua — 40
175. bomba hidroneumatica — 40
176. bomba periferica 1 hp — 40
177. bomba presurizadora de agua para casa — 40
178. bombas de agua para casa — 40
179. bombas de agua para pozos — 40
180. bombas manuales — 40
181. bombeador de agua para pozo — 40
182. cable para alargue — 40
183. cable preensamblado 2x16 — 40
184. cable trifasico — 40
185. camara septica como funciona — 40
186. camara septica de ladrillos — 40
187. caño redondo — 40
188. claraboya techo — 40
189. claraboyas techos — 40
190. hidroesmalte epoxi — 40
191. llave de paso fv con campana — 40
192. mesadas y bachas de cocina — 40
193. pileta de cocina tramontina — 40
194. pileta de marmol para cocina — 40
195. pileta para cocina de cemento — 40
196. pintura epoxica para metal — 40 · 0.87–3.11
197. precio de pileta para cocina — 40
198. recufloor pisos — 40
199. revoque fino — 40
200. revoque hidrofugo — 40
201. tablero seccional — 40
202. tableros industriales — 40
203. tejuela asfáltica — 40
204. tornillo autorroscante para chapa — 40
205. bacha acero inoxidable — 30
206. bacha tramontina simple — 30
207. bacha y mesada de acero inoxidable — 30
208. bomba con presurizador — 30
209. bomba de agua 1 2 hp — 30
210. bomba de agua 12 volt — 30
211. bomba de agua 12v — 30
212. bomba de agua centrifuga — 30
213. bomba de agua con tanque — 30
214. bomba de agua de 1 2 hp — 30
215. bomba de agua de 12v — 30
216. bomba de agua para riego — 30
217. bomba de agua para tanque — 30
218. bomba de agua pequeña — 30
219. bomba sumergible 1 hp — 30
220. bomba sumergible pedrollo — 30
221. cable amarillo rojo y blanco — 30
222. cable canal para piso — 30
223. cable preensamblado trifasico — 30
224. cable tpr 2x1 — 30
225. cal para revoque — 30
226. caño galvanizado de 2 pulgadas — 30
227. cemento alisado — 30
228. chapa ondulada galvanizada — 30
229. chapa ondulada medidas — 30
230. chapa perforada decorativa — 30
231. chapa perforadas decorativas — 30
232. chapa prepintada — 30
233. electrobomba sumergible — 30
234. epóxica — 30
235. epoxica para metal — 30
236. hierro t — 30
237. horno y parrilla de ladrillo — 30
238. ladrillo rústico — 30
239. ladrillo rústico rojo — 30
240. llave de agua — 30
241. llave de paso 3 4 — 30
242. llave de paso de media — 30
243. llave de paso deca — 30
244. llave de paso esferica — 30
245. llave de paso tigre — 30
246. llaves de agua — 30
247. mesada con pileta — 30
248. mezcla para revoque fino — 30
249. mini bomba de agua 12v — 30
250. motobomba centrifuga — 30
251. motobombas de agua precio — 30
252. motor de agua 1 hp — 30
253. parrilla de ladrillos comunes — 30
254. pileta acero inox — 30
255. pileta acero inoxidable — 30
256. pileta doble bacha — 30
257. piletas de acero inoxidable — 30
258. pintura aislante de calor para techos — 30
259. pintura epoxi para piso colores — 30
260. pintura epóxica para pisos colores — 30
261. pintura epoxica para pisos precios — 30
262. piso microcemento — 30
263. pisos de microcemento — 30
264. presurizador para bomba de agua — 30
265. presurizador pedrollo — 30
266. recuplast techos 20 litros precio — 30
267. sopapas para piletas de cocina — 30
268. sumergible — 30
269. tablero electrico externo — 30
270. termo calefon 20 litros — 30
271. termotanque 80 litros — 30 · 0.20–4.34
272. termotanque de 50 litros — 30
273. tipo de llaves de paso de agua — 30
274. tipos de cable de electricidad — 30
275. tipos de cables electricos — 30
276. tipos de llaves de paso de agua — 30
277. tramontina bachas — 30 · 0.63–2.14
278. xapa ondulada — 30
279. acelerante para concreto — 20
280. achique — 20
281. aditivo para hormigón — 20
282. aerosol pintura epoxi — 20
283. agua bomba — 20
284. bacha de cemento para lavadero — 20
285. bacha de cocina doble — 20
286. bacha doble para cocina — 20
287. bachas dobles de cocina — 20
288. bachas para cocina tramontina — 20
289. bomba agua — 20
290. bomba automatica de agua — 20
291. bomba de 1 hp — 20
292. bomba de agua 1 2 hp precio — 20
293. bomba de agua 2 hp — 20
294. bomba de agua a bateria — 20
295. bomba de agua automatica — 20
296. bomba de agua de 1 hp — 20
297. bomba de agua mini — 20
298. bomba de agua para pecera — 20
299. bomba de agua para piscina — 20
300. bomba de agua solar — 20

---

# Part 2 — Meaning groups

- Country / location of the data: **Paraguay** · language: Español
- 15088 phrases in 42 groups by meaning (local embedding model `bge-m3`, words ignored when grouping: paraguay, py, 2025, 2026; k-means on 798 phrases with >= 10 searches/mo, every other phrase added to its nearest group). Label = the group's highest-volume phrase.
- Searches/mo are **deduplicated** (same-number close variants count once, shown as "phrase (n) = variant") and leave out brand/competitor phrases. Total: 117,370 (deduplicated), 121,610 before.
- Built 2026-10-01 01:51 UTC. One group in full: Keyword Library → Semantic clusters → Export, written to `C:\Claude 1\Google KWP\projects\materiales.com.py\groups`. Full data: `keywords.csv` (filter it, don't read it whole).

## Largest groups

| Group | Phrases | Searches/mo | Top searches |
| --- | --- | --- | --- |
| chapa ondulada | 62 | 750 | chapa ondulada (320); chapa ondulada medidas (30); xapa ondulada (30); chapa de policarbonato ondulada (10); chapa negra ondulada (10); chapa ondulada 6 metros (10); chapa ondulada 6x1 (10); chapa ondulada blanca (10) |
| tejuelones | 2 | 490 | tejuelones (480); tejas y tejuelones precios (10) |
| tornillo para chapa | 35 | 480 | tornillo para chapa (210); tornillo para chapa madera (20); medidas de tornillos para chapa (10); precio de tornillo para chapa (10); precio tornillos para techo de chapa (10); tipos de tornillos para chapa (10); tornillo chapa madera (10); tornillo de chapa (10) |
| autoperforante para chapa | 42 | 450 | autoperforante para chapa (70); tornillos autoperforante (50); tornillo autorroscante para chapa (40); autoperforante chapa (10); autoperforante chapa madera (10); autoperforantes para chapa medidas (10); medidas de tornillos autoperforantes para chapa (10); precio de tornillo autoperforante para chapa (10) |
| babeta | 10 | 360 | babeta (320); babeta moto (10); babeta motorka (10); moto babeta (10); moto babeta 49cc (10); babeta jawa (0); java babeta (0); jawa babeta (0) |
| claraboya techo | 54 | 360 | claraboya techo (40); claraboyas techos (40); claraboya de techo (10); claraboya de techo con ventilacion (10); claraboya en el techo (10); claraboya en techo de chapa (10); claraboya en tejado (10); claraboya para techo chapa (10) |
| claraboya | 62 | 320 | claraboya (170) = claraboyas; chapa claraboya (10); claraboya arquitectura (10); claraboya barco (10); claraboya corrediza (10); claraboya luz (10); claraboya luz natural (10); claraboya marina (10) |
| casa con claraboya | 53 | 280 | casa con claraboya (10); claraboya aluminio (10); claraboya antigua (10); claraboya circular (10); claraboya con ventilacion (10); claraboya cristal (10); claraboya de acrilico (10); claraboya de barco (10) |
| techo tragaluz | 20 | 250 | techo tragaluz (70); tragaluz para techo (50); precio de tragaluces (10); precio de tragaluz para techos (10); precio tragaluz techo (10); tipos de tragaluces (10); tipos de tragaluz para techos (10); tragaluces velux (10) |
| limahoya | 20 | 230 | limahoya (110); limahoya chapa (10); limahoya construcción (10); limahoya cubierta (10); limahoya de zinc (10); limahoya medidas (10); limahoya metalica (10); limahoya precio (10) |
| autorroscante para chapa | 29 | 220 | autorroscante para chapa (110); tornillo autorroscante chapa (10); tornillo con rosca (10); tornillo con rosca interior (10); tornillo rosca chapa autoperforante (10); tornillo rosca chapa autorroscante (10); tornillo rosca chapa madera (10); tornillo rosca chapa para aluminio (10) |
| teja cerámica | 23 | 220 | teja cerámica (50); teja de ceramica (20); tejado de ceramica (20); ceramica verea (10); ceramicas verea (10); precio teja ceramica (10); teja ceramica francesa (10); teja ceramica negra (10) |
| chapa prepintada | 27 | 200 | chapa prepintada (30); chapa acanalada prepintada (10); chapa acanalada prepintada negra (10); chapa galvanizada prepintada (10); chapa ondulada prepintada (10); chapa prepintada colores (10); chapa prepintada gris (10); chapa prepintada negra (10) |
| lucernarios | 22 | 180 | lucernarios (70); lucernario circular (10); lucernario cubierta (10); lucernario de policarbonato (10); lucernario en cubierta (10); lucernario piramidal (10); lucernario policarbonato (10); lucernario precio (10) |
| chapa ondulada galvanizada | 28 | 180 | chapa ondulada galvanizada (30); chapa corrugada galvanizada (10); chapa galvanizada ondulada 6 metros (10); chapa galvanizada ondulada 6 metros precio (10); chapa galvanizada ondulada medidas y precios (10); chapa galvanizada ondulada precio m2 (10); chapa metalica ondulada galvanizada (10); chapa ondulada de acero galvanizado (10) |
| acrilico claraboyas | 41 | 160 | acrilico claraboyas (10); casa parisi claraboyas (10); claraboyas a medida (10); claraboyas de acrilico (10); claraboyas de policarbonato (10); claraboyas fijas rectangulares (10); claraboyas luz srl (10); claraboyas modernas (10) |
| chapa zinc ondulada | 14 | 110 | chapa zinc ondulada (20); chapa aluminio ondulada (10); chapa aluzinc (10); chapa de acero ondulada (10); chapa de aluminio ondulada (10); chapa de zinc ondulada (10); chapa metalica ondulada (10); chapa ondulada aluminio para fachadas (10) |
| manta térmica para techo | 1 | 110 | manta térmica para techo (110) |
| fibro de cemento | 17 | 110 | fibro de cemento (20); cemento para tejas (10); precio teja de cemento (10); teja asbesto cemento (10); teja cemento (10); teja de cemento (10); teja de cemento para techo (10); teja de cemento precio (10) |
| claraboya baño | 15 | 110 | claraboya baño (10); claraboya baño para losa (10); claraboya de baño (10); claraboya para baño (10); claraboya para bano con ventilacion (10); claraboya para baño techo de chapa (10); claraboya para barcos (10); claraboya para cocina (10) |
| tejuela ceramica | 15 | 100 | tejuela ceramica (50); teja ceramica curva (10); teja ceramica curva precio (10); teja concreto (10); teja termoacustica precio m2 (10); teja trapezoidal termoacustica (10); precio teja ceramica curva (0); precio teja curva 40x15 (0) |
| tirafondo para chapa | 4 | 90 | tirafondo para chapa (70); bulones para chapa (10); tirafondos rosca chapa (10); tornillos tirafondo para chapas (0) |
| claraboya de chapa | 32 | 70 | claraboya de chapa (10); claraboya electrica (10); claraboya monovalva (10); claraboya nautica (10); claraboya para terraza (10); claraboya telescopica (10); claraboya tubular (10); claraboia quadrada (0) |
| chapa de tornillo | 15 | 70 | chapa de tornillo (10); tornillo autorroscante panel sandwich (10); tornillo chapa (10); tornillo chapa chapa (10); tornillos chapa sandwich (10); tornillos chapa trapezoidal (10); tornillos para chapa sandwich (10); tornillo autorroscante para panel sandwich (0) |
| chapas metalicas onduladas | 15 | 60 | chapas metalicas onduladas (10); chapas onduladas de plastico (10); chapas onduladas de segunda mano (10); chapas onduladas para techos (10); chapas onduladas para tejados (10); chapas onduladas usadas (10); chapas onduladas baratas (0); chapas onduladas bricomart (0) |
| chapa blanca prepintada | 9 | 60 | chapa blanca prepintada (10); chapa lisa prepintada (10); chapa lisa prepintada blanca (10); chapa lisa prepintada negra (10); chapa prepintada blanca (10); chapa prepintada lisa (10); chapa lisa easy (0); chapa lisa prepintada colores (0) |
| precio teja mixta | 26 | 50 | precio teja mixta (10); precio tezontle (10); teja ceramica mixta (10); teja mixta (10); teja mixta roja (10); precio de la teja mixta (0); precio de teja mixta roja (0); precio teja ceramica mixta (0) |
| mazarron tejas | 12 | 40 | mazarron tejas (10); teja alicantina (10); tejas alicantina (10); tejas de hormigon (10); escandella tejas (0); hdr tejas (0); la escandella tejas (0); teja alicantina bricomart (0) |
| teja ceramica plana | 5 | 40 | teja ceramica plana (10); teja de ceramica plana (10); tejas de cemento planas (10); tejas de cemento usadas (10); permanit ceramic base (0) |
| chapa acanalada minionda | 22 | 40 | chapa acanalada minionda (10); chapa microondulada (10); chapa minionda (10); chapa minionda perforada (10); chapa aluminio minionda (0); chapa microperforada ondulada (0); chapa minionda aluminio precio (0); chapa minionda colores (0) |
| claraboya de pared | 6 | 40 | claraboya de pared (10); claraboya para pared (10); claraboya pared (10); escalera claraboya (10); claraboya con escalera (0); claraboya escalera (0) |
| claraboya plana | 9 | 30 | claraboya plana (10); claraboya techo plano (10); claraboyas velux (10); claraboya cubierta plana (0); claraboya tejado plano (0); claraboya tejado velux (0); claraboya velux cubierta plana (0); claraboya velux medidas (0) |
| chapa ondulada de policarbonato transparente | 10 | 30 | chapa ondulada de policarbonato transparente (10); placas onduladas policarbonato (10); placas onduladas transparentes (10); chapa ondulada policarbonato transparente (0); placa poliéster ondulada precio (0); placas de policarbonato onduladas (0); placas onduladas de chapa (0); placas onduladas para cubiertas (0) |
| claraboya abatible | 10 | 30 | claraboya abatible (10); claraboya easy (10); claraboya insoluz (10); claraboia automatica (0); claraboya apertura manual (0); claraboya automatica (0); claraboya para bano easy (0); claraboya transparente (0) |
| tornillo din 7981 | 2 | 20 | tornillo din 7981 (10); tornillo din 7982 (10) |
| claraboya practicable | 9 | 20 | claraboya practicable (10); claraboyas practicables (10); claraboya con escalera escamoteable (0); claraboya cristal transitable (0); claraboya pisable (0); claraboya transitable (0); claraboya transitable precio (0); claraboya vidrio transitable (0) |
| teja esmaltada negra | 9 | 20 | teja esmaltada negra (10); tejas esmaltadas azules (10); acrilicosgascon1027 (0); plasticos matillas (0); teja mixta envejecida bricomart (0); teja portuguesa envejecida (0); tejas barros y azulejos (0); tejas mixtas envejecidas (0) |
| chapa t101 negra | 4 | 10 | chapa t101 negra (10); chapa acanalada negra c25 (0); chapa prepintada t101 (0); chapa t101 prepintada (0) |
| limahoya que es | 2 | 10 | limahoya que es (10); limahoya definicion (0) |
| lucernario transitable | 3 | 10 | lucernario transitable (10); lucernario transitable precio (0); lucernarios pisables (0) |
| pernos para chapas | 1 | 10 | pernos para chapas (10) |
| claraboyas resopal | 1 | 0 | claraboyas resopal (0) |

---

# Part 3 — Group details (top 42 by searches/mo)

## chapa ondulada — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 62 phrases, 62 distinct searches, **750 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapa ondulada | 320 | 0.00 | 0.00 |  |
| chapa ondulada medidas | 30 | 0.00 | 0.00 |  |
| xapa ondulada | 30 | 0.00 | 0.00 |  |
| chapa de policarbonato ondulada | 10 | 0.00 | 0.00 |  |
| chapa negra ondulada | 10 | 0.00 | 0.00 |  |
| chapa ondulada 6 metros | 10 | 0.00 | 0.00 |  |
| chapa ondulada 6x1 | 10 | 0.00 | 0.00 |  |
| chapa ondulada blanca | 10 | 0.00 | 0.00 |  |
| chapa ondulada calibre 26 | 10 | 0.00 | 0.00 |  |
| chapa ondulada cincalum | 10 | 0.00 | 0.00 |  |
| chapa ondulada color | 10 | 0.00 | 0.00 |  |
| chapa ondulada cubierta | 10 | 0.00 | 0.00 |  |
| chapa ondulada curvada | 10 | 0.00 | 0.00 |  |
| chapa ondulada de poliester | 10 | 0.00 | 0.00 |  |
| chapa ondulada fibra de vidrio | 10 | 0.00 | 0.00 |  |
| chapa ondulada lacada | 10 | 0.00 | 0.00 |  |
| chapa ondulada para fachadas | 10 | 0.00 | 0.00 |  |
| chapa ondulada perforada | 10 | 0.00 | 0.00 |  |
| chapa ondulada perforada precio | 10 | 0.00 | 0.00 |  |
| chapa ondulada plastico | 10 | 0.00 | 0.00 |  |
| chapa ondulada poliester | 10 | 0.00 | 0.00 |  |
| chapa ondulada precio | 10 | 0.00 | 0.00 |  |
| chapa ondulada precio m2 | 10 | 0.00 | 0.00 |  |
| chapa ondulada pvc | 10 | 0.00 | 0.00 |  |
| chapa ondulada roja | 10 | 0.00 | 0.00 |  |
| chapa ondulada techo | 10 | 0.00 | 0.00 |  |
| chapa ondulada translucida | 10 | 0.00 | 0.00 |  |
| chapa ondulada transparente | 10 | 0.00 | 0.00 |  |
| chapa ondulada traslucida | 10 | 0.00 | 0.00 |  |
| chapa ondulada traslucida precio | 10 | 0.00 | 0.00 |  |
| chapa ondulada verde | 10 | 0.00 | 0.00 |  |
| chapa policarbonato ondulada | 10 | 0.00 | 0.00 |  |
| chapa transparente ondulada | 10 | 0.00 | 0.00 |  |
| chapa traslucida ondulada | 10 | 0.00 | 0.00 |  |
| comprar chapa ondulada | 10 | 0.00 | 0.00 |  |
| cubierta de chapa ondulada | 10 | 0.00 | 0.00 |  |
| fachadas de chapa ondulada | 10 | 0.00 | 0.00 |  |
| precio de chapa ondulada | 10 | 0.00 | 0.00 |  |
| techo chapa ondulada | 10 | 0.00 | 0.00 |  |
| techo de chapa ondulada | 10 | 0.00 | 0.00 |  |
| armco chapa ondulada | 0 | 0.00 | 0.00 |  |
| bricomart chapa ondulada | 0 | 0.00 | 0.00 |  |
| chapa lacada ondulada | 0 | 0.00 | 0.00 |  |
| chapa metal ondulada | 0 | 0.00 | 0.00 |  |
| chapa ondulada barata | 0 | 0.00 | 0.00 |  |
| chapa ondulada bricomart | 0 | 0.00 | 0.00 |  |
| chapa ondulada de segunda mano | 0 | 0.00 | 0.00 |  |
| chapa ondulada para cubiertas | 0 | 0.00 | 0.00 |  |
| chapa ondulada para puertas | 0 | 0.00 | 0.00 |  |
| chapa ondulada policarbonato cristal | 0 | 0.00 | 0.00 |  |
| chapa ondulada segunda mano | 0 | 0.00 | 0.00 |  |
| chapa ondulada tejado | 0 | 0.00 | 0.00 |  |
| chapa ondulada transparente bricomart | 0 | 0.00 | 0.00 |  |
| chapa pegaso lacada | 0 | 0.00 | 0.00 |  |
| chapa perfilada ondulada | 0 | 0.00 | 0.00 |  |
| chapa plastica ondulada | 0 | 0.00 | 0.00 |  |
| chapa plastico ondulada | 0 | 0.00 | 0.00 |  |
| chapa techo ondulada | 0 | 0.00 | 0.00 |  |
| chapa tejado ondulada | 0 | 0.00 | 0.00 |  |
| precio chapa ondulada roja | 0 | 0.00 | 0.00 |  |
| tejado chapa ondulada | 0 | 0.00 | 0.00 |  |
| tejado de chapa ondulada | 0 | 0.00 | 0.00 |  |

## tejuelones — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 2 phrases, 2 distinct searches, **490 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| tejuelones | 480 | 0.00 | 0.00 |  |
| tejas y tejuelones precios | 10 | 0.00 | 0.00 |  |

## tornillo para chapa — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 35 phrases, 35 distinct searches, **480 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| tornillo para chapa | 210 | 0.00 | 0.00 |  |
| tornillo para chapa madera | 20 | 0.00 | 0.00 |  |
| medidas de tornillos para chapa | 10 | 0.00 | 0.00 |  |
| precio de tornillo para chapa | 10 | 0.00 | 0.00 |  |
| precio tornillos para techo de chapa | 10 | 0.00 | 0.00 |  |
| tipos de tornillos para chapa | 10 | 0.00 | 0.00 |  |
| tornillo chapa madera | 10 | 0.00 | 0.00 |  |
| tornillo de chapa | 10 | 0.00 | 0.00 |  |
| tornillo gancho para chapa | 10 | 0.00 | 0.00 |  |
| tornillo para chapa acanalada | 10 | 0.00 | 0.00 |  |
| tornillo para chapa de techo | 10 | 0.00 | 0.00 |  |
| tornillo para chapa punta aguja | 10 | 0.00 | 0.00 |  |
| tornillo para chapa punta mecha | 10 | 0.00 | 0.00 |  |
| tornillo para chapa trapezoidal | 10 | 0.00 | 0.00 |  |
| tornillo para techo chapa | 10 | 0.00 | 0.00 |  |
| tornillo para techo de chapa | 10 | 0.00 | 0.00 |  |
| tornillo punta aguja para chapa | 10 | 0.00 | 0.00 |  |
| tornillos para chapa de policarbonato | 10 | 0.00 | 0.00 |  |
| tornillos para chapa de puerta | 10 | 0.00 | 0.00 |  |
| tornillos para chapa galvanizada | 10 | 0.00 | 0.00 |  |
| tornillos para chapa medidas | 10 | 0.00 | 0.00 |  |
| tornillos para chapa metalica | 10 | 0.00 | 0.00 |  |
| tornillos para chapa ondulada | 10 | 0.00 | 0.00 |  |
| tornillos para chapa precio | 10 | 0.00 | 0.00 |  |
| tornillos para clavar chapas | 10 | 0.00 | 0.00 |  |
| tornillos para madera y chapa | 10 | 0.00 | 0.00 |  |
| tornillos para techo de chapa y madera | 10 | 0.00 | 0.00 |  |
| tornillo para chapas galvanizadas | 0 | 0.00 | 0.00 |  |
| tornillos para chapa plastica | 0 | 0.00 | 0.00 |  |
| tornillos para chapas de zinc | 0 | 0.00 | 0.00 |  |
| tornillos para cubiertas metálicas | 0 | 0.00 | 0.00 |  |
| tornillos para fijar chapas | 0 | 0.00 | 0.00 |  |
| tornillos para lámina galvanizada precio | 0 | 0.00 | 0.00 |  |
| tornillos para tejado de chapa | 0 | 0.00 | 0.00 |  |
| tornillos parker para chapa | 0 | 0.00 | 0.00 |  |

## autoperforante para chapa — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 42 phrases, 42 distinct searches, **450 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| autoperforante para chapa | 70 | 0.00 | 0.00 |  |
| tornillos autoperforante | 50 | 0.00 | 0.00 |  |
| tornillo autorroscante para chapa | 40 | 0.00 | 0.00 |  |
| autoperforante chapa | 10 | 0.00 | 0.00 |  |
| autoperforante chapa madera | 10 | 0.00 | 0.00 |  |
| autoperforantes para chapa medidas | 10 | 0.00 | 0.00 |  |
| medidas de tornillos autoperforantes para chapa | 10 | 0.00 | 0.00 |  |
| precio de tornillo autoperforante para chapa | 10 | 0.00 | 0.00 |  |
| precio de tornillos autoperforantes para chapa | 10 | 0.00 | 0.00 |  |
| precio de tornillos autoperforantes para techos | 10 | 0.00 | 0.00 |  |
| precio tornillo autoperforante para chapa | 10 | 0.00 | 0.00 |  |
| precio tornillos autoperforantes para chapa | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante chapa | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante chapa madera | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante chapa madera 2 1 2 | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante para chapa 2 1 2 | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante para chapa acanalada | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante para chapa cabeza hexagonal | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante para chapa medidas | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante para chapa trapezoidal | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante para chapa y madera | 10 | 0.00 | 0.00 |  |
| tornillo autoperforante para techo de chapa | 10 | 0.00 | 0.00 |  |
| tornillo chapa autoperforante | 10 | 0.00 | 0.00 |  |
| tornillos autoperforantes para chapa | 10 | 0.00 | 0.00 |  |
| tornillos autoperforantes para chapa acanalada | 10 | 0.00 | 0.00 |  |
| tornillos autoperforantes para chapa de 2 pulgadas | 10 | 0.00 | 0.00 |  |
| tornillos autoperforantes para chapa precio | 10 | 0.00 | 0.00 |  |
| tornillos autoperforantes para madera y chapa | 10 | 0.00 | 0.00 |  |
| tornillos autoperforantes para techar | 10 | 0.00 | 0.00 |  |
| tornillos autoperforantes t1 punta mecha | 10 | 0.00 | 0.00 |  |
| tornillos autorroscantes para chapa | 10 | 0.00 | 0.00 |  |
| tornillos autorroscantes para metal | 10 | 0.00 | 0.00 |  |
| autoperforantes para chapa acanalada | 0 | 0.00 | 0.00 |  |
| autorroscantes y autoperforantes para chapas metálicas y maderas duras | 0 | 0.00 | 0.00 |  |
| tornillo autoperforante para perfil c | 0 | 0.00 | 0.00 |  |
| tornillos autoperforantes 2 pulgadas para chapa | 0 | 0.00 | 0.00 |  |
| tornillos autoperforantes para chapa por m2 | 0 | 0.00 | 0.00 |  |
| tornillos autoperforantes para chapa y madera | 0 | 0.00 | 0.00 |  |
| tornillos autoperforantes para chapa y perfil c | 0 | 0.00 | 0.00 |  |
| tornillos autoperforantes para techo de chapa | 0 | 0.00 | 0.00 |  |
| tornillos autoperforantes para techos de chapa precios | 0 | 0.00 | 0.00 |  |
| tornillos autotaladrantes para chapa | 0 | 0.00 | 0.00 |  |

## babeta — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 10 phrases, 10 distinct searches, **360 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| babeta | 320 | 0.00 | 0.00 |  |
| babeta moto | 10 | 0.00 | 0.00 |  |
| babeta motorka | 10 | 0.00 | 0.00 |  |
| moto babeta | 10 | 0.00 | 0.00 |  |
| moto babeta 49cc | 10 | 0.00 | 0.00 |  |
| babeta jawa | 0 | 0.00 | 0.00 |  |
| java babeta | 0 | 0.00 | 0.00 |  |
| jawa babeta | 0 | 0.00 | 0.00 |  |
| jawa babetta 207 500 | 0 | 0.00 | 0.00 |  |
| moto babetta | 0 | 0.00 | 0.00 |  |

## claraboya techo — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 54 phrases, 54 distinct searches, **360 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| claraboya techo | 40 | 0.00 | 0.00 |  |
| claraboyas techos | 40 | 0.00 | 0.00 |  |
| claraboya de techo | 10 | 0.00 | 0.00 |  |
| claraboya de techo con ventilacion | 10 | 0.00 | 0.00 |  |
| claraboya en el techo | 10 | 0.00 | 0.00 |  |
| claraboya en techo de chapa | 10 | 0.00 | 0.00 |  |
| claraboya en tejado | 10 | 0.00 | 0.00 |  |
| claraboya para techo chapa | 10 | 0.00 | 0.00 |  |
| claraboya para techo con ventilacion | 10 | 0.00 | 0.00 |  |
| claraboya para techo de chapa | 10 | 0.00 | 0.00 |  |
| claraboya para techo de chapa acanalada | 10 | 0.00 | 0.00 |  |
| claraboya para techo de chapa trapezoidal | 10 | 0.00 | 0.00 |  |
| claraboya para techo de losa | 10 | 0.00 | 0.00 |  |
| claraboya para techo de loza | 10 | 0.00 | 0.00 |  |
| claraboya para techo losa | 10 | 0.00 | 0.00 |  |
| claraboya techo casa | 10 | 0.00 | 0.00 |  |
| claraboya techo chapa | 10 | 0.00 | 0.00 |  |
| claraboya techo de chapa | 10 | 0.00 | 0.00 |  |
| claraboya techo eternit | 10 | 0.00 | 0.00 |  |
| claraboya techo precio | 10 | 0.00 | 0.00 |  |
| claraboya techo pvc | 10 | 0.00 | 0.00 |  |
| claraboya tejado segunda mano | 10 | 0.00 | 0.00 |  |
| claraboyas con ventilacion para techos | 10 | 0.00 | 0.00 |  |
| claraboyas de acrilico para techos | 10 | 0.00 | 0.00 |  |
| claraboyas de vidrio para techos | 10 | 0.00 | 0.00 |  |
| claraboyas para techo de chapa con ventilacion | 10 | 0.00 | 0.00 |  |
| claraboyas para techos | 10 | 0.00 | 0.00 |  |
| claraboyas para techos precios | 10 | 0.00 | 0.00 |  |
| techo de chapa con claraboya | 10 | 0.00 | 0.00 |  |
| tipos de claraboyas para techos | 10 | 0.00 | 0.00 |  |
| claraboya acceso tejado | 0 | 0.00 | 0.00 |  |
| claraboya en techo pvc | 0 | 0.00 | 0.00 |  |
| claraboya fija tejado | 0 | 0.00 | 0.00 |  |
| claraboya para techo de losa precios | 0 | 0.00 | 0.00 |  |
| claraboya para techo de madera | 0 | 0.00 | 0.00 |  |
| claraboya techo bricomart | 0 | 0.00 | 0.00 |  |
| claraboya techo fija | 0 | 0.00 | 0.00 |  |
| claraboya tejado inclinado | 0 | 0.00 | 0.00 |  |
| claraboyas de aluminio para techos | 0 | 0.00 | 0.00 |  |
| claraboyas electricas para techos | 0 | 0.00 | 0.00 |  |
| claraboyas para acceso a cubierta | 0 | 0.00 | 0.00 |  |
| claraboyas para techos eternit | 0 | 0.00 | 0.00 |  |
| claraboyas para techos planos | 0 | 0.00 | 0.00 |  |
| claraboyas para tejados | 0 | 0.00 | 0.00 |  |
| claraboyas para tejados de uralita | 0 | 0.00 | 0.00 |  |
| claraboyas para tejados inclinados | 0 | 0.00 | 0.00 |  |
| claraboyas para tejados precios | 0 | 0.00 | 0.00 |  |
| claraboyas para tejados velux | 0 | 0.00 | 0.00 |  |
| medidas claraboyas para techos | 0 | 0.00 | 0.00 |  |
| precio claraboya techo | 0 | 0.00 | 0.00 |  |
| precio de claraboya para techo de chapa | 0 | 0.00 | 0.00 |  |
| precio de claraboyas para techos | 0 | 0.00 | 0.00 |  |
| precios de claraboyas para tejados | 0 | 0.00 | 0.00 |  |
| techo en pvc con claraboya | 0 | 0.00 | 0.00 |  |

## claraboya — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 62 phrases, 62 distinct searches, **320 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| claraboya | 170 | 82.08 | 128.09 | claraboyas |
| chapa claraboya | 10 | 0.00 | 0.00 |  |
| claraboya arquitectura | 10 | 0.00 | 0.00 |  |
| claraboya barco | 10 | 0.00 | 0.00 |  |
| claraboya corrediza | 10 | 0.00 | 0.00 |  |
| claraboya luz | 10 | 0.00 | 0.00 |  |
| claraboya luz natural | 10 | 0.00 | 0.00 |  |
| claraboya marina | 10 | 0.00 | 0.00 |  |
| claraboya ojo de buey | 10 | 0.00 | 0.00 |  |
| claraboya para losa | 10 | 0.00 | 0.00 |  |
| claraboya puerta | 10 | 0.00 | 0.00 |  |
| claraboya solar | 10 | 0.00 | 0.00 |  |
| claraboya tejado | 10 | 0.00 | 0.00 |  |
| claraboya tragaluz | 10 | 0.00 | 0.00 |  |
| claraboya tubo de luz | 10 | 0.00 | 0.00 |  |
| precio de claraboya | 10 | 0.00 | 0.00 |  |
| amazon claraboya | 0 | 0.00 | 0.00 |  |
| bivalva claraboya | 0 | 0.00 | 0.00 |  |
| claraboya 4 | 0 | 0.00 | 0.00 |  |
| claraboya 6 | 0 | 0.00 | 0.00 |  |
| claraboya 8 | 0 | 0.00 | 0.00 |  |
| claraboya amazon | 0 | 0.00 | 0.00 |  |
| claraboya azotea | 0 | 0.00 | 0.00 |  |
| claraboya bivalva | 0 | 0.00 | 0.00 |  |
| claraboya bivalva precio | 0 | 0.00 | 0.00 |  |
| claraboya bricomart | 0 | 0.00 | 0.00 |  |
| claraboya camper amazon | 0 | 0.00 | 0.00 |  |
| claraboya carbest | 0 | 0.00 | 0.00 |  |
| claraboya casera | 0 | 0.00 | 0.00 |  |
| claraboya de luz | 0 | 0.00 | 0.00 |  |
| claraboya electrica precio | 0 | 0.00 | 0.00 |  |
| claraboya eternit | 0 | 0.00 | 0.00 |  |
| claraboya eternit precio | 0 | 0.00 | 0.00 |  |
| claraboya fibrocemento | 0 | 0.00 | 0.00 |  |
| claraboya furgoneta amazon | 0 | 0.00 | 0.00 |  |
| claraboya heki | 0 | 0.00 | 0.00 |  |
| claraboya heki 2 | 0 | 0.00 | 0.00 |  |
| claraboya luz solar | 0 | 0.00 | 0.00 |  |
| claraboya mercado libre | 0 | 0.00 | 0.00 |  |
| claraboya midi heki | 0 | 0.00 | 0.00 |  |
| claraboya mini heki | 0 | 0.00 | 0.00 |  |
| claraboya mini heki style | 0 | 0.00 | 0.00 |  |
| claraboya nave industrial | 0 | 0.00 | 0.00 |  |
| claraboya patio de luces | 0 | 0.00 | 0.00 |  |
| claraboya patio interior | 0 | 0.00 | 0.00 |  |
| claraboya piso | 0 | 0.00 | 0.00 |  |
| claraboya policarbonato precio | 0 | 0.00 | 0.00 |  |
| claraboya pvc | 0 | 0.00 | 0.00 |  |
| claraboya seguridad | 0 | 0.00 | 0.00 |  |
| claraboya sotano | 0 | 0.00 | 0.00 |  |
| claraboya suelo | 0 | 0.00 | 0.00 |  |
| claraboya tejado amazon | 0 | 0.00 | 0.00 |  |
| claraboya tejado barata | 0 | 0.00 | 0.00 |  |
| claraboya tejado bricomart | 0 | 0.00 | 0.00 |  |
| claraboya tejado grande | 0 | 0.00 | 0.00 |  |
| claraboya tejado uralita | 0 | 0.00 | 0.00 |  |
| claraboya tubo solar | 0 | 0.00 | 0.00 |  |
| claraboya uralita | 0 | 0.00 | 0.00 |  |
| claraboya vetus | 0 | 0.00 | 0.00 |  |
| medidas claraboya | 0 | 0.00 | 0.00 |  |
| medidas claraboya eternit | 0 | 0.00 | 0.00 |  |
| precio claraboya tejado | 0 | 0.00 | 0.00 |  |

## casa con claraboya — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 53 phrases, 53 distinct searches, **280 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| casa con claraboya | 10 | 0.00 | 0.00 |  |
| claraboya aluminio | 10 | 0.00 | 0.00 |  |
| claraboya antigua | 10 | 0.00 | 0.00 |  |
| claraboya circular | 10 | 0.00 | 0.00 |  |
| claraboya con ventilacion | 10 | 0.00 | 0.00 |  |
| claraboya cristal | 10 | 0.00 | 0.00 |  |
| claraboya de acrilico | 10 | 0.00 | 0.00 |  |
| claraboya de barco | 10 | 0.00 | 0.00 |  |
| claraboya de cemento | 10 | 0.00 | 0.00 |  |
| claraboya de cristal | 10 | 0.00 | 0.00 |  |
| claraboya de madera | 10 | 0.00 | 0.00 |  |
| claraboya de ventilacion | 10 | 0.00 | 0.00 |  |
| claraboya de vidrio | 10 | 0.00 | 0.00 |  |
| claraboya en vidrio | 10 | 0.00 | 0.00 |  |
| claraboya horizontal | 10 | 0.00 | 0.00 |  |
| claraboya metalica | 10 | 0.00 | 0.00 |  |
| claraboya piramidal | 10 | 0.00 | 0.00 |  |
| claraboya plastica | 10 | 0.00 | 0.00 |  |
| claraboya policarbonato | 10 | 0.00 | 0.00 |  |
| claraboya rectangular | 10 | 0.00 | 0.00 |  |
| claraboya redonda | 10 | 0.00 | 0.00 |  |
| claraboya vidrio para piso | 10 | 0.00 | 0.00 |  |
| cristal para claraboya | 10 | 0.00 | 0.00 |  |
| eternit con claraboya | 10 | 0.00 | 0.00 |  |
| tipos de claraboya | 10 | 0.00 | 0.00 |  |
| una claraboya | 10 | 0.00 | 0.00 |  |
| vidrio de claraboya | 10 | 0.00 | 0.00 |  |
| vidrio para claraboya | 10 | 0.00 | 0.00 |  |
| acrilico para claraboya | 0 | 0.00 | 0.00 |  |
| claraboya artificial | 0 | 0.00 | 0.00 |  |
| claraboya con apertura | 0 | 0.00 | 0.00 |  |
| claraboya de aluminio | 0 | 0.00 | 0.00 |  |
| claraboya de eternit | 0 | 0.00 | 0.00 |  |
| claraboya de metacrilato | 0 | 0.00 | 0.00 |  |
| claraboya de plastico | 0 | 0.00 | 0.00 |  |
| claraboya en pvc | 0 | 0.00 | 0.00 |  |
| claraboya eternit medidas | 0 | 0.00 | 0.00 |  |
| claraboya madera | 0 | 0.00 | 0.00 |  |
| claraboya metacrilato | 0 | 0.00 | 0.00 |  |
| claraboya metacrilato precio | 0 | 0.00 | 0.00 |  |
| claraboya para piso | 0 | 0.00 | 0.00 |  |
| claraboya para uralita | 0 | 0.00 | 0.00 |  |
| claraboya prefabricada | 0 | 0.00 | 0.00 |  |
| claraboya vidrio precio | 0 | 0.00 | 0.00 |  |
| cristal claraboya | 0 | 0.00 | 0.00 |  |
| cristal de claraboya | 0 | 0.00 | 0.00 |  |
| medidas de una claraboya | 0 | 0.00 | 0.00 |  |
| policarbonato claraboya | 0 | 0.00 | 0.00 |  |
| precio de una claraboya | 0 | 0.00 | 0.00 |  |
| puerta con claraboya | 0 | 0.00 | 0.00 |  |
| vidrio claraboya eternit | 0 | 0.00 | 0.00 |  |
| vidrio para claraboya eternit | 0 | 0.00 | 0.00 |  |
| vidrio para claraboya precio | 0 | 0.00 | 0.00 |  |

## techo tragaluz — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 20 phrases, 20 distinct searches, **250 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| techo tragaluz | 70 | 9.77 | 104.39 |  |
| tragaluz para techo | 50 | 9.59 | 126.99 |  |
| precio de tragaluces | 10 | 0.00 | 0.00 |  |
| precio de tragaluz para techos | 10 | 0.00 | 0.00 |  |
| precio tragaluz techo | 10 | 0.00 | 0.00 |  |
| tipos de tragaluces | 10 | 0.00 | 0.00 |  |
| tipos de tragaluz para techos | 10 | 0.00 | 0.00 |  |
| tragaluces velux | 10 | 0.00 | 0.00 |  |
| tragaluz con ventilación para techos | 10 | 0.00 | 0.00 |  |
| tragaluz para baño | 10 | 0.00 | 0.00 |  |
| tragaluz para techo precio | 10 | 0.00 | 0.00 |  |
| tragaluz techo baño | 10 | 0.00 | 0.00 |  |
| tragaluz techo fijo | 10 | 0.00 | 0.00 |  |
| tragaluz techo precio | 10 | 0.00 | 0.00 |  |
| tragaluz tejado | 10 | 0.00 | 0.00 |  |
| precio de tragaluz de techo | 0 | 0.00 | 0.00 |  |
| tejados con tragaluz | 0 | 0.00 | 0.00 |  |
| tragaluces para tejados | 0 | 0.00 | 0.00 |  |
| tragaluz de techo para baño | 0 | 0.00 | 0.00 |  |
| tragaluz velux precios | 0 | 0.00 | 0.00 |  |

## limahoya — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 20 phrases, 20 distinct searches, **230 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| limahoya | 110 | 0.00 | 0.00 |  |
| limahoya chapa | 10 | 0.00 | 0.00 |  |
| limahoya construcción | 10 | 0.00 | 0.00 |  |
| limahoya cubierta | 10 | 0.00 | 0.00 |  |
| limahoya de zinc | 10 | 0.00 | 0.00 |  |
| limahoya medidas | 10 | 0.00 | 0.00 |  |
| limahoya metalica | 10 | 0.00 | 0.00 |  |
| limahoya precio | 10 | 0.00 | 0.00 |  |
| limahoya pvc | 10 | 0.00 | 0.00 |  |
| limahoya tejado | 10 | 0.00 | 0.00 |  |
| limahoya zinc | 10 | 0.00 | 0.00 |  |
| limahoyas | 10 | 0.00 | 0.00 |  |
| limahoyas de zinc | 10 | 0.00 | 0.00 |  |
| limahoya bricomart | 0 | 0.00 | 0.00 |  |
| limahoya chapa galvanizada | 0 | 0.00 | 0.00 |  |
| limahoya chimenea | 0 | 0.00 | 0.00 |  |
| limahoya easy | 0 | 0.00 | 0.00 |  |
| limahoya eternit | 0 | 0.00 | 0.00 |  |
| limahoya teja asfaltica | 0 | 0.00 | 0.00 |  |
| limahoyas para tejados | 0 | 0.00 | 0.00 |  |

## autorroscante para chapa — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 29 phrases, 29 distinct searches, **220 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| autorroscante para chapa | 110 | 0.00 | 0.00 |  |
| tornillo autorroscante chapa | 10 | 0.00 | 0.00 |  |
| tornillo con rosca | 10 | 0.00 | 0.00 |  |
| tornillo con rosca interior | 10 | 0.00 | 0.00 |  |
| tornillo rosca chapa autoperforante | 10 | 0.00 | 0.00 |  |
| tornillo rosca chapa autorroscante | 10 | 0.00 | 0.00 |  |
| tornillo rosca chapa madera | 10 | 0.00 | 0.00 |  |
| tornillo rosca chapa para aluminio | 10 | 0.00 | 0.00 |  |
| tornillos rosca chapa | 10 | 0.00 | 0.00 |  |
| tornillos rosca chapa con arandela de goma | 10 | 0.00 | 0.00 |  |
| tornillos rosca chapa medidas | 10 | 0.00 | 0.00 |  |
| tornillos rosca chapa para hierro | 10 | 0.00 | 0.00 |  |
| rosca chapa autoperforante | 0 | 0.00 | 0.00 |  |
| rosca chapa cabeza hexagonal | 0 | 0.00 | 0.00 |  |
| tornillo de rosca cortante | 0 | 0.00 | 0.00 |  |
| tornillo rosca chapa 8 mm | 0 | 0.00 | 0.00 |  |
| tornillo rosca chapa aluminio | 0 | 0.00 | 0.00 |  |
| tornillo rosca chapa autotaladrante | 0 | 0.00 | 0.00 |  |
| tornillo rosca chapa bricomart | 0 | 0.00 | 0.00 |  |
| tornillo rosca chapa cabeza plana | 0 | 0.00 | 0.00 |  |
| tornillo rosca chapa pladur | 0 | 0.00 | 0.00 |  |
| tornillo rosca viga | 0 | 0.00 | 0.00 |  |
| tornillos de rosca cortante | 0 | 0.00 | 0.00 |  |
| tornillos rosca chapa autotaladrante | 0 | 0.00 | 0.00 |  |
| tornillos rosca chapa blancos | 0 | 0.00 | 0.00 |  |
| tornillos rosca chapa inox | 0 | 0.00 | 0.00 |  |
| tornillos rosca chapa largos | 0 | 0.00 | 0.00 |  |
| tornillos rosca chapa para tejados | 0 | 0.00 | 0.00 |  |
| tornillos rosca chapa pequeños | 0 | 0.00 | 0.00 |  |

## teja cerámica — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 23 phrases, 23 distinct searches, **220 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| teja cerámica | 50 | 0.00 | 0.00 |  |
| teja de ceramica | 20 | 0.00 | 0.00 |  |
| tejado de ceramica | 20 | 0.00 | 0.00 |  |
| ceramica verea | 10 | 0.00 | 0.00 |  |
| ceramicas verea | 10 | 0.00 | 0.00 |  |
| precio teja ceramica | 10 | 0.00 | 0.00 |  |
| teja ceramica francesa | 10 | 0.00 | 0.00 |  |
| teja ceramica negra | 10 | 0.00 | 0.00 |  |
| teja de ceramica precio | 10 | 0.00 | 0.00 |  |
| tejas cerámicas mixtas | 10 | 0.00 | 0.00 |  |
| tejas cerámicas precio | 10 | 0.00 | 0.00 |  |
| tejas ceramicas tipos | 10 | 0.00 | 0.00 |  |
| tejas de arcilla características | 10 | 0.00 | 0.00 |  |
| tejas de cerámica precios | 10 | 0.00 | 0.00 |  |
| tejas y pisos de cerámica | 10 | 0.00 | 0.00 |  |
| tipos de tejas ceramicas | 10 | 0.00 | 0.00 |  |
| borja tejas ceramicas | 0 | 0.00 | 0.00 |  |
| ceramica borja | 0 | 0.00 | 0.00 |  |
| teja arabe ceramica | 0 | 0.00 | 0.00 |  |
| teja ceramica arabe | 0 | 0.00 | 0.00 |  |
| tejas ceramicas borja | 0 | 0.00 | 0.00 |  |
| tejas de ceramica caracteristicas | 0 | 0.00 | 0.00 |  |
| tipos de tejas de arcilla | 0 | 0.00 | 0.00 |  |

## chapa prepintada — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 27 phrases, 27 distinct searches, **200 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapa prepintada | 30 | 0.00 | 0.00 |  |
| chapa acanalada prepintada | 10 | 0.00 | 0.00 |  |
| chapa acanalada prepintada negra | 10 | 0.00 | 0.00 |  |
| chapa galvanizada prepintada | 10 | 0.00 | 0.00 |  |
| chapa ondulada prepintada | 10 | 0.00 | 0.00 |  |
| chapa prepintada colores | 10 | 0.00 | 0.00 |  |
| chapa prepintada gris | 10 | 0.00 | 0.00 |  |
| chapa prepintada negra | 10 | 0.00 | 0.00 |  |
| chapa prepintada precio | 10 | 0.00 | 0.00 |  |
| chapa prepintada trapezoidal | 10 | 0.00 | 0.00 |  |
| chapa sinusoidal prepintada | 10 | 0.00 | 0.00 |  |
| chapa trapezoidal color gris | 10 | 0.00 | 0.00 |  |
| chapa trapezoidal negra | 10 | 0.00 | 0.00 |  |
| chapa trapezoidal negra precio | 10 | 0.00 | 0.00 |  |
| chapa trapezoidal prepintada | 10 | 0.00 | 0.00 |  |
| chapa trapezoidal prepintada negra | 10 | 0.00 | 0.00 |  |
| chapas pre pintadas | 10 | 0.00 | 0.00 |  |
| colores de chapa prepintada | 10 | 0.00 | 0.00 |  |
| chapa sinusoidal prepintada negra | 0 | 0.00 | 0.00 |  |
| chapa trapezoidal color azul precio | 0 | 0.00 | 0.00 |  |
| chapa trapezoidal negra 4 metros | 0 | 0.00 | 0.00 |  |
| chapa trapezoidal negra 5 metros | 0 | 0.00 | 0.00 |  |
| chapas prepintadas para techos precios | 0 | 0.00 | 0.00 |  |
| chapas prepintadas ternium siderar | 0 | 0.00 | 0.00 |  |
| colores chapa prepintada | 0 | 0.00 | 0.00 |  |
| plancha ondulada prepintada | 0 | 0.00 | 0.00 |  |
| precio de chapa trapezoidal negra | 0 | 0.00 | 0.00 |  |

## lucernarios — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 22 phrases, 22 distinct searches, **180 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| lucernarios | 70 | 0.00 | 0.00 |  |
| lucernario circular | 10 | 0.00 | 0.00 |  |
| lucernario cubierta | 10 | 0.00 | 0.00 |  |
| lucernario de policarbonato | 10 | 0.00 | 0.00 |  |
| lucernario en cubierta | 10 | 0.00 | 0.00 |  |
| lucernario piramidal | 10 | 0.00 | 0.00 |  |
| lucernario policarbonato | 10 | 0.00 | 0.00 |  |
| lucernario precio | 10 | 0.00 | 0.00 |  |
| lucernario techo | 10 | 0.00 | 0.00 |  |
| lucernario tubular | 10 | 0.00 | 0.00 |  |
| lucernario vidrio | 10 | 0.00 | 0.00 |  |
| tipos de lucernarios | 10 | 0.00 | 0.00 |  |
| lucernario cubierta plana | 0 | 0.00 | 0.00 |  |
| lucernario metacrilato | 0 | 0.00 | 0.00 |  |
| lucernario policarbonato celular | 0 | 0.00 | 0.00 |  |
| lucernario policarbonato precio | 0 | 0.00 | 0.00 |  |
| lucernario tejado | 0 | 0.00 | 0.00 |  |
| lucernarios de cristal | 0 | 0.00 | 0.00 |  |
| lucernarios para tejados | 0 | 0.00 | 0.00 |  |
| policarbonato lucernario | 0 | 0.00 | 0.00 |  |
| precio lucernario vidrio | 0 | 0.00 | 0.00 |  |
| vidrio lucernario | 0 | 0.00 | 0.00 |  |

## chapa ondulada galvanizada — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 28 phrases, 28 distinct searches, **180 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapa ondulada galvanizada | 30 | 0.00 | 0.00 |  |
| chapa corrugada galvanizada | 10 | 0.00 | 0.00 |  |
| chapa galvanizada ondulada 6 metros | 10 | 0.00 | 0.00 |  |
| chapa galvanizada ondulada 6 metros precio | 10 | 0.00 | 0.00 |  |
| chapa galvanizada ondulada medidas y precios | 10 | 0.00 | 0.00 |  |
| chapa galvanizada ondulada precio m2 | 10 | 0.00 | 0.00 |  |
| chapa metalica ondulada galvanizada | 10 | 0.00 | 0.00 |  |
| chapa ondulada de acero galvanizado | 10 | 0.00 | 0.00 |  |
| chapa ondulada galvanizada medidas | 10 | 0.00 | 0.00 |  |
| medidas de chapa ondulada | 10 | 0.00 | 0.00 |  |
| medidas de chapas onduladas galvanizadas | 10 | 0.00 | 0.00 |  |
| precio chapa galvanizada ondulada | 10 | 0.00 | 0.00 |  |
| precio chapa galvanizada ondulada 4 metros | 10 | 0.00 | 0.00 |  |
| precio chapa galvanizada ondulada 6 metros | 10 | 0.00 | 0.00 |  |
| precio de chapa galvanizada ondulada | 10 | 0.00 | 0.00 |  |
| precio m2 chapa ondulada galvanizada | 10 | 0.00 | 0.00 |  |
| chapa de acero galvanizado ondulada | 0 | 0.00 | 0.00 |  |
| chapa galvanizada ondulada bricomart | 0 | 0.00 | 0.00 |  |
| chapa galvanizada ondulada segunda mano | 0 | 0.00 | 0.00 |  |
| chapa ondulada acero galvanizado | 0 | 0.00 | 0.00 |  |
| chapa ondulada galvanizada bricomart | 0 | 0.00 | 0.00 |  |
| chapa ondulada galvanizada verde | 0 | 0.00 | 0.00 |  |
| lamina galvanizada ondulada precio | 0 | 0.00 | 0.00 |  |
| lamina ondulada galvanizada precio | 0 | 0.00 | 0.00 |  |
| medidas chapa galvanizada ondulada | 0 | 0.00 | 0.00 |  |
| medidas chapa ondulada galvanizada | 0 | 0.00 | 0.00 |  |
| plancha galvanizada ondulada | 0 | 0.00 | 0.00 |  |
| planchas onduladas galvanizadas | 0 | 0.00 | 0.00 |  |

## acrilico claraboyas — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 41 phrases, 40 distinct searches, **160 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| acrilico claraboyas | 10 | 0.00 | 0.00 |  |
| casa parisi claraboyas | 10 | 0.00 | 0.00 |  |
| claraboyas a medida | 10 | 0.00 | 0.00 |  |
| claraboyas de acrilico | 10 | 0.00 | 0.00 |  |
| claraboyas de policarbonato | 10 | 0.00 | 0.00 |  |
| claraboyas fijas rectangulares | 10 | 0.00 | 0.00 |  |
| claraboyas luz srl | 10 | 0.00 | 0.00 |  |
| claraboyas modernas | 10 | 0.00 | 0.00 |  |
| claraboyas para motorhome | 10 | 0.00 | 0.00 |  |
| claraboyas para patios de luces | 10 | 0.00 | 0.00 |  |
| claraboyas precios | 10 | 0.00 | 0.00 |  |
| claraboyas redondas | 10 | 0.00 | 0.00 |  |
| claraboyas tubulares | 10 | 0.00 | 0.00 |  |
| lucernarios y claraboyas | 10 | 0.00 | 0.00 |  |
| medidas de claraboyas | 10 | 0.00 | 0.00 |  |
| tecnocril claraboyas | 10 | 0.00 | 0.00 |  |
| bricomart claraboyas | 0 | 0.00 | 0.00 |  |
| claraboyas baratas | 0 | 0.00 | 0.00 |  |
| claraboyas cuadradas | 0 | 0.00 | 0.00 |  |
| claraboyas de metacrilato precios | 0 | 0.00 | 0.00 |  |
| claraboyas de tejado | 0 | 0.00 | 0.00 |  |
| claraboyas en drywall | 0 | 0.00 | 0.00 |  |
| claraboyas en eternit | 0 | 0.00 | 0.00 |  |
| claraboyas en terrazas | 0 | 0.00 | 0.00 |  |
| claraboyas es | 0 | 0.00 | 0.00 |  |
| claraboyas fijas | 0 | 0.00 | 0.00 |  |
| claraboyas fijas precios | 0 | 0.00 | 0.00 |  |
| claraboyas gran canaria | 0 | 0.00 | 0.00 |  |
| claraboyas grandes dimensiones | 0 | 0.00 | 0.00 |  |
| claraboyas las palmas de gran canaria | 0 | 0.00 | 0.00 |  |
| claraboyas matilla | 0 | 0.00 | 0.00 |  |
| claraboyas nauticas | 0 | 0.00 | 0.00 |  |
| claraboyas online | 0 | 0.00 | 0.00 |  |
| claraboyas para patios | 0 | 0.00 | 0.00 |  |
| matilla claraboyas | 0 | 0.00 | 0.00 |  |
| plastico para claraboyas | 0 | 0.00 | 0.00 |  |
| plasticos matilla claraboyas | 0 | 0.00 | 0.00 |  |
| plasticos y claraboyas matilla | 0 | 0.00 | 0.00 |  |
| policarbonato para claraboyas | 0 | 0.00 | 0.00 |  |
| ventilacion claraboyas eternit | 0 | 0.00 | 0.00 |  |

## chapa zinc ondulada — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 14 phrases, 14 distinct searches, **110 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapa zinc ondulada | 20 | 0.00 | 0.00 |  |
| chapa aluminio ondulada | 10 | 0.00 | 0.00 |  |
| chapa aluzinc | 10 | 0.00 | 0.00 |  |
| chapa de acero ondulada | 10 | 0.00 | 0.00 |  |
| chapa de aluminio ondulada | 10 | 0.00 | 0.00 |  |
| chapa de zinc ondulada | 10 | 0.00 | 0.00 |  |
| chapa metalica ondulada | 10 | 0.00 | 0.00 |  |
| chapa ondulada aluminio para fachadas | 10 | 0.00 | 0.00 |  |
| chapa ondulada con estructura metalica | 10 | 0.00 | 0.00 |  |
| chapa ondulada de zinc | 10 | 0.00 | 0.00 |  |
| chapa ondulada zinc medidas | 0 | 0.00 | 0.00 |  |
| cubierta metalica ondulada | 0 | 0.00 | 0.00 |  |
| plancha metalica ondulada | 0 | 0.00 | 0.00 |  |
| precio chapa ondulada aluminio | 0 | 0.00 | 0.00 |  |

## manta térmica para techo — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 1 phrases, 1 distinct searches, **110 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| manta térmica para techo | 110 | 0.00 | 0.00 |  |

## fibro de cemento — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 17 phrases, 17 distinct searches, **110 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| fibro de cemento | 20 | 0.00 | 0.00 |  |
| cemento para tejas | 10 | 0.00 | 0.00 |  |
| precio teja de cemento | 10 | 0.00 | 0.00 |  |
| teja asbesto cemento | 10 | 0.00 | 0.00 |  |
| teja cemento | 10 | 0.00 | 0.00 |  |
| teja de cemento | 10 | 0.00 | 0.00 |  |
| teja de cemento para techo | 10 | 0.00 | 0.00 |  |
| teja de cemento precio | 10 | 0.00 | 0.00 |  |
| teja fibro cemento | 10 | 0.00 | 0.00 |  |
| tejas de fibra de cemento | 10 | 0.00 | 0.00 |  |
| cemento cola para tejas | 0 | 0.00 | 0.00 |  |
| precio de tejas de cemento | 0 | 0.00 | 0.00 |  |
| teja asbesto cemento dimensiones | 0 | 0.00 | 0.00 |  |
| teja de asbesto cemento | 0 | 0.00 | 0.00 |  |
| teja en asbesto cemento | 0 | 0.00 | 0.00 |  |
| tejas asbesto cemento eternit | 0 | 0.00 | 0.00 |  |
| tejas de cemento eternit | 0 | 0.00 | 0.00 |  |

## claraboya baño — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 15 phrases, 15 distinct searches, **110 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| claraboya baño | 10 | 0.00 | 0.00 |  |
| claraboya baño para losa | 10 | 0.00 | 0.00 |  |
| claraboya de baño | 10 | 0.00 | 0.00 |  |
| claraboya para baño | 10 | 0.00 | 0.00 |  |
| claraboya para bano con ventilacion | 10 | 0.00 | 0.00 |  |
| claraboya para baño techo de chapa | 10 | 0.00 | 0.00 |  |
| claraboya para barcos | 10 | 0.00 | 0.00 |  |
| claraboya para cocina | 10 | 0.00 | 0.00 |  |
| claraboya para techo de baño | 10 | 0.00 | 0.00 |  |
| claraboya techo baño | 10 | 0.00 | 0.00 |  |
| claraboyas para techos de baños | 10 | 0.00 | 0.00 |  |
| claraboya para baño precio | 0 | 0.00 | 0.00 |  |
| claraboyas para baños con extractor | 0 | 0.00 | 0.00 |  |
| precio claraboya para baño | 0 | 0.00 | 0.00 |  |
| precio de claraboya para baño | 0 | 0.00 | 0.00 |  |

## tejuela ceramica — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 15 phrases, 15 distinct searches, **100 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| tejuela ceramica | 50 | 0.00 | 0.00 |  |
| teja ceramica curva | 10 | 0.00 | 0.00 |  |
| teja ceramica curva precio | 10 | 0.00 | 0.00 |  |
| teja concreto | 10 | 0.00 | 0.00 |  |
| teja termoacustica precio m2 | 10 | 0.00 | 0.00 |  |
| teja trapezoidal termoacustica | 10 | 0.00 | 0.00 |  |
| precio teja ceramica curva | 0 | 0.00 | 0.00 |  |
| precio teja curva 40x15 | 0 | 0.00 | 0.00 |  |
| precio teja verea | 0 | 0.00 | 0.00 |  |
| precio teja verea 40x15 | 0 | 0.00 | 0.00 |  |
| precio teja verea curva | 0 | 0.00 | 0.00 |  |
| teja asfaltica easy precio | 0 | 0.00 | 0.00 |  |
| tejas verea precio | 0 | 0.00 | 0.00 |  |
| tejas verea precios | 0 | 0.00 | 0.00 |  |
| verea teja | 0 | 0.00 | 0.00 |  |

## tirafondo para chapa — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 4 phrases, 4 distinct searches, **90 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| tirafondo para chapa | 70 | 0.00 | 0.00 |  |
| bulones para chapa | 10 | 0.00 | 0.00 |  |
| tirafondos rosca chapa | 10 | 0.00 | 0.00 |  |
| tornillos tirafondo para chapas | 0 | 0.00 | 0.00 |  |

## claraboya de chapa — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 32 phrases, 32 distinct searches, **70 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| claraboya de chapa | 10 | 0.00 | 0.00 |  |
| claraboya electrica | 10 | 0.00 | 0.00 |  |
| claraboya monovalva | 10 | 0.00 | 0.00 |  |
| claraboya nautica | 10 | 0.00 | 0.00 |  |
| claraboya para terraza | 10 | 0.00 | 0.00 |  |
| claraboya telescopica | 10 | 0.00 | 0.00 |  |
| claraboya tubular | 10 | 0.00 | 0.00 |  |
| claraboia quadrada | 0 | 0.00 | 0.00 |  |
| claraboya 100x100 | 0 | 0.00 | 0.00 |  |
| claraboya 120x120 | 0 | 0.00 | 0.00 |  |
| claraboya 40x40 amazon | 0 | 0.00 | 0.00 |  |
| claraboya 60x60 | 0 | 0.00 | 0.00 |  |
| claraboya 70x70 | 0 | 0.00 | 0.00 |  |
| claraboya 80x80 | 0 | 0.00 | 0.00 |  |
| claraboya acceso cubierta | 0 | 0.00 | 0.00 |  |
| claraboya apertura electrica | 0 | 0.00 | 0.00 |  |
| claraboya apertura telescopica | 0 | 0.00 | 0.00 |  |
| claraboya carbest 40x40 | 0 | 0.00 | 0.00 |  |
| claraboya carbest 70x50 | 0 | 0.00 | 0.00 |  |
| claraboya chapa trapezoidal | 0 | 0.00 | 0.00 |  |
| claraboya cuadrada | 0 | 0.00 | 0.00 |  |
| claraboya cubierta | 0 | 0.00 | 0.00 |  |
| claraboya cubierta inclinada | 0 | 0.00 | 0.00 |  |
| claraboya motorizada | 0 | 0.00 | 0.00 |  |
| claraboya panoramica | 0 | 0.00 | 0.00 |  |
| claraboya para chapa | 0 | 0.00 | 0.00 |  |
| claraboya para chapa acanalada | 0 | 0.00 | 0.00 |  |
| claraboya parabolica | 0 | 0.00 | 0.00 |  |
| claraboya salida a cubierta | 0 | 0.00 | 0.00 |  |
| claraboya terraza | 0 | 0.00 | 0.00 |  |
| claraboya tf40 | 0 | 0.00 | 0.00 |  |
| claraboya ventilada | 0 | 0.00 | 0.00 |  |

## chapa de tornillo — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 15 phrases, 15 distinct searches, **70 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapa de tornillo | 10 | 0.00 | 0.00 |  |
| tornillo autorroscante panel sandwich | 10 | 0.00 | 0.00 |  |
| tornillo chapa | 10 | 0.00 | 0.00 |  |
| tornillo chapa chapa | 10 | 0.00 | 0.00 |  |
| tornillos chapa sandwich | 10 | 0.00 | 0.00 |  |
| tornillos chapa trapezoidal | 10 | 0.00 | 0.00 |  |
| tornillos para chapa sandwich | 10 | 0.00 | 0.00 |  |
| tornillo autorroscante para panel sandwich | 0 | 0.00 | 0.00 |  |
| tornillo chapa chapa pladur | 0 | 0.00 | 0.00 |  |
| tornillo chapa metalica | 0 | 0.00 | 0.00 |  |
| tornillos autotaladrantes panel sandwich | 0 | 0.00 | 0.00 |  |
| tornillos autotaladrantes para panel sandwich | 0 | 0.00 | 0.00 |  |
| tornillos panel sandwich cubierta | 0 | 0.00 | 0.00 |  |
| tornillos para panel sandwich teja | 0 | 0.00 | 0.00 |  |
| tornillos techo chapa | 0 | 0.00 | 0.00 |  |

## chapas metalicas onduladas — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 15 phrases, 15 distinct searches, **60 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapas metalicas onduladas | 10 | 0.00 | 0.00 |  |
| chapas onduladas de plastico | 10 | 0.00 | 0.00 |  |
| chapas onduladas de segunda mano | 10 | 0.00 | 0.00 |  |
| chapas onduladas para techos | 10 | 0.00 | 0.00 |  |
| chapas onduladas para tejados | 10 | 0.00 | 0.00 |  |
| chapas onduladas usadas | 10 | 0.00 | 0.00 |  |
| chapas onduladas baratas | 0 | 0.00 | 0.00 |  |
| chapas onduladas bricomart | 0 | 0.00 | 0.00 |  |
| chapas onduladas galvanizadas de ocasion | 0 | 0.00 | 0.00 |  |
| chapas onduladas metalicas | 0 | 0.00 | 0.00 |  |
| chapas onduladas para tejados precios | 0 | 0.00 | 0.00 |  |
| chapas onduladas para tejados segunda mano | 0 | 0.00 | 0.00 |  |
| plancha ondulada plastico para techos | 0 | 0.00 | 0.00 |  |
| plancha ondulada tejado | 0 | 0.00 | 0.00 |  |
| tejados ondulados para cubiertas | 0 | 0.00 | 0.00 |  |

## chapa blanca prepintada — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 9 phrases, 9 distinct searches, **60 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapa blanca prepintada | 10 | 0.00 | 0.00 |  |
| chapa lisa prepintada | 10 | 0.00 | 0.00 |  |
| chapa lisa prepintada blanca | 10 | 0.00 | 0.00 |  |
| chapa lisa prepintada negra | 10 | 0.00 | 0.00 |  |
| chapa prepintada blanca | 10 | 0.00 | 0.00 |  |
| chapa prepintada lisa | 10 | 0.00 | 0.00 |  |
| chapa lisa easy | 0 | 0.00 | 0.00 |  |
| chapa lisa prepintada colores | 0 | 0.00 | 0.00 |  |
| chapa prepintada negra lisa precio | 0 | 0.00 | 0.00 |  |

## precio teja mixta — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 26 phrases, 26 distinct searches, **50 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| precio teja mixta | 10 | 0.00 | 0.00 |  |
| precio tezontle | 10 | 0.00 | 0.00 |  |
| teja ceramica mixta | 10 | 0.00 | 0.00 |  |
| teja mixta | 10 | 0.00 | 0.00 |  |
| teja mixta roja | 10 | 0.00 | 0.00 |  |
| precio de la teja mixta | 0 | 0.00 | 0.00 |  |
| precio de teja mixta roja | 0 | 0.00 | 0.00 |  |
| precio teja ceramica mixta | 0 | 0.00 | 0.00 |  |
| precio teja ceramica mixta roja | 0 | 0.00 | 0.00 |  |
| precio teja mixta ceramica | 0 | 0.00 | 0.00 |  |
| precio teja mixta roja | 0 | 0.00 | 0.00 |  |
| remate lateral teja mixta bricomart | 0 | 0.00 | 0.00 |  |
| teja arabe mixta | 0 | 0.00 | 0.00 |  |
| teja borja tb12 precio | 0 | 0.00 | 0.00 |  |
| teja ceramica mixta roja | 0 | 0.00 | 0.00 |  |
| teja cobert mixta | 0 | 0.00 | 0.00 |  |
| teja hdr mixta | 0 | 0.00 | 0.00 |  |
| teja mixta 43x26 | 0 | 0.00 | 0.00 |  |
| teja mixta borja tb 12 | 0 | 0.00 | 0.00 |  |
| teja mixta cobert | 0 | 0.00 | 0.00 |  |
| teja mixta colores | 0 | 0.00 | 0.00 |  |
| teja mixta escandella precio | 0 | 0.00 | 0.00 |  |
| teja mixta gris | 0 | 0.00 | 0.00 |  |
| teja mixta hdr | 0 | 0.00 | 0.00 |  |
| teja mixta hdr precio | 0 | 0.00 | 0.00 |  |
| teja mixta roja precio | 0 | 0.00 | 0.00 |  |

## mazarron tejas — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 12 phrases, 12 distinct searches, **40 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| mazarron tejas | 10 | 0.00 | 0.00 |  |
| teja alicantina | 10 | 0.00 | 0.00 |  |
| tejas alicantina | 10 | 0.00 | 0.00 |  |
| tejas de hormigon | 10 | 0.00 | 0.00 |  |
| escandella tejas | 0 | 0.00 | 0.00 |  |
| hdr tejas | 0 | 0.00 | 0.00 |  |
| la escandella tejas | 0 | 0.00 | 0.00 |  |
| teja alicantina bricomart | 0 | 0.00 | 0.00 |  |
| teja la escandella | 0 | 0.00 | 0.00 |  |
| tejas borja modelos | 0 | 0.00 | 0.00 |  |
| tejas la escandella | 0 | 0.00 | 0.00 |  |
| tejas la escandella precios | 0 | 0.00 | 0.00 |  |

## teja ceramica plana — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 5 phrases, 5 distinct searches, **40 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| teja ceramica plana | 10 | 0.00 | 0.00 |  |
| teja de ceramica plana | 10 | 0.00 | 0.00 |  |
| tejas de cemento planas | 10 | 0.00 | 0.00 |  |
| tejas de cemento usadas | 10 | 0.00 | 0.00 |  |
| permanit ceramic base | 0 | 0.00 | 0.00 |  |

## chapa acanalada minionda — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 22 phrases, 22 distinct searches, **40 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapa acanalada minionda | 10 | 0.00 | 0.00 |  |
| chapa microondulada | 10 | 0.00 | 0.00 |  |
| chapa minionda | 10 | 0.00 | 0.00 |  |
| chapa minionda perforada | 10 | 0.00 | 0.00 |  |
| chapa aluminio minionda | 0 | 0.00 | 0.00 |  |
| chapa microperforada ondulada | 0 | 0.00 | 0.00 |  |
| chapa minionda aluminio precio | 0 | 0.00 | 0.00 |  |
| chapa minionda colores | 0 | 0.00 | 0.00 |  |
| chapa minionda galvanizada | 0 | 0.00 | 0.00 |  |
| chapa minionda galvanizada precio | 0 | 0.00 | 0.00 |  |
| chapa minionda lacada | 0 | 0.00 | 0.00 |  |
| chapa minionda microperforada | 0 | 0.00 | 0.00 |  |
| chapa minionda microperforada precio | 0 | 0.00 | 0.00 |  |
| chapa minionda para fachadas | 0 | 0.00 | 0.00 |  |
| chapa minionda perforada precio | 0 | 0.00 | 0.00 |  |
| chapa minionda precio | 0 | 0.00 | 0.00 |  |
| chapa minionda translucida | 0 | 0.00 | 0.00 |  |
| chapa minionda transparente | 0 | 0.00 | 0.00 |  |
| chapa ondulada microperforada precio | 0 | 0.00 | 0.00 |  |
| chapa ondulada minionda | 0 | 0.00 | 0.00 |  |
| cubierta chapa minionda | 0 | 0.00 | 0.00 |  |
| minionda galvanizada | 0 | 0.00 | 0.00 |  |

## claraboya de pared — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 6 phrases, 6 distinct searches, **40 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| claraboya de pared | 10 | 0.00 | 0.00 |  |
| claraboya para pared | 10 | 0.00 | 0.00 |  |
| claraboya pared | 10 | 0.00 | 0.00 |  |
| escalera claraboya | 10 | 0.00 | 0.00 |  |
| claraboya con escalera | 0 | 0.00 | 0.00 |  |
| claraboya escalera | 0 | 0.00 | 0.00 |  |

## claraboya plana — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 9 phrases, 9 distinct searches, **30 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| claraboya plana | 10 | 0.00 | 0.00 |  |
| claraboya techo plano | 10 | 0.00 | 0.00 |  |
| claraboyas velux | 10 | 0.00 | 0.00 |  |
| claraboya cubierta plana | 0 | 0.00 | 0.00 |  |
| claraboya tejado plano | 0 | 0.00 | 0.00 |  |
| claraboya tejado velux | 0 | 0.00 | 0.00 |  |
| claraboya velux cubierta plana | 0 | 0.00 | 0.00 |  |
| claraboya velux medidas | 0 | 0.00 | 0.00 |  |
| claraboyas velux precios | 0 | 0.00 | 0.00 |  |

## chapa ondulada de policarbonato transparente — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 10 phrases, 10 distinct searches, **30 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapa ondulada de policarbonato transparente | 10 | 0.00 | 0.00 |  |
| placas onduladas policarbonato | 10 | 0.00 | 0.00 |  |
| placas onduladas transparentes | 10 | 0.00 | 0.00 |  |
| chapa ondulada policarbonato transparente | 0 | 0.00 | 0.00 |  |
| placa poliéster ondulada precio | 0 | 0.00 | 0.00 |  |
| placas de policarbonato onduladas | 0 | 0.00 | 0.00 |  |
| placas onduladas de chapa | 0 | 0.00 | 0.00 |  |
| placas onduladas para cubiertas | 0 | 0.00 | 0.00 |  |
| placas onduladas para tejados | 0 | 0.00 | 0.00 |  |
| placas policarbonato ondulado precios | 0 | 0.00 | 0.00 |  |

## claraboya abatible — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 10 phrases, 10 distinct searches, **30 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| claraboya abatible | 10 | 0.00 | 0.00 |  |
| claraboya easy | 10 | 0.00 | 0.00 |  |
| claraboya insoluz | 10 | 0.00 | 0.00 |  |
| claraboia automatica | 0 | 0.00 | 0.00 |  |
| claraboya apertura manual | 0 | 0.00 | 0.00 |  |
| claraboya automatica | 0 | 0.00 | 0.00 |  |
| claraboya para bano easy | 0 | 0.00 | 0.00 |  |
| claraboya transparente | 0 | 0.00 | 0.00 |  |
| easy claraboya | 0 | 0.00 | 0.00 |  |
| internit para ceramica easy | 0 | 0.00 | 0.00 |  |

## tornillo din 7981 — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 2 phrases, 2 distinct searches, **20 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| tornillo din 7981 | 10 | 0.00 | 0.00 |  |
| tornillo din 7982 | 10 | 0.00 | 0.00 |  |

## claraboya practicable — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 9 phrases, 9 distinct searches, **20 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| claraboya practicable | 10 | 0.00 | 0.00 |  |
| claraboyas practicables | 10 | 0.00 | 0.00 |  |
| claraboya con escalera escamoteable | 0 | 0.00 | 0.00 |  |
| claraboya cristal transitable | 0 | 0.00 | 0.00 |  |
| claraboya pisable | 0 | 0.00 | 0.00 |  |
| claraboya transitable | 0 | 0.00 | 0.00 |  |
| claraboya transitable precio | 0 | 0.00 | 0.00 |  |
| claraboya vidrio transitable | 0 | 0.00 | 0.00 |  |
| claraboyas para terrazas transitables | 0 | 0.00 | 0.00 |  |

## teja esmaltada negra — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 9 phrases, 9 distinct searches, **20 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| teja esmaltada negra | 10 | 0.00 | 0.00 |  |
| tejas esmaltadas azules | 10 | 0.00 | 0.00 |  |
| acrilicosgascon1027 | 0 | 0.00 | 0.00 |  |
| plasticos matillas | 0 | 0.00 | 0.00 |  |
| teja mixta envejecida bricomart | 0 | 0.00 | 0.00 |  |
| teja portuguesa envejecida | 0 | 0.00 | 0.00 |  |
| tejas barros y azulejos | 0 | 0.00 | 0.00 |  |
| tejas mixtas envejecidas | 0 | 0.00 | 0.00 |  |
| tejas pisos y azulejos | 0 | 0.00 | 0.00 |  |

## chapa t101 negra — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 4 phrases, 4 distinct searches, **10 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| chapa t101 negra | 10 | 0.00 | 0.00 |  |
| chapa acanalada negra c25 | 0 | 0.00 | 0.00 |  |
| chapa prepintada t101 | 0 | 0.00 | 0.00 |  |
| chapa t101 prepintada | 0 | 0.00 | 0.00 |  |

## limahoya que es — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 2 phrases, 2 distinct searches, **10 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| limahoya que es | 10 | 0.00 | 0.00 |  |
| limahoya definicion | 0 | 0.00 | 0.00 |  |

## lucernario transitable — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 3 phrases, 3 distinct searches, **10 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| lucernario transitable | 10 | 0.00 | 0.00 |  |
| lucernario transitable precio | 0 | 0.00 | 0.00 |  |
| lucernarios pisables | 0 | 0.00 | 0.00 |  |

## pernos para chapas — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 1 phrases, 1 distinct searches, **10 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| pernos para chapas | 10 | 0.00 | 0.00 |  |

## claraboyas resopal — materiales.com.py

- Meaning group from Keyword Library · data location **Paraguay** · CPC currency: SEK
- 1 phrases, 1 distinct searches, **0 searches/mo (deduplicated)**.
- Phrases in the last column have exactly the same numbers as the first: Keyword Planner reports them as one search, so the volume counts once.

| Phrase | Searches/mo | Low CPC | High CPC | Same numbers (Google groups them) |
| --- | --- | --- | --- | --- |
| claraboyas resopal | 0 | 0.00 | 0.00 |  |
