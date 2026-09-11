# Fase 10 — Captación de proveedores (Opus, `phase/10-proveedores`)

Fecha: 2026-09-11 · plan §11.2 · decisión §1.17

## Built
- `proveedores/index.php` (nuevo): H1 "Recibí pedidos de cotización de tu rubro", propuesta de
  valor (pedidos con material/cantidad/zona, hasta 3 proveedores por pedido, se paga por
  pedido y no por publicidad), "Cómo funciona para proveedores" en 3 pasos, condiciones desde
  `site('supplier_pitch')`, lista de los 11 rubros activos con los 5 de lanzamiento marcados
  "buscamos proveedores ahora", FAQ visible de 4 ítems con `FAQPage` y `BreadcrumbList`.
- `partials/form-proveedor.php` (nuevo): empresa (requerida), rubros (casillas de las
  categorías activas, ≥ 1 requerido), ciudad, nombre, WhatsApp (requerido, validación PY),
  mensaje, consentimiento propio, honeypot y sello firmado reutilizados, `tipo=proveedor`
  oculto.
- `cotizar/enviar.php`: rama aditiva `tipo === 'proveedor'` después de la trampa de bots →
  valida, arma el payload de proveedor, loguea y redirige 303 a `/proveedores/?ok=1#gracias`.
- `partials/lead.php`: `lead_build_supplier_payload()` y `lead_supplier_rubros()` (aditivos).
- `data/site.php`: `consent_version_proveedor` (`proveedor-v1`), `supplier_pitch`,
  `supplier_categories_note`. `config.sample.php`: cómo se rutea por `fields.tipo` en el CRM.
- Política de privacidad: cláusula "Datos de proveedores" (finalidad, base, encargado, plazo).
- Enlace "Para proveedores" en la nav del header y del pie; `/proveedores/` en el sitemap.
- `tools/smoke.php`: unidades del payload de proveedor + guard del texto de consentimiento +
  guard de que el payload del COMPRADOR no tiene `tipo`. `tools/render-check.sh`: alta feliz,
  alta sin rubros, y que ninguna línea de comprador lleve `tipo`.

## Decisions
- El acuse del alta NO usa `/gracias/`: esa página promete cotizaciones que van a llegar, que
  no es lo que acaba de pasar. Va a `/proveedores/?ok=1#gracias`, mismo patrón PRG.
- Payload aparte (`lead_build_supplier_payload`) en vez de parametrizar el del comprador: el
  contrato del comprador es fundacional (plan §4.4) y así queda imposible tocarlo por error.
- Los 5 rubros con captación activa se escriben en `proveedores/index.php` citando plan §5: es
  una constante del plan, no un dato editorial, y `data/categories.php` es de otra fase.
- Sin CSS nuevo: la hoja la escribe la fase 11. El grupo de casillas queda fuera de
  `.lead-form__field` (esa clase estira cualquier input a 3 rem de alto, correcto para un
  campo de texto y roto para una casilla) con un único `gap` inline.
- No se tocó `.htaccess` ni `tools/router-cli.php`: `DirectoryIndex index.php` ya sirve
  `/proveedores/` en Apache y la regla genérica `/{dir}/` ya lo sirve en el router de CI
  (verificado con un 200 real).

## Known issues
- `supplier_pitch` está vacío: la página muestra el texto "te contamos las condiciones por
  WhatsApp" y no inventa ningún número. Input humano §7 de Anton.
- El alta de proveedor comparte la ventana de idempotencia por teléfono con los pedidos de
  cotización: un mismo número que pide cotización y se da de alta en la misma hora UTC manda
  la misma `idempotency_key`. En el CRM son igual dos registros distintos por `fields.tipo`,
  pero conviene tenerlo escrito.

## Verification
`php -l` limpio · `php tools/smoke.php` OK (incluidas las unidades nuevas de proveedor) ·
`./tools/render-check.sh` OK, con el bloque LEAD HANDLER del comprador intacto y pasando ·
Playwright 360/390/1280 sobre `/proveedores/` y `/proveedores/?ok=1`:
`scrollWidth === clientWidth` en todos.
