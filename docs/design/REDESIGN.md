# Rediseño "Obra clara" — materiales.com.py

Fecha: 2026-09-23. Alcance: capa visual, UX y conversión (CSS + marcado de plantillas). Sin
cambios en el contrato del pedido (campos, nombres, ocultos, honeypot, sello firmado,
consentimiento y su texto, handler, payload), en la prosa de `content/` ni en el copy de `data/`.

Capturas: `docs/design/before/` y `docs/design/after/` (390×844 y 1366×900, primeras 2,5–3
pantallas, JPEG). En las capturas de página completa la barra pegajosa mobile aparece a mitad
de la imagen: es un elemento `position: fixed` capturado donde estaba el viewport, no un bug. Extra
del lote C11: `after/calc-hormigon-bundle-*` (después de tocar "Sumá todo el pedido") y
`after/cotizar-lista-*` (`/cotizar/?lista=1`).

---

## 1. Auditoría heurística (antes)

| Eje | Hallazgo | Evidencia |
|---|---|---|
| Jerarquía visual | Héroes enormes (padding inferior de hasta 176 px para el "mordisco" del panel) y H1 de 48 px mínimo: en mobile el H1 de la home ocupa 4 renglones y la primera pantalla es sólo título + bajada. | `before/home-m390.jpg` |
| Camino al pedido | En la home el único CTA del héroe ancla a un formulario a ~5.000 px. En /cotizar/ el H1, la bajada del héroe y la del formulario repiten la misma frase y el primer campo cae debajo del pliegue. | `before/home-m390.jpg`, `before/cotizar-m390.jpg` |
| Ergonomía mobile | Doble margen (gutter de página + padding del panel): la prosa queda en ~270 px útiles de 390. La foto del héroe de material va después del formulario y empuja el contenido 220 px. | `before/material-cemento-m390.jpg` |
| Catálogo | 13 rubros como barras negras idénticas, sin foto ni cantidad: 800 px de scroll en mobile sin información para elegir. En la categoría, las tarjetas de material sólo dicen el nombre. | `before/materiales-m390.jpg` |
| Confianza | "Cómo funciona" sólo en la home; en las páginas de dinero no hay nada que explique el modelo ni enlace a cómo se verifica a los proveedores (la página existe: /como-trabajamos/). | — |
| Claridad de CTA | CTA del héroe de la home, "Cotizar" como un link más del menú, "Ver todos los materiales" dentro de una caja blanca vacía de 600 px. | `before/home-d1366.jpg` |
| Calculadora → pedido | El resultado ocupa 5 renglones gigantes apilados en mobile; el botón "Cotizá estas …" parte la flecha a otro renglón y queda suelto del resultado. | `before/calc-hormigon-m390.jpg` |
| Lectura larga | Texto con alfa (color-mix) en vez de colores sólidos; tablas sin cebra; H2 de hasta 48 px dentro de la prosa. | `before/guia-porcelanato-*` |
| Accesibilidad | Sin estilo `:focus-visible` global: botones, tiles y links dependían del anillo del navegador (invisible sobre la cáscara oscura). Placeholders a 1.67:1 en algunos campos (texto alfa sobre beige). | inspección |
| Velocidad | Buena base (fuentes locales con fallbacks métricos, AVIF/WebP con `sizes`, sin JS pesado). Se conserva. | — |

## 2. Dirección

"Obra clara": el mismo corralón (casi negro + amarillo de seguridad + franja de peligro), pero
con la cáscara oscura reducida a lo que identifica la marca y todo lo demás claro, directo y
con el pedido siempre a un toque.

1. **El pedido empieza arriba.** Home, categorías y materiales abren con el formulario corto
   dentro del héroe; en mobile el botón de enviar entra en la primera pantalla (390×844).
2. **Menos cáscara, más campo.** Héroes compactos; en mobile la foto pasa a fondo del héroe
   (detrás de un velo) y el panel de lectura va a todo el ancho con un solo margen.
3. **Confianza honesta y repetida.** Tres pasos + tildes ("Gratis y sin compromiso", "No
   publicamos tu teléfono", "Cómo verificamos a los proveedores") en cada página de dinero.
   La única cifra sigue siendo `max_proveedores`; las otras dos de la home salen de los datos.
4. **Catálogo que se elige con los ojos.** Tarjetas de rubro con la foto que ya existía y la
   cantidad de materiales; tarjetas de material con su unidad de venta.
5. **Un solo sistema de controles.** Botones y campos de 48–56 px, anillo de foco de dos tonos
   visible sobre cualquier fondo, colores de texto sólidos con contraste AA calculado.

Tipografía: se mantienen Bricolage Grotesque (display) e Inter (texto), autohospedadas, con los
fallbacks métricos de S6 (sin CLS). Escala fluida: H1 `clamp(2.125rem, 1.35rem + 3.4vw, 4.25rem)`
(34 px en 390, 68 px en escritorio), H2 `clamp(1.5rem, …, 2.25rem)`, cuerpo 17 px / 1.65, prosa
1.72, medida 68ch.

## 3. Tokens

| Token | Valor | Uso | Contraste |
|---|---|---|---|
| `--base` | `#17170f` | cáscara, texto de botón primario | — |
| `--accent` | `#f2c318` | botón primario, números, foco sobre oscuro | 10.80:1 vs `--base` |
| `--accent-hover` | `#ffd43b` | hover del primario | 12.64:1 vs `--base` |
| `--accent-ink` | `#7a5c00` | links y marcas sobre claro | 5.64:1 paper · 6.25:1 blanco · 5.77:1 tint |
| `--ink` | `#f5f3f0` | texto sobre cáscara | 16.26:1 |
| `--ink-muted` | `#c9c5bb` | bajadas sobre cáscara | 10.45:1 |
| `--ink-subtle` | `#a9a498` | migas, fechas sobre cáscara | 7.25:1 |
| `--paper` | `#f5f3ee` | fondo de página, bloques suaves | — |
| `--tint` / `--tint-line` | `#fdf6dc` / `#eadfb8` | formulario completo, cierre de guía | — |
| `--text` | `#1b1a17` | títulos y texto fuerte | 15.69:1 paper |
| `--text-2` | `#45423b` | prosa | 9.04:1 paper · 10.02:1 blanco |
| `--text-3` | `#5c584f` | notas, metadatos | 6.39:1 paper · 6.54:1 tint |
| `--line-strong` | `#8a857a` | borde de campos/botón fantasma | 3.67:1 blanco (≥ 3:1, 1.4.11) |
| placeholder | `#767166` | ejemplos en campos | 4.86:1 blanco |
| `--error` / `--ok` | `#b3261e` / `#1e7a3c` | error / tilde | 6.54:1 / 5.38:1 blanco |
| foco | 3 px `--base` + 2 px `--accent` | todo `:focus-visible` | 18.01:1 sobre blanco; 10.80:1 (amarillo) sobre la cáscara |

Espaciado base 4 px (`--s-1` … `--s-24`), radios 8 / 14 / 24 px, `--tap: 48px`. Los alias de la
fase 4 (`--paper-ink-70`, `--ink-70`, …) quedan apuntando a los tokens nuevos.

## 4. Componentes y razón de conversión

| Cambio | Dónde | Por qué convierte (y por qué es honesto) |
|---|---|---|
| Formulario corto en el héroe de la **home** (material + cantidad + WhatsApp; se despliega al primer foco) | `index.php`, `partials/form.php` (`$formLeadFields`: sólo orden visual) | La home era la única página de dinero sin campo arriba. Mismo formulario, mismos nombres y consentimiento; el completo sigue al final. |
| Foto del héroe como fondo en mobile (y siempre en la home) | `site.css`, `partials/hero-image.php` (`$heroSizes`, default intacto) | Recupera ~220 px para que el botón "Pedir precio" entre en la primera pantalla sin perder la foto. La home declara `sizes="(min-width: 64rem) 80vw, 100vw"`; el resto conserva `(min-width: 64rem) 40vw, 100vw`. |
| Franja "Cómo funciona" + tildes de confianza en categoría, material y /cotizar/ | `partials/how-it-works.php` | Explica el modelo (y que es gratis) donde se decide. Sólo afirma lo ya afirmado (CONTENT-SPEC §1/§7, /como-trabajamos/). Sin plazos nuevos. |
| Tarjeta de pedido pegajosa en escritorio (material, categoría, guía, calculadora) | `partials/aside-cta.php` | Las páginas largas (3.000–6.000 px) dejaban el CTA sólo arriba y abajo. En mobile no se muestra: ya está la barra pegajosa. |
| Botón "Cotizar gratis" en el header | `partials/header.php` (`data-ev="cta_click"`, `data-ev-loc="header"`) | El pedido era un link más del menú. "Gratis" es un hecho del servicio. |
| Tarjetas de rubro con foto y cantidad de materiales | `partials/category-tiles.php` | Reconocer el rubro por la imagen es más rápido que leer 13 barras iguales. Las fotos ya existían (AVIF/WebP 640, `loading="lazy"`, `width/height` fijos → sin CLS). La cantidad se cuenta desde `data/materials.php`. |
| Tarjetas de material con "Se vende por …" | `materiales/index.php` | La unidad de venta es lo primero que hay que saber para pedir; sale de `sale_unit`. |
| Resultado + botón de la calculadora como una sola tarjeta, con "La cantidad ya queda cargada en el formulario de pedido." | `calculadoras/index.php`, `site.css` | El paso calculadora → pedido se ve como continuación, no como un link suelto. `calc.js` no se tocó. |
| C11: "Sumá todo el pedido" y "¿Tenés una lista…? Pegala entera acá" estilizados | `site.css` (bloque C11) | Botón secundario pegado al resultado (con estado en verde) y fila punteada con ícono de lista; el modo lista agranda el textarea. Lógica de #57 intacta. |
| /cotizar/ con héroe corto y los pasos al costado | `cotizar/index.php` | Sacamos la bajada duplicada; en mobile el primer campo sube ~180 px. |
| Formulario completo en bloque tinte amarillo | `site.css` | Es el destino de todos los anclajes `#cotizar`; se distingue del panel blanco (antes era tarjeta blanca sobre blanco). `:target` lo resalta al llegar. |
| Campos de 52 px, botón de envío de 56 px a todo el ancho en mobile, casilla de 22 px, nota con candado | `site.css` | Menos errores de toque; el consentimiento sigue desmarcado y con su texto literal. |
| Barra pegajosa mobile: primario ancho + WhatsApp compacto, 52 px | `site.css` | El pulgar encuentra el primario sin apuntar; WhatsApp sigue segundo (CONTENT-SPEC §9). |
| Prosa: medida 68ch, 1.72 de interlínea, H2 más chicos, listas con marcador en color, tablas con cebra y cabecera oscura, scroll interno | `site.css` | Guías de 600–2.000 palabras se leen sin fatiga; ninguna tabla desborda la página. |
| FAQ como tarjetas con zona de toque de 56 px | `site.css` | Mismo `<details>` (FAQ visibles = `FAQPage`), más fácil de tocar. |
| Teaser de calculadoras en la home ("¿No sabés cuánto pedir?") | `index.php` | Lleva a las páginas con más intención de compra y que precargan la cantidad. |
| /gracias/: tilde de confirmación | `gracias/index.php` | Confirma el éxito a primera vista. |
| Foco visible de dos tonos en todo, `prefers-reduced-motion` apaga transiciones, hover sin desplazamiento y scroll suave | `site.css` | WCAG 2.4.7 / 2.3.3. |

Lo que NO se agregó a propósito: reseñas, estrellas, logos, testimonios, "+N clientes", años,
cantidad de proveedores, plazos nuevos, precios. Los campos vacíos de `data/site.php` siguen sin
renderizarse.

## 5. Verificación

- `php -l` en todo el repo: sin errores.
- `php tools/smoke.php`, `php tools/check-rewrites.php`, `bash tools/render-check.sh`: OK
  (incluye las aserciones literales: `<h1>` sin atributos, `id="cotizar-rapido"`,
  `data-lead-compact`, consentimiento exactamente dos veces en material, un solo `role="alert"`,
  `sizes` del héroe de categoría, C11).
- `node tests/mobile-overflow.mjs`: OK (320/360/390/1280). Chequeo extra en 16 rutas más
  (320/390/768/1024), incluidas /cotizar/?lista=1, /gracias/ y el glosario: sin desborde.
- Foco con teclado recorrido en home (mobile) y material (escritorio): cada elemento muestra
  el anillo (amarillo sobre la cáscara, oscuro sobre claro).
- CSS: ~50 KB sin minificar (límite ~60 KB). Sin requests externos ni JS nuevo.

## 6. Riesgos y pendientes

- La home declara `sizes="(min-width: 64rem) 80vw, 100vw"` (antes `40vw` en escritorio): en
  1366 px baja la variante 1280 en vez de la 640 (unos KB más; la foto va detrás de un velo, así
  que no hace falta la 1920). Vigilar el LCP de la home.
- Las tarjetas de rubro suman hasta 12 imágenes lazy de ~10–35 KB (AVIF) en home y /materiales/.
- `:has()` se usa para dos mejoras progresivas (la columna del `<select>` en el héroe y el
  bloqueo de scroll con el menú abierto, que ya existía); sin soporte el layout sigue válido.
- La tarjeta lateral y la franja de pasos agregan marcado a material/categoría/guía/calculadora:
  si un merge futuro toca esas plantillas, conservar `.page-cols` / `.page-cols__main`.
- Medir antes/después con GA4 (`form_start`, `form_submit_attempt`, `cta_click` con
  `ev_loc` `header`, `aside-*`, `hero-*`) cuando `ga4_id` esté cargado (C1).
