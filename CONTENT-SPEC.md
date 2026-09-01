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

## 4. Vocabulario obligatorio

Cada material tiene `synonyms[]` con el vocabulario real de obra paraguaya y cada categoría
`intro_keywords[]`. La prosa tiene que usarlos de forma natural — son la razón por la que
estas páginas rankean: "piedra bruta" también se pide como *piedra para cimiento*; la
triturada, como *4ta*, *5ta* o *6ta*; el isopanel es *panel sándwich*; el ladrillo hueco es
*ladrillo de 8* o *de 12*. Ninguno de esos términos recibe página propia.

## 5. Cuerpo de prosa — estructura obligatoria

**Material** (`content/materiales/{slug}.php`), 350–600 palabras, en este orden:

- H2 `Para qué se usa` — usos reales en obra paraguaya; acá entran los sinónimos.
- H2 `Cómo se vende y cómo pedirlo` — unidad de venta, qué datos necesita el proveedor para
  cotizar (cantidad, medida, zona), qué se cotiza aparte (flete, accesorios).
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
