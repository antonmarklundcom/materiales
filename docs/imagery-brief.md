# Imagery brief — materiales.com.py

Qué generar cuando el entorno permita descargar del CDN de Higgsfield (KNOWN-ISSUES #22:
hoy `*.cloudfront.net` da 403; hay que habilitarlo en la configuración del entorno de Claude
Code antes de correr esto). Este archivo es el manifest de referencia que
`higgsfield-image-pipeline` Regla 0 espera encontrar — léelo antes de generar nada, así una
sesión futura no repite trabajo.

## Alcance: una foto por categoría, no por material

12 imágenes en total: el hero de home + una por cada una de las 11 categorías activas
(`electricidad` y `pinturas` siguen `proxima`, sin contenido — no llevan foto todavía). Sirven
como `og:image` de esa categoría y de todos sus materiales — no hay 64 fotos de material
individual. Motivo: muchos materiales de una misma categoría son visualmente casi idénticos
(arena lavada vs. arena gorda, ladrillo común vs. prensado) y una foto por material dispararía
el costo (64 × 2 créditos) sin mejorar el reconocimiento de marca. Si más adelante se quiere
una foto por material, generarlas siguiendo el mismo estilo/Element de su categoría.

Las 8 guías no llevan foto propia: reutilizan la foto de la categoría a la que enlazan más
(ver tabla de `related` en `data/guides.php`).

## Estilo y dirección de arte (una sola vez, como Element reusable)

- **Modelo**: `nano_banana_pro`, resolución `2k` (regla fija de `higgsfield-image-pipeline`,
  nunca otro modelo).
- **Encuadre**: 1200×630 (o el ratio más cercano que soporte el modelo, recortado a 1200×630
  después con `webimg`).
- **Mood**: fotografía industrial de producto, luz dura lateral o de contraluz, grano visible,
  paleta oscura y neutra (negros, grises de hormigón, tonos tierra) con **un solo acento
  anaranjado** (`#E8562A` — una correa, un cono, un cartel, una cinta, un elemento textil)
  coherente con el track INDUSTRIAL del sitio (`web-design-system`).
- **Nunca**: personas, rostros, manos, logos ni marcas visibles en bolsas/paquetes/chapas,
  texto superpuesto, precios, escenas de "nuestro equipo trabajando", nada que parezca stock
  genérico de banco de imágenes.
- **Composición**: dejar el tercio superior o un lateral con espacio negativo simple (fondo
  desenfocado u oscuro liso) para que un futuro overlay de texto (nombre de categoría) se
  pueda superponer en HTML/CSS sin pelear con la imagen — no hace falta bakear texto en la
  imagen misma.

## Lista y prompts, en el mismo orden del build (home, luego las 5 de lanzamiento, luego las
## 6 promovidas en orden §1.15)

### 0. Home / sitewide hero

**Archivo**: `assets/img/hero-home.jpg` · **Usa en**: `/` y como `og-default.jpg` de reemplazo.

> Fotografía industrial de producto, gran angular, de un depósito de materiales de
> construcción al atardecer: bolsas de cemento apiladas en palets, atados de varillas de
> hierro, ladrillos apilados y chapas de zinc apoyadas contra una pared de chapa corrugada,
> todo dentro del mismo encuadre pero sin desorden — composición ordenada, casi arquitectónica.
> Luz rasante cálida de atardecer entrando desde un lateral, sombras largas, polvo suspendido
> visible en el haz de luz. Paleta oscura y terrosa (grises de hormigón, óxido, tierra) con un
> solo acento anaranjado saturado (`#E8562A`) en una correa de sujeción o un cono de
> señalización. Grano fotográfico sutil, alto contraste, sin personas, sin rostros, sin manos,
> sin logos ni marcas visibles en ningún envase o chapa, sin texto. Encuadre 1200×630 (o el más
> cercano disponible), tercio superior con cielo o sombra lisa para dejar espacio a un overlay
> de texto futuro. Estética de editorial de arquitectura/industria, no de banco de imágenes
> genérico.

### 1. Hierro

**Archivo**: `assets/img/hero-hierro.jpg`

> Fotografía industrial de producto en primer plano: un atado grueso de varillas de hierro
> nervurado (varilla de construcción), apiladas en diagonal y atadas con alambre, textura
> metálica oxidada visible en los extremos cortados. Fondo desenfocado de un depósito de
> hierros, oscuro. Luz dura de contraluz que dibuja el perfil de las varillas, un destello de
> luz anaranjada (`#E8562A`) reflejado en un extremo o en una etiqueta de amarre de plástico.
> Grano fotográfico, alto contraste, paleta de grises metálicos y negros. Sin personas, sin
> manos, sin logos ni marcas, sin texto ni números de medida visibles. Encuadre 1200×630,
> espacio negativo en la parte superior izquierda.

### 2. Cemento y cal

**Archivo**: `assets/img/hero-cemento-y-cal.jpg`

> Fotografía industrial de producto: bolsas de cemento en papel kraft (sin ninguna marca ni
> logo impreso — superficie lisa o con textura genérica de papel), apiladas en un palet de
> madera dentro de un depósito. Rayo de luz cenital cortando el polvo en suspensión, atmósfera
> densa. Un flejado o correa de sujeción anaranjada (`#E8562A`) cruzando el palet como único
> acento de color. Paleta neutra de grises y beige, grano visible, alto contraste. Sin
> personas, sin logos, sin texto ni cifras. Encuadre 1200×630, fondo superior oscuro y liso
> para overlay de texto.

### 3. Áridos

**Archivo**: `assets/img/hero-aridos.jpg`

> Fotografía industrial de producto, a nivel del suelo: montículos de arena y piedra triturada
> de distintas granulometrías y colores (arena clara, ripio gris, piedra triturada más oscura)
> uno junto al otro en un depósito de áridos a cielo abierto, mostrando la diferencia de
> textura entre pilas. Luz lateral dura de fin de tarde, sombras marcadas resaltando el relieve
> de cada montículo. Un cono de señalización o pala anaranjada (`#E8562A`) como único acento de
> color, apoyada contra una de las pilas. Cielo nublado o en degradé oscuro arriba. Grano
> fotográfico, paleta terrosa. Sin personas, sin maquinaria de gran porte, sin logos, sin
> texto. Encuadre 1200×630.

### 4. Ladrillos y bloques

**Archivo**: `assets/img/hero-ladrillos-y-bloques.jpg`

> Fotografía industrial de producto en primer plano: un muro bajo hecho apilando ladrillos
> comunes de barro cocido y bloques de hormigón gris, mostrando la textura porosa del barro
> junto a la superficie lisa del bloque. Luz rasante lateral que acentúa la textura de cada
> pieza, fondo desenfocado de un depósito. Un pincel de cal o una cinta métrica con detalle
> anaranjado (`#E8562A`) apoyada sobre los ladrillos como único acento de color. Paleta cálida
> de terracota y gris hormigón, grano fotográfico, alto contraste. Sin personas, sin manos, sin
> logos, sin texto. Encuadre 1200×630, espacio negativo arriba.

### 5. Chapas y techos

**Archivo**: `assets/img/hero-chapas-y-techos.jpg`

> Fotografía industrial de producto: chapas metálicas onduladas y trapezoidales apoyadas en
> diagonal contra una pared de chapa corrugada, mostrando el brillo metálico y las ondas en
> perspectiva. Contraluz de atardecer creando reflejos cálidos naranjas y dorados directamente
> sobre el metal (el propio reflejo funciona como el acento `#E8562A`, sin necesidad de un
> objeto de attrezzo adicional). Cielo visible arriba en degradé oscuro. Grano fotográfico,
> alto contraste, paleta metálica fría contrastada con el reflejo cálido. Sin personas, sin
> logos ni marcas de fábrica visibles en la chapa, sin texto. Encuadre 1200×630.

### 6. Pisos y revestimientos

**Archivo**: `assets/img/hero-pisos-y-revestimientos.jpg`

> Fotografía industrial de producto, vista cenital o en ángulo bajo: muestras de cerámica,
> porcelanato y piedra laja apoyadas en abanico o superpuestas parcialmente sobre una
> superficie de hormigón pulido, mostrando la diferencia de textura y brillo entre materiales.
> Luz suave direccional desde un lateral, sombras suaves entre las piezas. Un metro plegable o
> una espaciadora de cerámica anaranjada (`#E8562A`) como único acento de color sobre las
> muestras. Paleta neutra clara con contraste de texturas, grano fotográfico sutil. Sin
> personas, sin manos, sin logos ni marcas de fábrica en las piezas, sin texto. Encuadre
> 1200×630.

### 7. Aberturas

**Archivo**: `assets/img/hero-aberturas.jpg`

> Fotografía industrial de producto: una puerta de madera maciza y un marco de ventana de
> aluminio apoyados uno junto al otro contra una pared de taller sin terminar, mostrando la
> veta de la madera y el perfil metálico del aluminio en contraste. Luz lateral dura tipo
> galería de taller, sombras marcadas. Un nivel de burbuja o una cinta métrica con detalle
> anaranjado (`#E8562A`) apoyada sobre el marco como único acento de color. Paleta cálida de
> madera contra gris de taller, grano fotográfico. Sin personas, sin manos, sin logos, sin
> texto ni medidas visibles. Encuadre 1200×630.

### 8. Impermeabilizantes

**Archivo**: `assets/img/hero-impermeabilizantes.jpg`

> Fotografía industrial de producto: un rollo de membrana asfáltica parcialmente desenrollado
> sobre una losa de hormigón expuesta, con un balde de impermeabilizante líquido y una llana o
> pincel apoyados al lado. Cielo nublado visible arriba (contexto de azotea), luz difusa y fría
> con un reflejo cálido anaranjado (`#E8562A`) en la superficie brillante de la membrana o en
> el balde. Paleta gris-azulada con el acento cálido puntual, grano fotográfico, atmósfera de
> humedad. Sin personas, sin manos, sin logos ni marcas, sin texto. Encuadre 1200×630.

### 9. Yeso y durlock

**Archivo**: `assets/img/hero-yeso-y-durlock.jpg`

> Fotografía industrial de producto: placas de yeso apiladas de canto mostrando el borde
> biselado y la textura del cartón, junto a perfiles metálicos de montante y solera apoyados en
> diagonal, dentro de un interior de obra en construcción en seco (paredes sin terminar de
> fondo, desenfocadas). Luz suave lateral, sombras suaves. Un cúter o cinta de papel para
> juntas con detalle anaranjado (`#E8562A`) como único acento de color. Paleta clara de blancos
> y grises con textura de cartón visible, grano fotográfico sutil. Sin personas, sin manos, sin
> logos, sin texto. Encuadre 1200×630.

### 10. Caños y plomería

**Archivo**: `assets/img/hero-canos-y-plomeria.jpg`

> Fotografía industrial de producto: rollos de caño de PVC blanco enrollados y apilados junto a
> un tanque de agua plástico (tricapa, superficie lisa sin ninguna marca ni logo) en un depósito
> o patio de obra. Luz lateral dura, sombras marcadas resaltando la curvatura del caño y el
> volumen del tanque. Una llave de paso o teflón con detalle anaranjado (`#E8562A`) como único
> acento de color. Paleta neutra de blancos y grises con el fondo oscuro desenfocado, grano
> fotográfico. Sin personas, sin manos, sin logos ni marcas visibles, sin texto. Encuadre
> 1200×630.

### 11. Madera

**Archivo**: `assets/img/hero-madera.jpg`

> Fotografía industrial de producto: tablones y tirantes de madera apilados en un depósito de
> maderas, mostrando la veta y el corte transversal de las piezas en primer plano, con más
> pilas desenfocadas de fondo. Luz cálida de atardecer entrando en rasante, acentuando la
> textura de la veta y el color natural de la madera. Una cinta métrica o correa de sujeción
> anaranjada (`#E8562A`) como único acento de color cruzando una de las pilas. Paleta cálida de
> maderas (ocres, marrones) contra sombras oscuras, grano fotográfico, alto contraste. Sin
> personas, sin manos, sin logos, sin texto ni medidas visibles. Encuadre 1200×630.

## Cómo usarlo cuando el CDN esté permitido

1. Confirmar Regla 2 de `higgsfield-image-pipeline`: `curl -sI -m 15 https://*.cloudfront.net/`
   ya no debe dar 403.
2. Generar las 12 imágenes de arriba, en orden, con `generate_image` /
   `generate_image_batch` — `model: "nano_banana_pro"`, `resolution: "2k"`, un prompt por
   imagen, tal cual están escritos.
3. Verificar en el ledger (`transactions`) que cada job cobró como "Nano Banana Pro" (Regla 1).
4. Convertir con `webimg` al nombre de archivo indicado en cada sección, alt text en español
   paraguayo describiendo el contenido real (no el prompt), y colocarlas en
   `public_html/assets/img/`.
5. Actualizar este archivo con un bloque `_notes` (generated, cost_preflight,
   actual_spend_credits, ledger_checked, download_status) por imagen, para que la próxima
   sesión no repita el trabajo (Regla 0).
6. Recién ahí conviene decidir si vale la pena tocar `partials/header.php` para servir un
   `og:image` distinto por categoría (hoy sólo hay un `og-default.jpg` compartido) — es un
   cambio de plantilla, no de contenido, así que necesita su propia fase/PR.
