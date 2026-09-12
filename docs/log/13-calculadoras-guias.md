# Fase 13 — Calculadoras + guías wave 3 (Sonnet, `phase/13-calculadoras-guias`)

Fecha: 2026-09-12 · plan §11.5

## Built
- Tres calculadoras nuevas en `data/calculators.php` + `content/calculadoras/`:
  `hormigon-por-m3` (cemento/arena/ripio/agua por m³, estructural 1:2:3 o contrapiso 1:3:5),
  `ladrillos-por-m2` (piezas de ladrillo común o bloque de hormigón por m² según medida de
  cara y junta) y `revoque-y-mortero` (cal + cemento + arena por m² con mortero de cal 1:1:6).
  Las tres reusan dosificaciones ya publicadas en CONTENT-SPEC §12.1; ninguna inventa una cifra.
- Seis guías nuevas en `data/guides.php` + `content/guias/`, todas `activa` desde este PR:
  `de-que-depende-el-costo-de-construir-en-paraguay`, `como-hacer-un-computo-metrico`,
  `como-elegir-un-corralon`, `chapa-o-teja-que-techo-conviene`, `cuanto-hierro-lleva-una-columna`,
  `como-impermeabilizar-una-losa`.
- `CONTENT-SPEC.md` §12.1: addendum documentando las medidas de cara del ladrillo común y del
  bloque de hormigón estándar que usa `ladrillos-por-m2`, y el 5 % de piezas extra por roturas
  (merma de mampostería, distinta del 10 % de una mezcla).
- Escribí a mano el exemplar de cada tipo (`hormigon-por-m3` y
  `de-que-depende-el-costo-de-construir-en-paraguay`) y repartí el resto en 7 subagentes Sonnet
  en paralelo (2 calculadoras + 5 guías), cada uno con su entrada de datos ya cerrada y las
  reglas de CONTENT-SPEC §12/§6 pegadas en el prompt.

## Decisions
- `ladrillos-por-m2` **no** incluye ladrillo hueco (el plan sugería "común / hueco 8 / hueco 12 /
  bloque"): sus medidas de cara no están documentadas en este repo y no se inventaron. Anotado en
  `docs/decisions-needed.md` #1, con el workaround (3 medidas de ladrillo común + bloque
  estándar) y cómo agregar la quinta opción si alguien confirma la medida real.
- El bloque de hormigón usa la medida estándar de manual (39 × 19 cm de cara), no una verificada
  con una olería paraguaya — `docs/decisions-needed.md` #2.
- `chapa-o-teja-que-techo-conviene` se escribió un nivel por encima de la guía ya publicada
  `que-chapa-conviene-para-techo` (que compara variantes de chapa entre sí): la nueva compara la
  familia chapa contra la familia teja (peso/estructura, estética, mantenimiento, costo de
  instalación) y cada guía enlaza a la otra para no competir por la misma búsqueda.
- `cuanto-hierro-lleva-una-columna` nunca publica una cifra de "tantas barras" o "tantos kilos"
  por columna: la cantidad es una decisión de cálculo estructural, no un dato de manual. La guía
  lo dice explícitamente y remite siempre al calculista.
- Los enlaces cruzados calculadora↔guía se resuelven desde `related[]` de la CALCULADORA (nunca
  al revés, porque `guides.php[].related[]` sólo puede apuntar a materiales/categorías —
  `tools/smoke.php` ya lo exige): `hormigon-por-m3` enlaza a `cuanta-arena-y-ripio-por-m3-de-
  hormigon` y a `cuanto-hierro-lleva-una-columna`; `ladrillos-por-m2` a `ladrillo-comun-vs-hueco`
  y a `como-hacer-un-computo-metrico`; `revoque-y-mortero` a `como-revocar-una-pared`.

## Known issues
- Ver `docs/decisions-needed.md` #1 y #2 (ladrillo hueco sin medida; bloque de hormigón con
  medida de manual, no verificada localmente).
- Conteo de palabras de prosa (sin el bloque JSON ni el markup): las tres calculadoras entre
  437–482 (rango 350–500 OK); las seis guías entre 722–908 (rango 600–900 OK, `como-hacer-un-
  computo-metrico` quedó en el límite superior).

## Verification
`find . -name '*.php' | xargs -n1 php -l` limpio · `php tools/smoke.php` → SMOKE OK (13
categorías, 64 materiales, 14 guías, 4 calculadoras, 77 slugs únicos) · `./tools/render-check.sh`
OK sin cambios (mismos checks de la fase 12) · las 9 páginas nuevas devuelven 200 con el
servidor embebido (`php -S` + `tools/router-cli.php`) · grep de la lista cerrada de marcas
(§11.2) y de precios/moneda sobre los 9 archivos nuevos: sin coincidencias.
