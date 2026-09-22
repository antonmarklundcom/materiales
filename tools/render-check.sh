#!/usr/bin/env bash
# tools/render-check.sh — levanta el servidor embebido y verifica dos cosas:
#
#   1. que el router sirva las rutas del criterio de salida de la fase 1 (/, /materiales/,
#      una categoría, un material, una guía, /sitemap.xml, un 404 real);
#   2. que el handler de leads de la fase 2 se comporte por HTTP: honeypot y trampa de tiempo
#      cortan en silencio, el teléfono inválido y el consentimiento sin marcar vuelven al
#      formulario, el camino feliz escribe leads.log y redirige a /gracias/, y dos envíos
#      idénticos producen la MISMA clave de idempotencia.
#
# Sin dependencias más allá de PHP y curl. No hay CRM configurado en CI: el handler degrada a
# leads.log-only (plan §4.5), que es exactamente el camino que se quiere ver acá.
set -euo pipefail

cd "$(dirname "$0")/.."
PORT="${PORT:-8123}"
BASE="http://127.0.0.1:${PORT}"

# storage/ temporal: el repo ES el docroot, así que usar ./storage acá pisaba el leads.log
# real si alguien corría este check en el servidor. partials/init.php lee esta variable; el
# servidor embebido y los `php -r` de abajo la heredan.
MATERIALES_STORAGE_DIR="$(mktemp -d)"
export MATERIALES_STORAGE_DIR
# Archivos de trabajo del propio check en el mismo dir temporal: con ${TMPD}/render-body fijo, dos
# render-check en paralelo (otro puerto, otro worktree) se pisaban las respuestas.
TMPD="${MATERIALES_STORAGE_DIR}"

php -S "127.0.0.1:${PORT}" -t . tools/router-cli.php >${TMPD}/render-check.log 2>&1 &
SERVER_PID=$!
trap 'kill "${SERVER_PID}" 2>/dev/null || true; rm -rf "${MATERIALES_STORAGE_DIR}"' EXIT

for _ in $(seq 1 40); do
  if curl -fsS -o /dev/null "${BASE}/" 2>/dev/null; then break; fi
  sleep 0.25
done

fail=0

check() { # ruta, status esperado, patrón que debe aparecer en el cuerpo
  local path="$1" expected="$2" pattern="${3:-}"
  local body status
  body="$(curl -sS -o ${TMPD}/render-body -w '%{http_code}' "${BASE}${path}")"
  status="${body}"
  if [ "${status}" != "${expected}" ]; then
    echo "  FAIL ${path}: status ${status}, esperaba ${expected}"
    fail=1
    return
  fi
  if [ -n "${pattern}" ] && ! grep -q "${pattern}" ${TMPD}/render-body; then
    echo "  FAIL ${path}: no encontré '${pattern}' en el cuerpo"
    fail=1
    return
  fi
  echo "  ok   ${path} (${status})"
}

absent() { # ruta, patrón que NO debe aparecer en el cuerpo (la ruta tiene que dar 200)
  local path="$1" pattern="$2" status
  status="$(curl -sS -o ${TMPD}/render-body -w '%{http_code}' "${BASE}${path}")"
  if [ "${status}" != "200" ] || grep -q -- "${pattern}" ${TMPD}/render-body; then
    echo "  FAIL ${path}: status ${status} o aparece '${pattern}' (no debería)"
    fail=1
    return
  fi
  echo "  ok   ${path} sin '${pattern}'"
}

echo "RENDER CHECK"
check "/"                          200 '<title>'
check "/materiales/"               200 'ItemList'
check "/materiales/hierro/"        200 'BreadcrumbList'
check "/materiales/piedra-bruta/"  200 '"@type":"FAQPage"'
# S3: Product sin offers/review/aggregateRating es un ítem inválido en Search Console.
absent "/materiales/piedra-bruta/" '"@type":"Product"'
# S10: Organization (no LocalBusiness) y WebSite con publisher.
check "/"                          200 '"@type":"Organization"'
check "/"                          200 '"publisher":{"@id":"https://materiales.com.py/#organization"}'
absent "/"                         'LocalBusiness'
# S2: íconos declarados y servidos.
check "/"                          200 'rel="icon" href="/favicon.svg"'
check "/favicon.svg"               200 '<svg'
check "/favicon.ico"               200 ''
check "/apple-touch-icon.png"      200 ''
# S4: /materiales/ tiene título y H1 propios (no los de la home) y lista cada material.
check "/materiales/"               200 '<h1>Catálogo de materiales de construcción</h1>'
check "/materiales/"               200 'href="/materiales/ladrillo-refractario/"'
# S9: los rubros "próxima" (noindex) no se enlazan desde la home ni desde el catálogo.
absent "/"                         'href="/materiales/electricidad/"'
absent "/materiales/"              'href="/materiales/electricidad/"'
# G2: pinturas se promovió a activa — se enlaza desde el catálogo, lista sus materiales y
# cada material nuevo sirve con su H1 "{Nombre} en Paraguay".
check "/materiales/"               200 'href="/materiales/pinturas/"'
check "/materiales/pinturas/"      200 '<h1>Pinturas en Paraguay</h1>'
check "/materiales/pinturas/"      200 '<h2>Materiales de Pinturas</h2>'
check "/materiales/pinturas/"      200 '"@type":"ItemList"'
for pint in "pintura-para-pared|Pintura para pared" "pintura-para-piso|Pintura para piso" \
            "barniz|Barniz para madera" "esmalte-sintetico|Esmalte sintético" \
            "sellador-para-pared|Sellador para pared"; do
  check "/materiales/pinturas/"    200 "href=\"/materiales/${pint%%|*}/\""
  check "/materiales/${pint%%|*}/" 200 "<h1>${pint#*|} en Paraguay</h1>"
done
check "/materiales/pinturas/"      200 'href="/materiales/pintura-antihumedad/"'
check "/sitemap.xml"               200 '/materiales/pinturas/'
# S5: el héroe declara sizes y prioridad alta.
check "/materiales/cemento-y-cal/" 200 'fetchpriority="high"'
check "/materiales/cemento-y-cal/" 200 'sizes="(min-width: 64rem) 40vw, 100vw"'
# S6/S8: js-nav inline antes del primer render; CSS/JS con versión.
check "/"                          200 "classList.add('js', 'js-nav')"
check "/"                          200 'href="/assets/css/site.css?v='
check "/calculadoras/hormigon-por-m3/" 200 'src="/assets/js/calc.js?v='
# S7: robots.txt ya no bloquea /gracias/ (tiene que poder leer su noindex).
absent "/robots.txt"               'Disallow: /gracias/'
# A1: página del sello de proveedor verificado (carpeta anidada: router-cli la sirve igual que
# el DirectoryIndex de Apache), con el código para copiar y la imagen servida.
check "/proveedores/verificado/"   200 'data-badge-snippet'
check "/proveedores/verificado/"   200 'sello-proveedor-verificado.svg'
check "/assets/img/sello-proveedor-verificado.svg" 200 'Proveedor verificado'
check "/proveedores/"              200 'href="/proveedores/verificado/"'
check "/sitemap.xml"               200 '/proveedores/verificado/'

# ---- PR F (frescura, E-E-A-T, formulario del héroe) ----
# S16: fechas visibles, lastmod en el sitemap, Article en guías y calculadoras.
check "/materiales/cemento/"       200 'Actualizado: <time datetime="20'
check "/sitemap.xml"               200 '<lastmod>20'
check "/guias/como-elegir-un-corralon/" 200 '"@type":"Article"'
check "/guias/como-elegir-un-corralon/" 200 '"datePublished":"20'
check "/calculadoras/hormigon-por-m3/" 200 '"author":{"@type":"Organization"'
# G6: glosario de obra — un término visible con ancla, su enlace al dueño y el DefinedTermSet.
check "/guias/glosario-de-obra-paraguay/" 200 '<dt id="millar"><dfn>millar</dfn></dt>'
check "/guias/glosario-de-obra-paraguay/" 200 'href="/materiales/tejuelon/"'
check "/guias/glosario-de-obra-paraguay/" 200 '"@type":"DefinedTermSet"'
check "/nosotros/"                 200 '<h1>Sobre Materiales.com.py</h1>'
check "/como-trabajamos/"          200 'Cómo verificamos a los proveedores'
check "/sitemap.xml"               200 '/como-trabajamos/'
# C3: formulario corto en el héroe, mismo handler, mismo consentimiento, sin mostrar errores
# del servidor (esos van en el formulario completo).
check "/materiales/cemento/"       200 'id="cotizar-rapido"'
check "/materiales/aridos/"        200 'data-lead-compact'
check "/materiales/cemento/?error=telefono" 200 'id="lead-form-error"'
if [ "$(curl -sS "${BASE}/materiales/cemento/" | grep -c 'Acepto que mis datos sean compartidos')" != "2" ]; then
  echo "  FAIL /materiales/cemento/ no tiene el consentimiento en los dos formularios"; fail=1
else
  echo "  ok   /materiales/cemento/ con el mismo consentimiento en los dos formularios"
fi
if [ "$(curl -sS "${BASE}/materiales/cemento/?error=telefono" | grep -c 'role="alert"')" != "1" ]; then
  echo "  FAIL el error del servidor aparece en más de un formulario"; fail=1
else
  echo "  ok   el error del servidor aparece sólo en el formulario completo"
fi

# ---- PR E (targeting on-page) ----
# S11/S13: el H1 lleva el término medido o el país, no el nombre pelado.
check "/materiales/tierra-gorda/"  200 '<h1>Tierra colorada (tierra gorda) en Paraguay</h1>'
check "/materiales/cemento/"       200 '<h1>Cemento en Paraguay</h1>'
check "/materiales/membrana-asfaltica/" 200 '<title>Membrana para techo (asfáltica) en Paraguay'
# S12: ningún título dice "Precio por" (la página no muestra precios).
absent "/materiales/cemento/"      '<title>[^<]*Precio por'
# S14: guía y calculadora con H1 distinto; la guía manda a la calculadora.
check "/guias/cuantas-bolsas-de-cemento-por-m2/" 200 '<h1>Cómo calcular las bolsas de cemento por m²</h1>'
check "/guias/cuantas-bolsas-de-cemento-por-m2/" 200 'href="/calculadoras/bolsas-de-cemento-por-m2/"'
# S15: páginas antes casi huérfanas reciben enlaces en prosa.
check "/materiales/ladrillo-prensado/" 200 'href="/materiales/ladrillo-refractario/"'
check "/materiales/piso-vinilico/" 200 'href="/materiales/mdf-fibrofacil/"'
# G3: materiales nuevos con volumen medido (improvement report #2).
check "/materiales/ducha-higienica/" 200 '<h1>Ducha higiénica en Paraguay</h1>'
check "/materiales/piso-parquet/"    200 '<h1>Piso parquet en Paraguay</h1>'
check "/materiales/metal-desplegado/" 200 '<h1>Metal desplegado en Paraguay</h1>'
# G8: intros de los hubs.
check "/guias/"                    200 'Estas guías responden'
check "/calculadoras/"             200 'Cada calculadora resuelve'

# ---- PR D (conversión) ----
# C2: botón bajo el resultado de la calculadora, y la barra pegajosa ancla al formulario de
# la misma página (antes mandaba a un /cotizar/ en blanco).
check "/calculadoras/bolsas-de-cemento-por-m2/" 200 'data-calc-cta-template="Cotizá estas {bolsas} bolsas →"'
check "/calculadoras/bolsas-de-cemento-por-m2/" 200 'cta-bar__primary" href="#cotizar"'
# C6: WhatsApp con el material ya escrito.
check "/materiales/cemento/"       200 'wa.me/595992279599?text=Hola%2C%20quiero%20cotizar%20cemento'
# C7: en /proveedores/ la barra es de proveedor y los rubros no llevan a páginas de comprador.
check "/proveedores/"              200 'href="#sumate" data-ev="cta_click" data-ev-loc="sticky-proveedores">Sumate como proveedor'
absent "/proveedores/"             'href="/materiales/hierro/"'
# C9: la guía manda a /cotizar/ con su material preseleccionado.
check "/guias/cuantas-bolsas-de-cemento-por-m2/" 200 'href="/cotizar/?m='
check "/cotizar/?m=cemento"        200 '<option value="cemento" selected>'
# C5/C10: validación en el navegador y marcas de obligatorio; el JS versionado.
check "/materiales/hierro/"        200 'data-lead-form'
check "/materiales/hierro/"        200 'class="lead-form__field is-required"'
check "/materiales/hierro/"        200 'src="/assets/js/forms.js?v='
# C4: banner compacto con Aceptar / Rechazar / Configurar.
check "/"                          200 'data-consent-configure'
check "/guias/"                    200 '<h1>'
check "/cotizar/"                  200 '<h1>'
check "/politica-de-privacidad/"   200 '<h1>'
check "/politica-de-privacidad/"   200 'automáticamente a los 12 meses'
check "/sitemap.xml"               200 '<urlset'
check "/materiales/no-existe-esto/" 404 'No encontramos'
check "/partials/header.php"       403 ''
check "/data/site.php"             403 ''
check "/content/README.md"         403 ''
check "/config/vendercrm.php"       403 ''
check "/storage/leads.log"         403 ''
check "/tools/smoke.php"           403 ''
check "/prompts/opus-1-foundation.md" 403 ''
check "/docs/imagery-brief.md"     403 ''
check "/tests/mobile-overflow.mjs" 403 ''
check "/.gitignore"                403 ''
# R4: respaldos, scripts, logs y dumps en cualquier ruta (existan o no).
check "/index.php.bak"             403 ''
check "/materiales/index.php.orig" 403 ''
check "/index.php~"                403 ''
check "/backup.sql"                403 ''
check "/error.LOG"                 403 ''
check "/deploy.sh"                 403 ''
check "/cotizar/"                  200 'name="consentimiento"'
check "/materiales/hierro/"        200 'action="/cotizar/enviar.php"'
# El formulario de cada página de dinero vuelve a SU página ante un error y le dice al CRM
# de qué página vino el lead (antes todas mandaban /cotizar/, header.php pisaba $canonical).
check "/materiales/hierro/"        200 'name="origen" value="/materiales/hierro/"'
check "/materiales/cemento/"       200 'name="origen" value="/materiales/cemento/"'
check "/calculadoras/hormigon-por-m3/" 200 'name="origen" value="/calculadoras/hormigon-por-m3/"'
# og:image apunta a un archivo real (antes era la ruta base sin extensión → 404).
check "/materiales/cemento/"       200 'og:image" content="https://materiales.com.py/assets/img/hero-cemento-y-cal-og.jpg"'
# Fase 9 — capa de conversión: la home explica el modelo y cierra en el formulario, y las
# páginas de dinero tienen el CTA del hero anclado al formulario que ya está en la página.
check "/"                          200 'Cómo funciona'
check "/"                          200 'name="consentimiento"'
check "/materiales/hierro/"        200 'href="#cotizar"'
check "/"                          200 'data-cta-bar'
# Fase 10 — landing de proveedores: existe, trae su formulario con el tipo oculto y entra
# en el sitemap.
check "/proveedores/"              200 'name="tipo"'
check "/proveedores/"              200 'Recibí pedidos de cotización de tu rubro'
check "/sitemap.xml"               200 '/proveedores/'
# Fase 11 — motor de enlaces cruzados: cemento está en el related[] de la guía de bolsas de
# cemento, así que su página tiene que listarla sin que la prosa la tipee.
check "/materiales/cemento/"       200 'Guías relacionadas'
check "/materiales/cemento/"       200 '/guias/cuantas-bolsas-de-cemento-por-m2/'
check "/materiales/hierro/"        200 'Guías relacionadas'
# Fase 12 — calculadoras: índice, exemplar (con su fórmula y su formulario), 404 real, y el
# enlace cruzado que la fase 11 calcula desde data/calculators.php.
check "/calculadoras/"             200 'ItemList'
check "/calculadoras/bolsas-de-cemento-por-m2/" 200 'data-calc'
check "/calculadoras/bolsas-de-cemento-por-m2/" 200 'name="consentimiento"'
check "/calculadoras/bolsas-de-cemento-por-m2/" 200 'Es una referencia'
# G1 — calculadoras nuevas.
check "/calculadoras/hierro-para-columnas/" 200 'data-calc'
check "/calculadoras/hierro-para-columnas/" 200 '<title>Calculadora de hierro para columnas | Paraguay'
check "/guias/cuanto-hierro-lleva-una-columna/" 200 'href="/calculadoras/hierro-para-columnas/"'
check "/calculadoras/durlock-por-m2/" 200 'data-calc'
check "/calculadoras/durlock-por-m2/" 200 '<title>Calculadora de placas de yeso y perfiles | Paraguay'
check "/calculadoras/membrana-por-m2/" 200 'data-calc'
check "/calculadoras/membrana-por-m2/" 200 '<title>Calculadora de rollos de membrana por m² | Paraguay'
check "/calculadoras/ceramica-por-m2/" 200 'data-calc'
check "/calculadoras/ceramica-por-m2/" 200 '<title>Calculadora de cajas de cerámica y porcelanato | Paraguay'
check "/calculadoras/chapas-para-techo/" 200 'data-calc'
check "/calculadoras/chapas-para-techo/" 200 '<title>Calculadora de chapas para techo: cuántas pedir | Paraguay</title>'
check "/calculadoras/tanque-de-agua-litros/" 200 'data-calc'
check "/calculadoras/tanque-de-agua-litros/" 200 '<title>Calculadora de litros del tanque de agua | Paraguay'
check "/calculadoras/no-existe/"   404 'No encontramos'
check "/materiales/cemento/"       200 'Calculadoras relacionadas'
check "/guias/cuantas-bolsas-de-cemento-por-m2/" 200 'Calculadoras relacionadas'
check "/sitemap.xml"               200 '/calculadoras/bolsas-de-cemento-por-m2/'

echo "LEAD HANDLER"

LOG="${MATERIALES_STORAGE_DIR}/leads.log"
: >"${LOG}"

# El sello firmado se genera con las MISMAS funciones que usa el formulario: si un cambio
# rompiera la firma, este check fallaría en vez de pasar por casualidad.
stamp() { # antigüedad en segundos → "ts tsg"
  php -r '
    require "partials/init.php";
    require "partials/lead.php";
    $s = lead_form_stamp(time() - (int) $argv[1]);
    echo $s["ts"], " ", $s["sig"];
  ' "$1"
}

post() { # descripción, status esperado, patrón esperado en Location, campos de curl…
  local what="$1" expected="$2" pattern="$3"; shift 3
  local status location
  status="$(curl -sS -o ${TMPD}/post-body -D ${TMPD}/post-head -w '%{http_code}' \
              "${BASE}/cotizar/enviar.php" "$@")"
  location="$(grep -i '^location:' ${TMPD}/post-head | tr -d '\r' | sed 's/^[Ll]ocation: *//' || true)"
  if [ "${status}" != "${expected}" ]; then
    echo "  FAIL ${what}: status ${status}, esperaba ${expected}"
    fail=1
    return
  fi
  if [ -n "${pattern}" ] && ! printf '%s' "${location}" | grep -q "${pattern}"; then
    echo "  FAIL ${what}: Location '${location}' no contiene '${pattern}'"
    fail=1
    return
  fi
  echo "  ok   ${what} (${status} → ${location})"
}

lines() { if [ -f "${LOG}" ]; then grep -c . "${LOG}" || true; else echo 0; fi; }

# GET al handler no es un envío: es alguien que llegó por un enlace o un crawler.
post "GET al handler no envía nada" 303 '/cotizar/' --get

read -r TS TSG <<<"$(stamp 30)"
VALID=(--data-urlencode "ts=${TS}" --data-urlencode "tsg=${TSG}"
       --data-urlencode "origen=/materiales/hierro/" --data-urlencode "material=hierro"
       --data-urlencode "nombre=Ana Benítez" --data-urlencode "cantidad=30 barras"
       --data-urlencode "ciudad=Luque")

# 1. Honeypot: 303 silencioso a /gracias/, sin postear nada y sin token de conversión.
before="$(lines)"
post "honeypot corta en silencio" 303 '^/gracias/$' \
  "${VALID[@]}" --data-urlencode "telefono=0981123456" \
  --data-urlencode "consentimiento=1" --data-urlencode "website=http://spam.example"

# 2. Trampa de tiempo: sello de hace 1s = bot.
read -r FAST_TS FAST_TSG <<<"$(stamp 1)"
post "submit demasiado rápido corta en silencio" 303 '^/gracias/$' \
  --data-urlencode "ts=${FAST_TS}" --data-urlencode "tsg=${FAST_TSG}" \
  --data-urlencode "telefono=0981123456" --data-urlencode "consentimiento=1"

# 3. Sello falsificado (un bot que postea directo, sin pasar por el formulario).
post "sello falsificado corta en silencio" 303 '^/gracias/$' \
  --data-urlencode "ts=${TS}" --data-urlencode "tsg=firma-inventada" \
  --data-urlencode "telefono=0981123456" --data-urlencode "consentimiento=1"

if [ "$(lines)" != "$((before + 3))" ]; then
  echo "  FAIL los descartes de bot deberían dejar 3 líneas en leads.log"
  fail=1
else
  echo "  ok   los 3 descartes quedaron registrados en leads.log"
fi
# Los 3 traen teléfono y consentimiento válidos: puede ser una persona (autocompletado en
# el campo trampa, pestaña vieja), así que se RETIENEN con el payload — nunca se envían.
if grep -q '"outcome":"enviado"' "${LOG}" || [ "$(grep -c '"outcome":"retenido"' "${LOG}")" != "3" ]; then
  echo "  FAIL los descartes de bot con teléfono válido deberían quedar retenidos (y nunca enviados)"
  fail=1
else
  echo "  ok   los descartes con teléfono válido quedaron retenidos con su payload, sin envío"
fi

# 3bis. Bot sin teléfono válido: descarte liso, sin payload.
before="$(lines)"
post "bot sin teléfono corta en silencio" 303 '^/gracias/$' \
  --data-urlencode "ts=${TS}" --data-urlencode "tsg=firma-inventada" \
  --data-urlencode "telefono=spam" --data-urlencode "consentimiento=1"
if [ "$(lines)" != "$((before + 1))" ] || tail -n 1 "${LOG}" | grep -q '"payload"'; then
  echo "  FAIL un bot sin teléfono válido debería registrarse como descarte sin payload"
  fail=1
else
  echo "  ok   bot sin teléfono: descarte sin payload"
fi

# 3ter. Sello de hace 49 h (pestaña abierta de un día para el otro): se retiene, no se tira.
read -r OLD_TS OLD_TSG <<<"$(stamp 176400)"
post "sello vencido con teléfono válido se retiene" 303 '^/gracias/$' \
  --data-urlencode "ts=${OLD_TS}" --data-urlencode "tsg=${OLD_TSG}" \
  --data-urlencode "material=cemento" --data-urlencode "telefono=0981 555 444" \
  --data-urlencode "consentimiento=1"
if tail -n 1 "${LOG}" | grep -q '"outcome":"retenido","reason":"vencido"'; then
  echo "  ok   sello vencido quedó retenido con motivo 'vencido'"
else
  echo "  FAIL sello vencido no quedó retenido"
  fail=1
fi

# 4. Teléfono inválido: vuelve al formulario, y NO arrastra datos personales en la URL.
post "teléfono inválido vuelve al formulario" 303 'error=telefono' \
  "${VALID[@]}" --data-urlencode "telefono=123" --data-urlencode "consentimiento=1"
if grep -i '^location:' ${TMPD}/post-head | grep -qi 'nombre\|telefono='; then
  echo "  FAIL el redirect de error arrastra datos personales en la URL"
  fail=1
fi

# 5. Consentimiento sin marcar: se rechaza aunque todo lo demás esté bien (Ley 7593).
post "consentimiento sin marcar vuelve al formulario" 303 'error=consentimiento' \
  "${VALID[@]}" --data-urlencode "telefono=0981123456"

# 6. Camino feliz: 303 a /gracias/ con material y token, y una línea nueva en leads.log.
before="$(lines)"
post "camino feliz redirige a /gracias/" 303 '^/gracias/?m=hierro&k=[0-9a-f]\{16\}$' \
  "${VALID[@]}" --data-urlencode "telefono=0981 123 456" --data-urlencode "consentimiento=1"
if [ "$(lines)" != "$((before + 1))" ]; then
  echo "  FAIL el camino feliz no escribió su línea en leads.log"
  fail=1
else
  echo "  ok   el camino feliz escribió leads.log"
fi
# C8: la referencia que ve el visitante en /gracias/ (mitad del token k) queda en su línea.
K="$(grep -i '^location:' ${TMPD}/post-head | tr -d '\r' | sed -E 's/.*k=([0-9a-f]{16}).*/\1/')"
REF="$(printf '%s' "${K:0:8}" | tr 'a-f' 'A-F')"
if [ -n "${REF}" ] && tail -n 1 "${LOG}" | grep -q "\"ref\":\"${REF}\""; then
  echo "  ok   leads.log guarda la referencia ${REF} del pedido"
else
  echo "  FAIL leads.log no guarda la referencia del pedido (k=${K})"
  fail=1
fi

# 7. Doble envío = MISMA clave de idempotencia. Es lo que impide que un doble clic o un
#    reintento por timeout cree un segundo contacto en el CRM.
post "envío duplicado" 303 '^/gracias/?m=hierro&k=' \
  "${VALID[@]}" --data-urlencode "telefono=0981 123 456" --data-urlencode "consentimiento=1"
KEYS="$(tail -n 2 "${LOG}" | grep -o '"idempotency_key":"[0-9a-f]*"' | sort -u | wc -l)"
if [ "${KEYS}" != "1" ]; then
  echo "  FAIL dos envíos idénticos produjeron ${KEYS} claves de idempotencia distintas (esperaba 1)"
  fail=1
else
  echo "  ok   dos envíos idénticos comparten la clave de idempotencia"
fi

# 7a. Mismo teléfono, OTRO material, misma hora: es otro pedido, no un duplicado.
post "segundo pedido de otro material" 303 '^/gracias/?m=cemento&k=' \
  "${VALID[@]}" --data-urlencode "material=cemento" \
  --data-urlencode "telefono=0981 123 456" --data-urlencode "consentimiento=1"
KEYS="$(tail -n 2 "${LOG}" | grep -o '"idempotency_key":"[0-9a-f]*"' | sort -u | wc -l)"
if [ "${KEYS}" != "2" ]; then
  echo "  FAIL dos pedidos de materiales distintos comparten la clave de idempotencia"
  fail=1
else
  echo "  ok   dos materiales distintos producen dos claves"
fi
if tail -n 1 "${LOG}" | grep -q '"page_url":"https://materiales.com.py/materiales/hierro/"'; then
  echo "  ok   el lead registra la página de origen real"
else
  echo "  FAIL el lead no registra la página de origen (origen=/materiales/hierro/)"
  fail=1
fi

# 7bis. Alta de proveedor: mismo handler, otro camino. Vuelve a /proveedores/?ok=1 y la
#       línea del log lleva tipo=proveedor, empresa y rubros — nunca material ni categoría.
read -r PTS PTSG <<<"$(stamp 30)"
post "alta de proveedor redirige a /proveedores/?ok=1" 303 '^/proveedores/?ok=1#gracias$' \
  --data-urlencode "ts=${PTS}" --data-urlencode "tsg=${PTSG}" \
  --data-urlencode "tipo=proveedor" --data-urlencode "empresa=Corralón San Blas" \
  --data-urlencode "rubros[]=hierro" --data-urlencode "rubros[]=aridos" \
  --data-urlencode "ciudad=Luque" --data-urlencode "nombre=Ana Benítez" \
  --data-urlencode "telefono=0981 123 456" --data-urlencode "consentimiento=1"

if ! grep -q '"tipo":"proveedor"' "${LOG}"; then
  echo "  FAIL la línea del alta de proveedor no lleva tipo=proveedor"
  fail=1
else
  echo "  ok   el alta de proveedor quedó registrada con tipo=proveedor"
fi
if ! grep -q '"rubros":"hierro,aridos"' "${LOG}"; then
  echo "  FAIL la línea del alta de proveedor no lleva los rubros marcados"
  fail=1
else
  echo "  ok   el alta de proveedor guarda los rubros marcados"
fi
if ! grep -q "\"consent\":\"proveedor-v1 @ " "${LOG}"; then
  echo "  FAIL el alta de proveedor no guarda su propia versión de consentimiento"
  fail=1
else
  echo "  ok   el alta de proveedor guarda la versión proveedor-v1"
fi

# Sin rubros marcados no hay alta: el rubro es lo que define qué pedidos le llegan.
read -r PTS2 PTSG2 <<<"$(stamp 30)"
post "alta de proveedor sin rubros vuelve al formulario" 303 'error=rubros' \
  --data-urlencode "ts=${PTS2}" --data-urlencode "tsg=${PTSG2}" \
  --data-urlencode "tipo=proveedor" --data-urlencode "empresa=Corralón San Blas" \
  --data-urlencode "nombre=Ana Benítez" --data-urlencode "telefono=0981 123 456" \
  --data-urlencode "consentimiento=1"

# El camino del COMPRADOR no puede haberse contaminado: ninguna línea de comprador lleva tipo.
if grep -v '"tipo":"proveedor"' "${LOG}" | grep -q '"tipo"'; then
  echo "  FAIL una línea del camino del comprador lleva 'tipo' en el payload"
  fail=1
else
  echo "  ok   el payload del comprador sigue sin 'tipo'"
fi

# 8. El consentimiento queda registrado con versión y timestamp (constancia, plan §8.6).
if ! grep -q '"consent":"proveedores-v1 @ ' "${LOG}"; then
  echo "  FAIL leads.log no guarda la constancia de consentimiento (versión + timestamp)"
  fail=1
else
  echo "  ok   leads.log guarda versión y timestamp del consentimiento"
fi

# 9. El payload nunca lleva ruteo: eso se configura en el registro del sitio en el CRM.
if grep -qE '"(pipeline|stage|owner|tag)":' "${LOG}"; then
  echo "  FAIL el payload incluye ruteo (pipeline/stage/owner/tag)"
  fail=1
else
  echo "  ok   el payload no incluye pipeline/stage/owner/tag"
fi

# 10. /gracias/ con token firmado declara el evento de conversión; sin token, o con uno
#     tipeado a mano, no declara nada.
TOKEN="$(php -r 'require "partials/init.php"; require "partials/lead.php"; echo lead_conversion_token();')"
check "/gracias/?m=hierro&k=${TOKEN}" 200 'matLead'
# C8: referencia visible, en el WhatsApp precargado, y materiales para sumar al pedido.
check "/gracias/?m=hierro&k=${TOKEN}" 200 "Referencia de tu pedido: <strong>$(printf '%s' "${TOKEN:0:8}" | tr 'a-f' 'A-F')</strong>"
check "/gracias/?m=hierro&k=${TOKEN}" 200 'ref.%20'
check "/gracias/?m=hierro&k=${TOKEN}" 200 'href="/cotizar/?m=varilla-de-hierro"'
check "/gracias/?m=hierro&k=${TOKEN}" 200 'Guardá nuestro número'
if curl -sS "${BASE}/gracias/" | grep -q 'matLead'; then
  echo "  FAIL /gracias/ sin token declara una conversión"
  fail=1
else
  echo "  ok   /gracias/ sin token no declara conversión"
fi
if curl -sS "${BASE}/gracias/?k=0123456789abcdef" | grep -q 'matLead'; then
  echo "  FAIL /gracias/ con un token inventado declara una conversión"
  fail=1
else
  echo "  ok   /gracias/ con token inventado no declara conversión"
fi

# 11. El handler nunca tocó el storage/ del repo (el de producción, si esto corre allá).
if [ -f storage/leads.log ] && grep -q 'Ana Benítez' storage/leads.log 2>/dev/null; then
  echo "  FAIL render-check escribió en ./storage/leads.log"
  fail=1
fi

if [ "${fail}" -ne 0 ]; then
  echo "RENDER CHECK FAIL"
  cat ${TMPD}/render-check.log
  exit 1
fi
echo "RENDER CHECK OK"
