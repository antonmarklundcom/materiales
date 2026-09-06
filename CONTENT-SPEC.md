# CONTENT-SPEC — materiales.com.py

Copy **cerrado** de la fase 3. Todo lo que está entre comillas acá va tal cual al sitio.
Regla de esta fase: si una fase posterior tiene que *decidir* una redacción, esta fase
falló — se corrige acá, no en la plantilla.

Reglas que gobiernan todo el documento:

- Español paraguayo, **voseo** en cada CTA ("pedí", "contanos", "cargá", "mirá").
- **Sin precios publicados** (plan §8.7). Nunca "desde X Gs.", nunca rangos visibles.
  `price_band` en `data/materials.php` es interno: alimenta bandas de presupuesto y futuros
  calculadores, no se renderiza.
- **Sin cifras inventadas**: nada de "+500 clientes", "20 años", "4,9 estrellas", cantidad de
  proveedores del rubro ni plazos que no controlamos. Lo único cuantificado es "hasta 3
  proveedores verificados", que es decisión nuestra (plan §1.8) y sale de
  `data/site.php` → `max_proveedores`.
- Un H1 por página. Los H2 salen de esta especificación.
- Sinónimos: se usan **dentro** del texto y de las FAQ; nunca son página propia.
- Toda FAQ que se emita como `FAQPage` tiene que estar **visible** en la página. El router
  de `/materiales/` ya las renderiza en `<details>`: si agregás FAQ a un dato, se muestran.

Dónde vive cada cosa: los textos de plantilla ya están implementados en los archivos que se
indican. Cambiar el texto = editar ese archivo, no duplicarlo.

---

## 1. Home (`public_html/index.php`)

| Elemento | Copy |
|---|---|
| `<title>` | `Materiales de construcción en Paraguay \| Cotizá gratis` |
| meta | `Pedí cotización de hierro, cemento, arena, ladrillos o chapas y hasta 3 proveedores verificados de Gran Asunción te escriben por WhatsApp.` |
| H1 | `Cotizá materiales de construcción en Paraguay` |
| Bajada | `Decinos qué material necesitás, cuánto y para qué zona. Hasta 3 proveedores verificados te pasan precio por WhatsApp, normalmente dentro del día.` |
| CTA | `Pedí tu cotización` → `/cotizar/` |
| H2 | `Rubros` (grilla de categorías; las `proxima` van en gris con `.is-proxima`) |
| Cierre | `Ver todos los materiales` → `/materiales/` |

Sin sección de testimonios, clientes ni años de experiencia: no hay datos reales y el plan
prohíbe inventarlos.

## 2. Índice `/materiales/` (`public_html/materiales/_index.php`)

`<title>`: `Materiales de construcción en Paraguay | Cotizá gratis` ·
meta: `Hierro, cemento, áridos, ladrillos, chapas y más. Elegí el material y pedí cotización: hasta 3 proveedores verificados te escriben.` ·
H1: `Materiales de construcción en Paraguay` ·
bajada: `Elegí el rubro que necesitás y pedí tu cotización en un paso.`

## 3. Plantilla de categoría y de material (`public_html/materiales/index.php`)

Orden fijo, igual para los dos tipos:

1. H1 = `name` del dato.
2. Bajada = `intro` del dato.
3. Sólo material: `Se vende por: {sale_unit}`.
4. Prosa: `content/categorias/{slug}.php` o `content/materiales/{slug}.php` (§5). Si no
   existe todavía, se muestra el aviso ya escrito en la plantilla — no se inventa relleno.
5. Categoría: H2 `Materiales de {categoría}` + grilla. Material: enlace
   `Ver toda la categoría {categoría}`.
6. H2 `Preguntas frecuentes` (material: `Preguntas frecuentes sobre {material}`) con las FAQ
   del dato — son las que alimentan el `FAQPage`.
7. Sólo material: H2 `También te puede servir` + `related[]` publicados.
8. Formulario, con el slug de la página preseleccionado.

`title` y `meta` de cada categoría y material ya están escritos en `data/*.php` (fase 1,
completados en fase 3). **No se reescriben** en fases posteriores.

Única excepción, ya ejecutada: la fase 5c reescribió `title`, `meta`, `intro`,
`intro_keywords` y `faq` de las **seis categorías promovidas** (madera, pisos-y-revestimientos,
aberturas, impermeabilizantes, yeso-y-durlock, canos-y-plomeria), porque se habían escrito en
la fase 3 sin saber qué materiales iban a colgar de ellas ni qué términos iban a poseer
(CONTENT-SPEC §11.1). Las cinco categorías de lanzamiento no se tocaron. A partir de acá, esos
textos también están cerrados.

## 4. Vocabulario obligatorio

Cada material tiene `synonyms[]` con el vocabulario real de obra paraguaya y cada categoría
`intro_keywords[]`. La prosa tiene que usarlos de forma natural — son la razón por la que
estas páginas rankean: "piedra bruta" también se pide como *piedra para cimiento*; la
triturada, como *4ta*, *5ta* o *6ta*; el isopanel es *panel sándwich*; el ladrillo hueco es
*ladrillo de 8* o *de 12*. Ninguno de esos términos recibe página propia.

## 5. Cuerpo de prosa — estructura obligatoria

**Material** (`content/materiales/{slug}.php`), 350–600 palabras, en este orden:

- H2 `Para qué se usa` — usos reales en obra paraguaya; acá entran los sinónimos.
- H2 `Cómo se vende, cómo pedirlo y de qué depende el precio` — unidad de venta, qué datos
  necesita el proveedor para cotizar (cantidad, medida, zona), qué se cotiza aparte (flete,
  accesorios) y qué factores mueven el precio. **Nunca un número**: los factores, no la cifra
  (§11.3).
- H2 `Qué mirar antes de comprar` — criterios de calidad y errores comunes.
- Cierre de una línea enlazando a la categoría madre y a 1–2 materiales relacionados, con
  texto ancla descriptivo (nunca "hacé clic acá").

**Categoría** (`content/categorias/{slug}.php`), 250–450 palabras:

- H2 `Qué incluye esta categoría` — a qué material va cada necesidad, enlazando cada uno con
  su nombre como ancla.
- H2 `Cómo se cotiza en Paraguay` — unidades, flete, zona, qué conviene pedir junto.

Prohibido en ambas: precios, plazos concretos de entrega, marcas o nombres de proveedores, y
cualquier afirmación numérica que no salga del propio dato.

## 6. Guías — esquemas cerrados (`content/guias/{slug}.php`)

H1, title, meta y `related` ya están en `data/guides.php`. 600–900 palabras. Cada guía cierra
enlazando a sus páginas de dinero con ancla descriptiva y con el CTA `Pedí tu cotización`.

| Guía | H2 | Enlaces obligatorios (ancla → destino) |
|---|---|---|
| `cuantas-bolsas-de-cemento-por-m2` | `De qué depende el cálculo` · `Contrapiso, revoque y mampostería` · `Cómo pedir la cantidad al proveedor` | `cemento en bolsa` → /materiales/cemento/ · `arena lavada` → /materiales/arena-lavada/ · `ripio` → /materiales/ripio/ |
| `que-piedra-usar-para-cimientos` | `Qué hace cada piedra` · `Piedra bruta en cimientos corridos` · `Cómo calcular el volumen` | `piedra bruta` → /materiales/piedra-bruta/ · `piedra triturada` → /materiales/piedra-triturada/ · `áridos` → /materiales/aridos/ |
| `ladrillo-comun-vs-hueco` | `Peso, aislación y velocidad de obra` · `Cuándo va cada uno` · `Qué preguntar antes de comprar` | `ladrillo común` → /materiales/ladrillo-comun/ · `ladrillo hueco` → /materiales/ladrillo-hueco/ · `ladrillos y bloques` → /materiales/ladrillos-y-bloques/ |
| `que-chapa-conviene-para-techo` | `Calor y ruido` · `Luz a cubrir y estructura` · `Accesorios que se cotizan aparte` | `chapa de zinc` → /materiales/chapa-de-zinc/ · `chapa trapezoidal` → /materiales/chapa-trapezoidal/ · `chapa termoacústica` → /materiales/chapa-termoacustica/ |
| `cuanta-arena-y-ripio-por-m3-de-hormigon` | `Cómo se compone un metro cúbico` · `Qué pedir de cada árido` · `Cuándo conviene hormigón elaborado` | `arena lavada` → /materiales/arena-lavada/ · `ripio` → /materiales/ripio/ · `hormigón elaborado` → /materiales/hormigon-elaborado/ |
| `que-diametro-de-hierro-para-que-uso` | `Qué define el diámetro` · `Columnas, vigas, losas y estribos` · `Qué datos necesita el proveedor` | `varilla de hierro` → /materiales/varilla-de-hierro/ · `malla electrosoldada` → /materiales/malla-electrosoldada/ · `hierro` → /materiales/hierro/ |

Ninguna guía afirma una dosificación como verdad universal: siempre "depende de la
dosificación que uses / consultá al proveedor o al calculista".

## 7. Formulario (`public_html/partials/form.php`) — microcopy cerrado

| Elemento | Copy |
|---|---|
| H2 | `Cotizá {nombre de la página}` (en `/cotizar/`: `Pedí tu cotización`) |
| Bajada | `Hasta 3 proveedores verificados te escriben por WhatsApp con su precio. Es gratis y sin compromiso.` |
| Material | `¿Qué material necesitás?` · vacío: `Elegí el material (o dejalo en blanco si no estás seguro)` · por categoría: `{categoría} (toda la categoría)` |
| Cantidad | `¿Qué cantidad?` · placeholder `Ej: 30 bolsas, 2 camiones, 500 kg` |
| Ciudad | `¿En qué ciudad o zona?` · placeholder `Ej: Luque, Asunción, San Lorenzo` |
| Nombre | `Tu nombre` |
| Teléfono | `Tu WhatsApp (por acá te pasan el precio)` · placeholder `0981 123 456` |
| Mensaje | `¿Algo más que tengan que saber? (opcional)` |
| Botón | `Pedir cotización` |
| Nota | `Sin costo. No publicamos tu teléfono en ningún lado.` |
| Error teléfono | `Revisá el teléfono: necesitamos un número paraguayo, por ejemplo 0981 123 456.` |
| Error consentimiento | `Para poder pasarle tu pedido a los proveedores necesitamos que marques la casilla.` |

**Frase de consentimiento — literal, no se toca** (plan §8.6; cambiarla es una parada §4.4 y
además rompe el test que la fija en `tools/smoke.php`):

> Acepto que mis datos sean compartidos con proveedores del rubro para recibir cotizaciones.
> Ver la [Política de privacidad](/politica-de-privacidad/).

Casilla **desmarcada** y obligatoria. Versión guardada en el CRM: `proveedores-v1`
(`data/site.php` → `consent_version`). Si cambia el texto, cambia la versión.

## 8. `/cotizar/` y `/gracias/`

`/cotizar/`: title `Pedí cotización de materiales | Materiales.com.py` · meta
`Cargá qué material necesitás, cuánto y en qué zona. Hasta 3 proveedores verificados te pasan
precio por WhatsApp, normalmente en el día.` · H1 `Pedí tu cotización`.

`/gracias/` (noindex): title `Recibimos tu pedido de cotización` · H1
`Listo, recibimos tu pedido` · bajada `Hasta 3 proveedores verificados te van a escribir por
WhatsApp, normalmente dentro del día.` · enlaces `Volver a {material}` y `Ver otros materiales`.

## 9. Pie y CTA secundario

Pie: razón social, RUC, condición IVA, dirección, horarios, WhatsApp, teléfono, correo,
navegación y zonas de cobertura — **cada dato se muestra sólo si existe** en `data/site.php`.
Nunca se inventa ni queda un placeholder visible.

CTA primario en todo el sitio: el formulario (plan §6) — la reventa del pedido necesita
campos estructurados y consentimiento explícito, y un chat no los da. WhatsApp queda como
secundario y sólo aparece cuando el número real esté cargado.

## 10. Lo que las fases Sonnet NO pueden cambiar

Router, esquemas de los archivos de datos, contrato del handler de leads, taxonomía de URLs y
el texto de consentimiento (plan §4.7). Tampoco los `title`/`meta` ya escritos en `data/*.php`
ni las frases de este documento. Si algo no se puede ejecutar tal cual: workaround + nota en
`KNOWN-ISSUES.md`.

## 11. Propiedad de keywords y vocabulario (fase 5a)

Fuente: `KEYWORDS-MATERIALES.md` §1, §2, §3.4 y §5. Esta sección cierra **qué término es de
qué página**. Vale para toda la prosa de las fases 6 y 7: si un término figura acá en la fila
de otra página, no se usa como H2 ni como frase de apertura en la tuya; como mucho aparece de
paso, enlazando a su dueño.

Regla madre: **un término de cabecera pertenece a exactamente una página.** Las variantes de
plural, acento y grafía del mismo término son sinónimos de esa misma página, nunca páginas ni
H2 propios (Google ya las agrupa: `cerámica`/`ceramica`/`cerámicos` comparten volumen, igual
que las cuatro grafías de `malla electrosoldada`). Los volúmenes entre paréntesis son
búsquedas mensuales de Keyword Planner Paraguay, de-duplicadas.

### 11.1 Tabla de propiedad — una fila por página `activa` después de 5c

Columnas: **Posee** = términos de cabecera que esta página persigue (H1/intro/H2 propios) ·
**Teje** = sinónimos y variantes que la prosa usa dentro del texto y de las FAQ ·
**No es suya** = términos cercanos que pertenecen a otra página (enlazar, no perseguir).

#### Hierro

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `hierro` (categoría) | hierro (1.600) · hierro de construcción · acero (720) | fierro, fierro de construcción, hierro para obra | perfiles → `perfiles-metalicos` · varillas → `varilla-de-hierro` · perfiles de aluminio → `ventanas-de-aluminio` |
| `varilla-de-hierro` | varilla de hierro · varillas (480) · varilla de 8 (320) · varillas de hierro (260) · varilla conformada (140) | hierro nervurado, barra de hierro, hierro de 8/10/12, estribo | hierro a secas → categoría · hormigón armado → guía `losa-de-hormigon-encofrado-y-hierro` |
| `malla-electrosoldada` | malla electrosoldada (590, 4 grafías) · malla (880) · malla metálica (390) | malla sima, malla para contrapiso, electro soldada, microsoldada, termosoldada, 6 6 10 10 | malla para cerco / tejido → `tejido-de-alambre` |
| `alambre-negro` | alambre (720) · alambre dulce (390) | alambre recocido, alambre de atar, alambre para atar armadura | alambre galvanizado y alambre de púas → `tejido-de-alambre` |
| `tejido-de-alambre` | tejido de alambre (1.300) · alambre de púas (480) · alambre galvanizado (210) · alambre tejido (90) | tejido romboidal, alambrado, malla para cerco, cerco perimetral, poste de alambrado | malla de obra → `malla-electrosoldada` |
| `perfiles-metalicos` | perfiles (4.400) · perfiles C / en C (480) · perfiles en U (390) · perfiles de hierro (260) · ángulo de hierro (210) · perfiles IPN (170) · perfiles UPN (140) · viga de hierro (170) | planchuela, caño estructural, tubo estructural, perfilería, correa de galpón | perfiles de aluminio (480) y aluminio (590) → `ventanas-de-aluminio` · montante y solera → `perfiles-para-durlock` |
| `clavos` | clavos (90) · clavo de acero | clavo de albañil, clavo punta parís | tornillos y autoperforantes → bucket Productos, sin página acá |

#### Cemento y cal

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `cemento-y-cal` (categoría) | cal (1.600) · cal para construcción · cemento y cal | cal de obra, cal para revoque y pintura a la cal | cemento → `cemento` · hormigón → `hormigon-elaborado` · cal agrícola / cal dolomita (agro): ninguna página las persigue |
| `cemento` | cemento (1.000) · cemento precio (320) · cemento blanco (320) · cemento gris (320) · mortero (720, como H2 `Mortero premezclado`) | bolsa de cemento, cemento portland, cemento de albañilería, mortero premezclado | hormigón / concreto → `hormigon-elaborado` · cal a secas → categoría |
| `cal-viva` | cal viva · cal en terrón | cal en piedra, cal para apagar | cal hidratada / apagada → `cal-hidratada` |
| `cal-hidratada` | cal hidratada (320) · cal apagada | cal en bolsa, cal para revoque, cal fina | cal a secas → categoría |
| `hormigon-elaborado` | hormigón (880) · concreto (320) · hormigón elaborado (70) | premezclado, hormigón premezclado, mixer, m³ de hormigón, bombeado | hormigón armado, encofrado, losas y zapatas → guía `losa-de-hormigon-encofrado-y-hierro` · hormigón pulido / impreso: intención de servicio, ninguna página |
| `adhesivo-para-ceramica` | adhesivo (880) · pegamento para cerámica · pegamento para porcelanato (120) · pastina | adhesivo en polvo, cola para cerámica, pastina para juntas | cerámica y porcelanato como producto → `ceramica-para-piso`, `porcelanato` |

#### Áridos

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `aridos` (categoría) | áridos · arena y ripio · camionada · piedra (880) | metro cúbico, m³, volquete, camión de áridos | arena → `arena-lavada` · ripio y canto rodado → `ripio` · triturada → `piedra-triturada` |
| `arena-lavada` | arena (880) · arena fina (110) · arena lavada | arena de río, arena para revoque, arena cernida | arena gruesa / gorda → `arena-gorda` |
| `arena-gorda` | arena gruesa (40) · arena gorda | arena de construcción, arena para contrapiso, arena sucia | arena a secas y arena fina → `arena-lavada` |
| `ripio` | ripio (590) · canto rodado (1.000) · gravas (320) | grava, piedra redonda; **la intro abre con "canto rodado"** (§5.1 del keyword research) | piedra 4ta/5ta/6ta y basalto → `piedra-triturada` |
| `piedra-triturada` | piedra triturada (260) · piedra 4ta · piedra 5ta · piedra 6ta | sexta, basalto, piedra partida, triturada de basalto (tabla de granulometrías) | canto rodado → `ripio` · piedra de cimiento → `piedra-bruta` |
| `piedra-bruta` | piedra bruta · piedra para cimiento | piedra de cimiento, piedra en bruto, piedra para muro de contención | triturada → `piedra-triturada` |
| `tierra-gorda` | tierra colorada (1.300) · tosca (210) · tierra para relleno | tierra negra, tierra de relleno, camionada de tierra | escombro → `escombro-relleno` |
| `escombro-relleno` | escombro · escombro para relleno · relleno | material de relleno, escombro limpio | tierra colorada → `tierra-gorda` |

#### Ladrillos y bloques

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `ladrillos-y-bloques` (categoría) | ladrillo (720) · ladrillos · bloques (590) | millar, mampostería, pared de ladrillo | ladrillo visto → `ladrillo-prensado` · ladrillo hueco → `ladrillo-hueco` · bloque de cemento → `bloque-de-hormigon` |
| `ladrillo-comun` | ladrillo común (390) · ladrillo común medidas (210) | ladrillo rústico, ladrillo de campo, ladrillo macizo, ladrillo de olería (tabla de medidas) | ladrillo visto / prensado → `ladrillo-prensado` |
| `ladrillo-hueco` | ladrillo hueco (1.000) · ladrillo hueco medida (170) | ladrillo de 8, ladrillo de 12, ladrillo de 18, hueco de 6 (tabla de medidas) | ladrillo macizo → `ladrillo-comun` · **`ladrillo sapo` (260) queda sin dueño**: es demanda paraguaya real, pero no está verificado si nombra al hueco o a un macizo grande, y adjudicarlo mal ensucia la página (KNOWN-ISSUES) |
| `ladrillo-prensado` | ladrillo prensado (390) · ladrillo visto (1.300) | ladrillo a la vista, ladrillo de máquina; **la primera frase dice "ladrillo visto"** | ladrillo rústico y de campo → `ladrillo-comun` |
| `bloque-de-hormigon` | bloque de hormigón · bloques de cemento (170) · bloque de cemento | bloque hueco, bloque de 15, bloque de 20, bloque estructural | bloques a secas → categoría · adoquín → `adoquines` |
| `tejuelon` | tejuelón · tejuela | ladrillo para losa, losa de tejuelón, bovedilla cerámica | ladrillo hueco → `ladrillo-hueco` |
| `ladrillo-refractario` | ladrillo refractario (880) | ladrillo para parrilla, ladrillo para horno, ladrillo para quincho, mortero refractario | ladrillo común → `ladrillo-comun` |
| `adoquines` | adoquines (1.000) · adoquinado · adoquines de cemento (170) · adopasto (110) | adoquín de hormigón, adoquín ecológico, adoquín para patio, pavimento articulado | bloque de cemento → `bloque-de-hormigon` |

#### Chapas y techos

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `chapas-y-techos` (categoría) | chapa (1.300) · chapas para techo (260) · techos de chapa (480) | metro de chapa, cumbrera, tornillo autoperforante, estructura de techo | zinc y galvanizada → `chapa-de-zinc` · trapezoidal → `chapa-trapezoidal` · termoacústica → `chapa-termoacustica`. **Ningún material persigue "chapa" a secas** (§5.1) |
| `chapa-de-zinc` | zinc (1.600) · chapa zinc (880) · chapa acanalada (480) · chapa galvanizada (390) · galvanizado (260) | chapa ondulada, chapa sinusoidal, chapa n° 25, n° 26, calibre (tabla de calibres) | trapezoidal → `chapa-trapezoidal` · con aislación → `chapa-termoacustica` |
| `chapa-trapezoidal` | chapa trapezoidal (1.600) · chapa trapezoidal medidas (110) | trapezoidal T101, chapa para galpón, perfil trapezoidal, luz entre correas (tabla de medidas) | acanalada → `chapa-de-zinc` · con aislación → `chapa-termoacustica` |
| `chapa-termoacustica` | chapa termoacústica (3.600) · chapa sandwich (720) · techo termoacústico (480) · aislante térmico para techo (390) | isopanel, panel sándwich, techo sandwich, chapa con aislación, poliuretano inyectado | chapa sin aislación → `chapa-de-zinc` / `chapa-trapezoidal` · lana de vidrio y telgopor: sin página, ver Backlog |
| `teja-espanola` | teja española (170) · teja colonial (170) · teja romana (140) · tejas (260) · techo de teja (140) | teja cerámica, teja de barro, teja colonial paraguaya | teja francesa / marsellesa → `teja-francesa` |
| `teja-francesa` | teja francesa (320) | teja marsellesa, teja plana, teja de encastre | teja colonial y romana → `teja-espanola` |
| `fibrocemento` | fibrocemento (140) · eternit (140) · placas de fibrocemento (30) · placa cementicia (40) | chapa de fibrocemento, placa ondulada de fibrocemento, teja de fibrocemento | las 38 marcas extranjeras del §3.4 (internit, superboard, uralita, cedral, pizarreño): **no se escriben** |
| `cielorraso-de-pvc` | pvc para techos (1.300) · cielorraso de pvc (1.000) · cielo raso pvc (390) · cielorrasos (320) · pvc techo (210) · techo de pvc (170) | machimbre de pvc, tablilla de pvc, cielorraso plástico, perfil U de arranque | cielorraso de durlock → `placa-de-yeso` · machimbre de madera → `machimbre` |
| `policarbonato` | techos de policarbonato (590) · policarbonato techo (390) · claraboyas (170) | policarbonato alveolar, policarbonato compacto, 4/6/8/10 mm, techo translúcido, media sombra rígida (tabla de espesores) | chapa translúcida de PVC → `cielorraso-de-pvc` |
| `canaletas` | canaletas (590) · canaleta embutida (590) · canaletas de pvc (260) · canaletas para techos (110) | canaleta para techo, desagüe pluvial, bajada, caño de bajada, canaleta de chapa galvanizada, canaleta tigre, pecho paloma, limahoya | **canaleta a secas y canaleta para cable → electricidad** (sin página): esta página nunca persigue `canaleta` sola |

#### Madera

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `madera` (categoría) | madera (1.000) · madera para construcción · tablas de madera (260) · vigas de madera (260) | corralón de maderas, madera de obra, escuadría | terciado → `terciada` · machimbre → `machimbre` · MDF → `mdf-fibrofacil` · curupay y lapacho → `madera-dura` |
| `tirantes` | tirantes (390) · tirantería | alfajía, tirante de 2x4, tirante de techo, cabio, correa de madera | puntal → `puntales` · listón → `listones` |
| `puntales` | puntales · puntal | puntal de encofrado, puntal de eucalipto, apuntalamiento | tabla de encofrado → `tabla-de-encofrado` |
| `tabla-de-encofrado` | tabla de encofrado · madera de encofrado · entablonado | tabla de pino, tabla sacrificial, fenólico de encofrado | encofrado como técnica → guía `losa-de-hormigon-encofrado-y-hierro` |
| `terciada` | terciado (1.000) · madera terciada (480) · terciado precio (170) · plywood (140) | fenólico, terciado fenólico, placa de terciado, 15/18 mm (tabla de espesores) | MDF y fibrofácil → `mdf-fibrofacil` |
| `machimbre` | machimbre (880) · machimbre de madera (390) | machimbre de pino, machimbre para techo, tablilla, entablonado de cielorraso | machimbre de pvc → `cielorraso-de-pvc` |
| `listones` | listones (140) · listón | varilla de madera, junquillo, alfajía fina | tirante → `tirantes` |
| `mdf-fibrofacil` | material mdf (590) · fibrofácil (590) · madera mdf (480) | placa de MDF, fibrofácil crudo, MDF laminado, 3/9/18 mm | melamina y mueble de melamina: mueblería, sin página (Backlog) · terciado → `terciada` |
| `madera-dura` | curupay (480) · madera dura · lapacho | eucalipto, yvyraró, madera nativa, madera estacionada, poste de eucalipto (tabla de especies) | pino de obra → `tirantes` / `tabla-de-encofrado` |

#### Pisos y revestimientos

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `pisos-y-revestimientos` (categoría) | pisos y revestimientos · revestimientos para pared (880) · zócalo | m² de piso, caja de cerámica, colocación, junta | cerámica → `ceramica-para-piso` · porcelanato → `porcelanato` · azulejos → `azulejos` · adhesivo y pastina → `adhesivo-para-ceramica`. `piso parquet` (480) no tiene página: es madera maciza, queda en Backlog |
| `ceramica-para-piso` | cerámica (1.600, todas las grafías) · pisos de cerámica (320) · cerámica para piso (140) · baldosa piso (480) · piso para baño (480) | cerámico, piso cerámico, 45x45, esmaltado, PEI, antideslizante (tabla de medidas) | porcelanato → `porcelanato` · azulejo de pared → `azulejos` |
| `porcelanato` | porcelanato (1.600) · pisos porcelanato (1.300) · porcelanato para cocina (260) · porcelanato símil madera (170) | porcelanato rectificado, pulido, mate, 60x60, símil madera (tabla de medidas) | cerámica esmaltada → `ceramica-para-piso` |
| `azulejos` | azulejos para baño (1.000) · azulejos (720) · azulejos para cocina (720) | azulejo de pared, revestimiento de baño, subway, 20x30 | piso de baño → `ceramica-para-piso` · símil piedra → `piedra-laja` |
| `piso-vinilico` | piso vinílico (880) · vinílicos adhesivos (720) · piso flotante (320) · piso spc (170) | vinílico en rollo, autoadhesivo, click, SPC, laminado flotante, 4/8 mm | las grafías extranjeras del §3.2 (piso flotante "easy", quito): **no se escriben** |
| `piedra-laja` | piedra laja (320) · revestimiento de piedra (320) · piedra de revestimiento (320) · muros de piedra (170) | símil piedra, piedra para fachada, laja natural, piedra San Luis | porcelanato símil piedra → `porcelanato` |

#### Aberturas

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `aberturas` (categoría) | aberturas (320) · puertas (1.000) · puertas y ventanas · ventanas de madera (260) | vano, medida de vano, colocación, marco, contramarco | puertas de madera → `puertas-de-madera` · puerta placa → `puerta-placa` · ventanas → `ventanas-de-aluminio` · vidrio → `vidrio-templado` · portones y rejas → `portones-y-rejas`. `puertas de PVC` (260) no tiene página: se menciona en la categoría |
| `puerta-placa` | puerta placa (480) | puerta placa de madera, puerta interior, puerta de placa, marco metálico | puerta maciza exterior → `puertas-de-madera` |
| `puertas-de-madera` | puertas de madera (1.000) · puertas principales (110) · diseños de puertas de madera (90) | puerta maciza, puerta de entrada, puerta tallada, puerta de cedro | puerta interior liviana → `puerta-placa` · las grafías de España del §3.2 (blindadas, acorazadas, correderas, de garaje): **no se escriben** |
| `puertas-de-chapa` | puertas de metal (1.300) · puertas metálicas (320) | puerta de chapa, puerta metálica reforzada, puerta de seguridad, marco de chapa | portón → `portones-y-rejas` |
| `ventanas-de-aluminio` | ventanas (880) · aluminio (590) · perfiles de aluminio (480) · ventanas de aluminio (210) · carpintería de aluminio (170) | ventana corrediza, ventana de abrir, línea Módena, mosquitero, premarco | vidrio y blindex → `vidrio-templado` · perfiles de hierro → `perfiles-metalicos` |
| `vidrio-templado` | vidrio (880) · ventanas de blindex (880) · vidrio templado (590) | blindex, vidrio laminado, DVH, 6/8/10 mm, espejo | ventana de aluminio con vidrio común → `ventanas-de-aluminio` |
| `portones-y-rejas` | portones de hierro (720) · rejas para ventanas (590) · rejas (480) · rejas para frentes (480) · portón basculante (480) · rejas y portones (260) · portones (170) · portones corredizos (170) | portón de chapa, portón corredizo, reja de seguridad, herrería, a medida | portones eléctricos y automatización → bucket Profesionales, sin página |

#### Impermeabilizantes

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `impermeabilizantes` (categoría) | impermeabilizantes · impermeabilizante para losa · pintura asfáltica | azotea, losa expuesta, filtración, humedad ascendente | membrana → `membrana-asfaltica` · membrana líquida → `membrana-liquida` · antihumedad → `pintura-antihumedad` · hidrófugo → `hidrofugo` · silicona → `selladores-y-siliconas`. Empresas de impermeabilización: Profesionales |
| `membrana-asfaltica` | membrana para techo (1.300) · membrana (880) · membranas asfálticas (390) | membrana aluminizada, rollo de membrana, 3/4 mm, soplete, geotextil | membrana líquida → `membrana-liquida` |
| `membrana-liquida` | membrana líquida (590) · membrana líquida para techos (260) · pintura impermeabilizante (260) | membrana en pasta, impermeabilizante acrílico, malla de refuerzo, manos de aplicación | membrana en rollo → `membrana-asfaltica` · antihumedad de pared → `pintura-antihumedad` |
| `pintura-antihumedad` | pintura antihumedad (880) · antihumedad (210) | pintura para humedad, humedad de cimiento, salitre, revoque húmedo | techo → `membrana-liquida` · aditivo de mezcla → `hidrofugo` |
| `hidrofugo` | impermeabilizante (590) · impermeabilizante para techos (210) · hidrófugo | aditivo hidrófugo, hidrófugo de mezcla, capa aisladora, cimiento | producto en rollo → `membrana-asfaltica` |
| `selladores-y-siliconas` | silicona (1.000, 3 grafías) · silicona fría (590) · silicona líquida (390) · silicona para vidrio (260) · silicona transparente (140) | sikaflex (marca-genérico, §11.2), sellador de juntas, sellador acrílico, poliuretánico, pistola de silicona | sikacryl, sikadur, sikagrout, sikalatex y demás códigos de producto: **fuera de la lista cerrada, no se escriben** · aditivos para hormigón: Backlog |

#### Yeso y durlock

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `yeso-y-durlock` (categoría) | yeso y durlock · construcción en seco · masilla para juntas · cinta de papel | tabique, cielorraso suspendido, m² de placa, tornillo T1/T2 | durlock y placas de yeso → `placa-de-yeso` · yeso en polvo → `yeso-en-polvo` · montante y solera → `perfiles-para-durlock` |
| `placa-de-yeso` | durlock (1.900) · cielorraso durlock (260) · placas de yeso (170) · techo durlock (110) | durlock (marca-genérico, §11.2), placa de roca de yeso, placa verde (humedad), placa rosa (fuego), 9,5 / 12,5 mm | cielorraso de PVC → `cielorraso-de-pvc` · perfilería → `perfiles-para-durlock` |
| `yeso-en-polvo` | yeso (880) · yeso para pared (390) | yeso en bolsa, yeso proyectado, enlucido de yeso, fragüe | placa → `placa-de-yeso` · revoque de cal y cemento → guía `como-revocar-una-pared` |
| `perfiles-para-durlock` | soleras para durlock (90) · perfil montante · solera | montante de 35/70, solera, ángulo de ajuste, omega, perfil galvanizado para durlock | perfiles estructurales de hierro → `perfiles-metalicos` |

#### Caños y plomería

| Página | Posee | Teje | No es suya |
|---|---|---|---|
| `canos-y-plomeria` (categoría) | caños y plomería · accesorios de plomería · cañería | codos, tees, uniones, cupla, pegamento para PVC, teflón | tanque → `tanque-de-agua` · caño de desagüe → `cano-de-pvc` · caño de agua → `cano-de-agua` · caño conduit (480) → electricidad, sin página · canillas, inodoros y grifería: fuera del build (plan §1.14) |
| `tanque-de-agua` | tanque de agua (1.300) · tanque de agua 1000 litros (720) · tanque de agua 500 litros (210) · tanque de 1000 litros (110) | syopar (marca-genérico, §11.2), tanque tricapa, bicapa, tapa hermética, base de tanque, 400/500/1000/2000 l (tabla de medidas) | bomba de agua y presurizadora: sin página |
| `cano-de-pvc` | caños pvc (210) · tubos pvc (140, 4 grafías) · caño de desagüe | caño de 110, caño de 100, cloacal, pluvial, junta elástica, caño amanco (marca-genérico) | caño de agua fría y caliente → `cano-de-agua` · caño conduit → electricidad |
| `cano-de-agua` | caño (480) · cañería de agua · caño de agua | termofusión, PPR, caño roscado, agua fría y caliente, ½ y ¾, llave de paso | desagüe → `cano-de-pvc` · conduit eléctrico → electricidad. **Volumen de `termofusión`/`PPR` sin medir** (KEYWORDS §4.2 los lista como frases a chequear): la página se sostiene sobre `caño` (480, la puja más alta del clúster). Si el segundo pull vuelve vacío, se funde con `cano-de-pvc` (KNOWN-ISSUES) |

Categorías que **no** se promueven en la fase 5 y por lo tanto no tienen fila: `pinturas` y
`electricidad` (quedan `proxima`, plan §1.15). Los clústeres de sanitarios y grifería,
herrajes y cerraduras, y herramientas están fuera del build (plan §1.14).

### 11.2 Marcas usadas como genérico — lista CERRADA

El sitio no vende marcas y no las pone nunca en `title`, `meta`, H1 ni H2, y jamás dice
"vendemos X" ni nombra un proveedor. Pero el comprador paraguayo busca ocho marcas **como si
fueran el nombre del producto**, y una página que no puede escribir la palabra no rankea. Se
permiten sólo estas ocho, sólo dentro de la prosa, los sinónimos o una FAQ, y siempre en
minúscula y como sinónimo genérico ("las placas de yeso, que acá todos llaman durlock"):

| Marca | Producto genérico | Página que la usa |
|---|---|---|
| durlock | placa de yeso | `placa-de-yeso` |
| isopanel | chapa termoacústica / panel sándwich | `chapa-termoacustica` |
| blindex | vidrio templado | `vidrio-templado` |
| eternit | fibrocemento | `fibrocemento` |
| syopar | tanque de agua | `tanque-de-agua` |
| sikaflex | sellador poliuretánico | `selladores-y-siliconas` |
| canaleta tigre | canaleta de PVC | `canaletas` |
| caño amanco | caño de PVC | `cano-de-pvc` |

Todo lo que no está en esta tabla sigue prohibido, incluidas las marcas locales de cemento
(INC, Yguazú), las de pintura y todos los códigos de producto (sikadur 31, sika 1, sikacryl,
recuplast, internit, superboard, uralita, pizarreño…). Ampliar la lista es decisión de plan,
no de una fase de contenido.

### 11.3 FAQ obligatoria de precio — patrón cerrado

Cerca de 2.500 búsquedas mensuales del bucket llevan el modificador `precio` (`cemento
precio`, `chapa trapezoidal precio`, `tanque 1000 litros precio`, `ladrillo común precio`…).
No publicamos números (regla de encabezado de este documento), pero sin una respuesta visible
esas visitas rebotan. Por eso **toda página de material lleva, como última FAQ, la pregunta**:

> `¿Cuánto cuesta {material}?`

Respuesta según esta plantilla — factores primero, CTA al final, nunca una cifra, nunca un
rango, nunca "desde":

> No publicamos precios porque cambian con {la unidad de venta}, la cantidad que lleves,
> {el factor propio del material: medida, calibre, espesor, tipo}, el flete hasta tu zona y el
> momento en que pidas. Cargá {qué cantidad y qué medida} en el formulario y hasta 3
> proveedores verificados te pasan su precio por WhatsApp, normalmente dentro del día.

Reglas de la plantilla:

- Los factores se escriben en el vocabulario del material, no genéricos: la chapa se cotiza
  por metro lineal y calibre, el ladrillo por millar, los áridos por camionada o m³, el hierro
  por barra o por kilo.
- Va siempre **al final** de `faq[]` y siempre visible (el router ya renderiza las FAQ, así
  que también entra en el `FAQPage`).
- El total de `faq[]` sigue siendo 3 a 5 entradas (lo verifica `tools/smoke.php`), así que
  esta pregunta ocupa uno de esos lugares.
- Las páginas de **categoría** no llevan esta FAQ: la pregunta de precio es por producto.

### 11.4 Regla de medidas

Cerca de 700 búsquedas mensuales piden `medidas` (`ladrillo común medidas` 210, `chapa
trapezoidal medidas` 110, `medida de ladrillo hueco` 170…) y muchas más piden un tamaño
directamente (`1000 litros`, `60x60`, `de 8`, `8 mm`). Es demanda con forma de tabla.

**Obligatorio:** estas páginas llevan una tabla de medidas dentro del H2 `Qué mirar antes de
comprar` — `varilla-de-hierro` (diámetros), `ladrillo-comun`, `ladrillo-hueco`,
`chapa-trapezoidal`, `tanque-de-agua` (litrajes), `policarbonato` (espesores) y `terciada`
(espesores).

**Recomendado donde el vocabulario ya lo pide:** `piedra-triturada` (4ta/5ta/6ta),
`perfiles-metalicos` (C, U, IPN, UPN, ángulo), `chapa-de-zinc` (calibres n° 25 / 26),
`cano-de-pvc` (100 y 110 mm), `porcelanato` y `ceramica-para-piso` (formatos),
`madera-dura` (especies).

La tabla es informativa: filas de medida y uso típico. **Nunca** lleva columna de precio ni
de disponibilidad, y las medidas que se listan son las que el propio texto puede sostener
("las que se consiguen en plaza"), sin inventar catálogos de proveedor.

### 11.5 Guías nuevas — esquemas cerrados

Mismas reglas que §6: 600–900 palabras, cierre con enlaces de dinero y el CTA `Pedí tu
cotización`, ninguna dosificación afirmada como verdad universal.

| Guía | H2 | Enlaces obligatorios (ancla → destino) |
|---|---|---|
| `como-revocar-una-pared` | `Qué lleva un revoque` · `Grueso y fino: cómo se hace` · `Cuánto material calcular` · `Qué pedirle al proveedor` | `cal hidratada` → /materiales/cal-hidratada/ · `arena lavada` → /materiales/arena-lavada/ · `cemento` → /materiales/cemento/ |
| `losa-de-hormigon-encofrado-y-hierro` | `Qué es el hormigón armado` · `El encofrado: tablas, puntales y desencofrado` · `El hierro de la losa y de las zapatas` · `Qué pedir al proveedor y en qué orden` | `hormigón elaborado` → /materiales/hormigon-elaborado/ · `varilla de hierro` → /materiales/varilla-de-hierro/ · `tabla de encofrado` → /materiales/tabla-de-encofrado/ |

`como-revocar-una-pared` absorbe el clúster `revoque` / `revocado` / `revoco de pared`
(~1.800, intención de cómo-hacerlo). `losa-de-hormigon-encofrado-y-hierro` absorbe `hormigón
armado`, `encofrado`, `losas` y `zapatas` (~1.900, conocimiento constructivo): son términos
que ninguna página de material persigue, precisamente porque su intención no es comprar sino
entender.
