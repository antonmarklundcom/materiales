#!/usr/bin/env bash
# tools/render-check.sh — levanta el servidor embebido y verifica que el router sirva las
# rutas del criterio de salida de la fase 1: /, /materiales/, una categoría, un material,
# una guía, /sitemap.xml y un 404 real. Sin dependencias más allá de PHP.
set -euo pipefail

cd "$(dirname "$0")/.."
PORT="${PORT:-8123}"
BASE="http://127.0.0.1:${PORT}"

php -S "127.0.0.1:${PORT}" -t public_html tools/router-cli.php >/tmp/render-check.log 2>&1 &
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

if [ "${fail}" -ne 0 ]; then
  echo "RENDER CHECK FAIL"
  cat /tmp/render-check.log
  exit 1
fi
echo "RENDER CHECK OK"
