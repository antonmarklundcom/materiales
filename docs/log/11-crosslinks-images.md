# Fase 11 — Motor de enlaces cruzados y slots de imagen (Opus, `phase/11-crosslinks-images`)

Fecha: 2026-09-11 · plan §11.3 · decisiones §1.19, §1.20

## Built
- `partials/init.php`: `guides_for()` y `calculators_for()` (inverso de `related[]`, sólo
  entradas `activa`, ordenadas por `order`; la de calculadoras está guardada con `is_file`
  porque `data/calculators.php` lo crea la fase 12) e `image_for()` con la herencia §1.20
  (material → su categoría, guía → su primera página de dinero) que devuelve null cuando el
  archivo no está en disco. `page()` acepta la clave `image`.
- `partials/related.php` (nuevo): "Guías relacionadas" y "Calculadoras relacionadas" como
  `.tile-grid`. Vacío ⇒ no imprime nada. Se incluye entre las FAQ y el formulario en
  categorías y materiales, y al final de cada guía (sólo calculadoras).
- `partials/hero-image.php` (nuevo): `<picture>` con `width/height`, `loading="eager"` y alt
  es-PY `"{nombre} — materiales de construcción en Paraguay"`. Sin imagen no imprime nada.
- `partials/header.php`: `og:image` por página (imagen declarada → `og-default.jpg`).
  `partials/schema.php`: `Product.image` como URL ABSOLUTA y sólo si el archivo existe.
- Héroes partidos con la imagen a la derecha en ≥ 64rem en categoría, material, guía y home
  (la home agrupa foto + franja de datos en `.page-hero__aside`).
- `data/site.php`: clave documentada `hero_image` (vacía). Comentario de la clave opcional
  `image` en `categories.php`, `materials.php` y `guides.php` — sin valores, los cablea la 15.
- `tools/smoke.php`: toda `image` declarada tiene que existir y cumplir
  `^assets/img/[a-z0-9/_-]+\.(jpg|webp)$`. `tools/render-check.sh`: `/materiales/cemento/` y
  `/materiales/hierro/` listan "Guías relacionadas" con el enlace correcto.

## Decisions
- `related.php` recibe el slug propio Y el de la categoría: una guía que apunta a `hierro`
  aparece en la categoría y en sus seis materiales sin declarar seis veces lo mismo.
- Las calculadoras de una guía se resuelven por el slug de la guía (la fase 12 enlaza el
  exemplar en ambos sentidos), no por sus páginas de dinero: si no, cada guía de cemento
  arrastraría todas las calculadoras del rubro.
- `image_for()` toca disco (`is_file`) en vez de confiar en el dato: declarar y subir son dos
  pasos distintos y el sitio nunca puede quedar con un `<img>` roto en el héroe.
- Se agregó una regla CSS defensiva para que una casilla nunca se estire como campo de texto
  (lo que obligó al `gap` inline de la fase 10; el markup se deja como está).

## Known issues
- Todavía no hay ningún archivo de imagen en el repo: todo el motor está inerte hasta que
  Anton suba las 12 fotos de `docs/imagery-brief.md` (§7) y la fase 15 cablee los valores.
- `calculators_for()` devuelve `[]` hasta que exista `data/calculators.php` (fase 12).

## Verification
`php -l` limpio · `php tools/smoke.php` OK · `./tools/render-check.sh` OK con los checks
nuevos · **diff de HTML renderizado contra `origin/main`** en `/materiales/cemento/`,
`/materiales/hierro/`, una guía y `/`: las únicas diferencias son el `<div>` envolvente del
héroe y el bloque de relacionadas — cero cambio visual en páginas sin imagen · prueba
temporal con un JPG de 1200×630: héroe partido, herencia categoría → material, `og:image` y
`Product.image` absolutos (el archivo y los valores de prueba se revirtieron antes del
commit) · Playwright 360/390/1280: `scrollWidth === clientWidth`.
