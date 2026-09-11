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

php -S "127.0.0.1:${PORT}" -t . tools/router-cli.php >/tmp/render-check.log 2>&1 &
SERVER_PID=$!
trap 'kill "${SERVER_PID}" 2>/dev/null || true' EXIT

for _ in $(seq 1 40); do
  if curl -fsS -o /dev/null "${BASE}/" 2>/dev/null; then break; fi
  sleep 0.25
done

fail=0

check() { # ruta, status esperado, patrón que debe aparecer en el cuerpo
  local path="$1" expected="$2" pattern="${3:-}"
  local body status
  body="$(curl -sS -o /tmp/render-body -w '%{http_code}' "${BASE}${path}")"
  status="${body}"
  if [ "${status}" != "${expected}" ]; then
    echo "  FAIL ${path}: status ${status}, esperaba ${expected}"
    fail=1
    return
  fi
  if [ -n "${pattern}" ] && ! grep -q "${pattern}" /tmp/render-body; then
    echo "  FAIL ${path}: no encontré '${pattern}' en el cuerpo"
    fail=1
    return
  fi
  echo "  ok   ${path} (${status})"
}

echo "RENDER CHECK"
check "/"                          200 '<title>'
check "/materiales/"               200 'ItemList'
check "/materiales/hierro/"        200 'BreadcrumbList'
check "/materiales/piedra-bruta/"  200 '"@type":"Product"'
check "/guias/"                    200 '<h1>'
check "/cotizar/"                  200 '<h1>'
check "/politica-de-privacidad/"   200 '<h1>'
check "/sitemap.xml"               200 '<urlset'
check "/materiales/no-existe-esto/" 404 'No encontramos'
check "/partials/header.php"       403 ''
check "/cotizar/"                  200 'name="consentimiento"'
check "/materiales/hierro/"        200 'action="/cotizar/enviar.php"'
# Fase 9 — capa de conversión: la home explica el modelo y cierra en el formulario, y las
# páginas de dinero tienen el CTA del hero anclado al formulario que ya está en la página.
check "/"                          200 'Cómo funciona'
check "/"                          200 'name="consentimiento"'
check "/materiales/hierro/"        200 'href="#cotizar"'
check "/"                          200 'data-cta-bar'

echo "LEAD HANDLER"

LOG="$(cd "$(dirname "$0")/.." && pwd)/storage/leads.log"
mkdir -p "$(dirname "${LOG}")"
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
  status="$(curl -sS -o /tmp/post-body -D /tmp/post-head -w '%{http_code}' \
              "${BASE}/cotizar/enviar.php" "$@")"
  location="$(grep -i '^location:' /tmp/post-head | tr -d '\r' | sed 's/^[Ll]ocation: *//' || true)"
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
if grep -q '"outcome":"enviado"' "${LOG}" || grep -q '"payload"' "${LOG}"; then
  echo "  FAIL un descarte de bot armó payload — no debe postearse nada al CRM"
  fail=1
fi

# 4. Teléfono inválido: vuelve al formulario, y NO arrastra datos personales en la URL.
post "teléfono inválido vuelve al formulario" 303 'error=telefono' \
  "${VALID[@]}" --data-urlencode "telefono=123" --data-urlencode "consentimiento=1"
if grep -i '^location:' /tmp/post-head | grep -qi 'nombre\|telefono='; then
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

# 7. Doble envío = MISMA clave de idempotencia. Es lo que impide que un doble clic o un
#    reintento por timeout cree un segundo contacto en el CRM.
post "envío duplicado" 303 '^/gracias/?m=hierro&k=' \
  "${VALID[@]}" --data-urlencode "telefono=0981 123 456" --data-urlencode "consentimiento=1"
KEYS="$(grep -o '"idempotency_key":"[0-9a-f]*"' "${LOG}" | sort -u | wc -l)"
if [ "${KEYS}" != "1" ]; then
  echo "  FAIL dos envíos idénticos produjeron ${KEYS} claves de idempotencia distintas (esperaba 1)"
  fail=1
else
  echo "  ok   dos envíos idénticos comparten la clave de idempotencia"
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

# 10. /gracias/ con token declara el evento de conversión; sin token no declara nada.
check "/gracias/?m=hierro&k=0123456789abcdef" 200 'matLead'
if curl -sS "${BASE}/gracias/" | grep -q 'matLead'; then
  echo "  FAIL /gracias/ sin token declara una conversión"
  fail=1
else
  echo "  ok   /gracias/ sin token no declara conversión"
fi

if [ "${fail}" -ne 0 ]; then
  echo "RENDER CHECK FAIL"
  cat /tmp/render-check.log
  exit 1
fi
echo "RENDER CHECK OK"
