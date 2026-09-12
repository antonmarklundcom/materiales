# Decisiones pendientes (no bloquean el build)

Este archivo lo leen las fases Sonnet (plan §4). Cada entrada es algo que no se pudo resolver
dentro de los límites de la fase y necesita una decisión humana o un dato que no está en el
repo. Nunca bloquea: la fase sigue con el workaround que anota.

## Fase 13 — Calculadoras + guías wave 3

1. **`ladrillos-por-m2` no incluye ladrillo hueco.** El plan §11.5 proponía las opciones
   "común / hueco 8 / hueco 12 / bloque". Las medidas de cara del ladrillo hueco (largo × alto)
   no están documentadas en `content/materiales/ladrillo-hueco.php` — esa página sólo publica el
   espesor (8/12/18 cm), no el largo ni el alto de la pieza, y las variantes reales cambian
   bastante entre olerías. Inventar una medida de cara para poder ofrecer la opción rompería la
   regla de no fabricar cifras. **Workaround**: el selector ofrece las 3 medidas de ladrillo
   común ya publicadas (22×11, 25×12, 28×14 cm) más el bloque de hormigón estándar de manual
   (39×19 cm) — ver CONTENT-SPEC §12.1. Si alguien mide y confirma una medida de cara real y
   representativa para el ladrillo hueco paraguayo (por ejemplo, preguntándole a una olería o a
   un proveedor), se agrega como quinta opción sin tocar el resto de la calculadora.
2. **El bloque de hormigón (39 × 19 cm) usa una medida estándar de manual, no una verificada en
   una olería paraguaya.** Es la medida nominal habitual en construcción (bloque de 40×20×20 cm
   con junta de 1 cm), citada en manuales de albañilería, pero nadie la confirmó contra un
   proveedor local. Si un proveedor paraguayo usa otra medida de cara, ajustar la tabla
   `alto_cm`/`largo_cm` de `data/calculators.php['ladrillos-por-m2']` es un cambio de una línea.
