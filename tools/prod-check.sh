#!/usr/bin/env bash
# tools/prod-check.sh — R3: verificación MANUAL contra producción (no corre en CI: CI no
# llega al servidor, y render-check.sh sólo prueba tools/router-cli.php, no el .htaccess real).
#
#   bash tools/prod-check.sh                         # https://materiales.com.py
#   bash tools/prod-check.sh https://otro-host.test  # otra base (staging)
#
# Correrlo después de cada deploy que toque .htaccess, y cuando Hostinger cambie algo del
# plan. Sale con 1 si alguna ruta no responde lo esperado.
set -uo pipefail

BASE="${1:-https://materiales.com.py}"
fail=0

expect() { # ruta, status esperado, patrón opcional en el cuerpo
  local path="$1" expected="$2" pattern="${3:-}" status
  status="$(curl -sS -o /tmp/prod-check-body -w '%{http_code}' --max-time 20 "${BASE}${path}" 2>/dev/null || true)"
  if [[ "${status}" != "${expected}" ]]; then
    echo "  FAIL ${path} → ${status} (esperado ${expected})"; fail=1; return
  fi
  if [[ -n "${pattern}" ]] && ! grep -q -- "${pattern}" /tmp/prod-check-body; then
    echo "  FAIL ${path} → ${status} pero sin '${pattern}' en el cuerpo"; fail=1; return
  fi
  echo "  ok   ${path} (${status})"
}

redirect() { # URL completa, Location esperada
  local from="$1" to="$2" out
  out="$(curl -sS -o /dev/null -w '%{http_code} %{redirect_url}' --max-time 20 "${from}" 2>/dev/null || true)"
  if [[ "${out}" == "301 ${to}" ]]; then echo "  ok   ${from} → ${to}"; else echo "  FAIL ${from} → '${out}' (esperado 301 ${to})"; fail=1; fi
}

HOST="${BASE#https://}"
echo "REDIRECCIONES (S1)"
redirect "http://${HOST}/"                   "https://${HOST}/"
redirect "http://${HOST}/materiales/cemento/" "https://${HOST}/materiales/cemento/"
redirect "https://www.${HOST}/guias/"        "https://${HOST}/guias/"
redirect "http://www.${HOST}/"               "https://${HOST}/"
redirect "https://${HOST}/materiales/hierro" "https://${HOST}/materiales/hierro/"

echo "PÚBLICO (lo mismo que conviene vigilar con UptimeRobot, ver DEPLOY.md)"
expect "/"                  200 '<title>'
expect "/cotizar/"          200 'name="consentimiento"'
expect "/sitemap.xml"       200 '<urlset'
expect "/robots.txt"        200 'Sitemap:'
expect "/favicon.ico"       200
expect "/favicon.svg"       200 '<svg'
expect "/apple-touch-icon.png" 200

echo "INTERNO — tiene que ser 403 (bloque [F] de .htaccess y R4)"
for path in \
  /data/site.php /content/README.md /config/vendercrm.php /storage/leads.log \
  /storage/.form-secret /tools/smoke.php /tools/prod-check.sh /tests/mobile-overflow.mjs \
  /prompts/opus-1-foundation.md /docs/IMPROVEMENT-REPORT-2.md /DEPLOY.md /plan.md \
  /config.sample.php /partials/header.php /materiales/_index.php /.git/HEAD /.gitignore \
  /index.php.bak /index.php~ /backup.sql /error.log /deploy.sh; do
  expect "${path}" 403
done

echo "CABECERAS"
headers="$(curl -sSI --max-time 20 "${BASE}/" | tr -d '\r')"
for h in X-Content-Type-Options Referrer-Policy X-Frame-Options Permissions-Policy Strict-Transport-Security; do
  if grep -qi "^${h}:" <<<"${headers}"; then echo "  ok   ${h}"; else echo "  FAIL falta ${h}"; fail=1; fi
done

if [[ "${fail}" -ne 0 ]]; then echo "PROD CHECK FALLÓ"; exit 1; fi
echo "PROD CHECK OK"
