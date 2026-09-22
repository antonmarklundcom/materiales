# Deploy — Hostinger (hosting compartido, PHP 8.x)

El deploy es un **webhook Git de Hostinger sobre `main`**. GitHub Actions nunca despliega
(plan §1.10); Actions sólo corre el check requerido del PR.

## Layout en el servidor

En hosting compartido, la integración Git de hPanel **sólo permite instalar dentro de
`public_html`** — no hay campo para elegir un directorio padre distinto. Por eso el repo
entero (incluidos `data/`, `content/`, `tools/`, `prompts/`, `docs/` y los `.md`) vive
DENTRO del docroot:

```
~/domains/materiales.com.py/
  public_html/            ← Git de Hostinger clona el repo ACÁ directamente (repo = docroot)
    index.php  materiales/  guias/  cotizar/  gracias/  contacto/  ...
    partials/  assets/  .htaccess  robots.txt  sitemap.php
    data/  content/  tools/  prompts/  docs/  *.md    ← no públicos, bloqueados por .htaccess
    config/                ← NO está en el repo, se crea a mano DENTRO de public_html
    storage/               ← NO está en el repo, se crea a mano DENTRO de public_html
```

`data/`, `content/`, `config/`, `storage/`, `tools/`, `prompts/`, `docs/`, todo `*.md` y
`config.sample.php` quedan no-públicos por las reglas `RewriteRule ... [F,L]` al principio
de `.htaccess`, NO por estar fuera del docroot (no se puede: Hostinger fuerza el clone
dentro de `public_html` en este plan). Si alguna vez cambia el plan de hosting y aparece la
opción de elegir un directorio padre distinto, se puede volver al layout con `data/` etc.
fuera del docroot — pero mientras tanto el aislamiento lo da `.htaccess`, así que **cualquier
carpeta interna nueva tiene que sumarse a esa lista de `.htaccess`**.

## Puesta a punto (una vez)

1. hPanel → sitio → Advanced → **Git** → Connect with GitHub, repo
   `antonmarklundcom/materiales`, rama `main`. El "Root directory" que muestra Hostinger es
   `public_html` y no es editable en este plan — es lo esperado, no hay que cambiarlo.
2. Copiar el webhook que muestra Hostinger y pegarlo en GitHub → repo → Settings →
   Webhooks (content type `application/json`, evento *push*).
3. PHP 8.x activo en hPanel; confirmar que `curl` está habilitado (lo usa el handler de la
   fase 2).
4. Crear los directorios que no vienen del repo, DENTRO de `public_html`:

   ```sh
   cd ~/domains/materiales.com.py/public_html
   mkdir -p config storage
   chmod 750 config
   chmod 770 storage
   cp config.sample.php config/vendercrm.php
   cp tools/.htaccess config/.htaccess
   cp tools/.htaccess storage/.htaccess
   ```

   La `.htaccess` raíz ya bloquea `/config/` y `/storage/` por nombre (regla `[F]`), pero
   como esas dos carpetas no vienen del repo se les suma el mismo `Require all denied` de
   `tools/.htaccess` — defensa en profundidad, igual que `data/` y `content/`: si algún
   redeploy dejara una `.htaccess` raíz vieja o rota, la de la propia carpeta sigue
   protegiendo.

   Después editar `config/vendercrm.php` con la URL y la API key del sitio en VenderCRM
   (fase 2). Ese archivo **nunca** se commitea.
5. Verificar que estas rutas devuelvan 403 y no el contenido real:
   - `https://materiales.com.py/data/site.php`
   - `https://materiales.com.py/config/vendercrm.php`
   - `https://materiales.com.py/DEPLOY.md`
   - `https://materiales.com.py/tools/smoke.php`

   Si alguna devuelve el archivo en vez de 403, revisar que `mod_rewrite` esté activo y que
   `.htaccess` se haya desplegado tal cual (no lo haya pisado un `.htaccess` viejo que
   Hostinger no borra solo al redeployar).

## Checklist de go-live (fase 6)

- [ ] `data/site.php` → `staging_noindex` en `false`
- [ ] NAP real cargado en `data/site.php` (razón social, RUC, IVA, dirección, teléfonos,
      horarios) — hasta entonces esos bloques no se renderizan
- [ ] DNS de materiales.com.py apuntando a Hostinger, SSL emitido
- [ ] `https://materiales.com.py/sitemap.xml` responde 200 y sólo lista páginas `activa`
- [ ] `robots.txt` accesible y enlazando el sitemap
- [ ] Sitemap enviado en Search Console

## Avisos de lead (`config/vendercrm.php`)

Sin estos valores, un pedido que queda en modo sólo-log o que el CRM rechaza no le llega a
nadie. Van en `config/vendercrm.php` (plantilla en `config.sample.php`):

| Clave | Qué es |
|---|---|
| `notify_email` | Casilla que recibe el aviso de cada lead (vía `mail()` de PHP, Hostinger lo trae). |
| `notify_from` | Remitente; vacío = `no-reply@materiales.com.py`. Conviene que sea un buzón real del dominio para que no caiga en spam. |
| `telegram_bot_token` | Token del bot (crearlo con @BotFather). |
| `telegram_chat_id` | Chat donde llega el aviso (escribirle al bot y leer el id en `https://api.telegram.org/bot<TOKEN>/getUpdates`). |
| `notify_on` | `todos` = aviso por cada lead; `problemas` = sólo `solo_log`, `fallo_crm` y `retenido`. |
| `leads_retention_months` | Meses que se guardan los `storage/leads-AAAA-MM.log` rotados (por defecto 12). Si se cambia, cambiar el texto de "Conservación" en la política de privacidad (la página ya muestra el valor de la config). |

Email y Telegram pueden ir juntos. Los mismos canales los usan la alerta de
`tools/replay-leads.php` y el resumen diario de `tools/lead-digest.php`.

Probar después de cargarlos: `php tools/lead-digest.php` tiene que llegar al email/Telegram
(aunque sea con "0 pedidos").

## Crons (hPanel → Advanced → Cron Jobs)

Ajustar `USUARIO` y la ruta real. Confirmar antes por SSH que `/usr/bin/php -v` es el mismo PHP
8.x del sitio (en hosting compartido puede apuntar a otra versión). Crear `~/logs/` una vez.

```
# Reintento de leads al CRM, cada hora
0 * * * *   /usr/bin/php /home/USUARIO/domains/materiales.com.py/public_html/tools/replay-leads.php >> /home/USUARIO/logs/replay-leads.log 2>&1
# Resumen diario de leads (10:00 UTC = 7:00 en Paraguay; ajustar a la hora del servidor)
0 10 * * *  /usr/bin/php /home/USUARIO/domains/materiales.com.py/public_html/tools/lead-digest.php >> /home/USUARIO/logs/lead-digest.log 2>&1
# Rotación mensual de leads.log + retención + limpieza de storage/throttle/, diario
30 4 * * *  /usr/bin/php /home/USUARIO/domains/materiales.com.py/public_html/tools/maintenance.php >> /home/USUARIO/logs/maintenance.log 2>&1
```

Los tres son sólo CLI (devuelven 403 por web y además `tools/` está bloqueada).

## Replay de leads fallidos (fase 14, decisión §1.26; R1)

`tools/replay-leads.php` reintenta los leads que quedaron con `outcome: 'fallo_crm'` en
`storage/leads.log` (y en el último `leads-AAAA-MM.log` rotado). Es idempotente por
`idempotency_key`: nunca reenvía un lead cuyo `storage/replayed.log` ya tenga `ok:true` para
esa clave, así que correrlo de más no duplica nada en el CRM.

- **Tope de reintentos**: un rechazo 4xx permanente del CRM (400, 401, 403, 404, 409, 422…) no
  se reintenta nunca; 408, 425, 429, 5xx y errores de red se reintentan hasta
  `--max-attempts=N` veces (24 por defecto, un día de cron horario). Los abandonados se listan
  como `ABANDONADO idempotency_key=…` en cada corrida: cargarlos a mano en VenderCRM.
- **Alerta**: si una corrida real termina con exit ≠ 0 (1 = quedó un fallo o se abandonó un
  lead; 2 = CRM sin configurar o log ilegible) avisa por email/Telegram, como mucho una vez por
  día por código de salida (`storage/replay-alert.json`). `--no-alert` la apaga. Mientras el
  CRM no esté configurado, el cron va a avisar una vez por día que está en exit 2: es el
  recordatorio de que los leads siguen en `solo_log`.
- **Backlog `solo_log`**: también reintenta `outcome: 'solo_log'` (leads que llegaron mientras
  `config/vendercrm.php` no existía), pero sólo los de menos de 72 horas por defecto, para no
  volcarle de golpe a los proveedores un pedido de hace semanas. Los `fallo_crm` no tienen
  tope de antigüedad.

### El día que se configura el CRM por primera vez

El sitio está en `solo_log` desde el go-live (2026-09-16). Para no perder ese backlog:

1. Cargar `url` y `api_key` en `config/vendercrm.php`.
2. Ver qué se mandaría, sin enviar nada:
   `php tools/replay-leads.php --max-age-hours=0 --dry-run`
   (`--max-age-hours=0` = sin tope de antigüedad).
3. Revisar esa lista contra lo que ya se cargó a mano en VenderCRM (los avisos por email/
   Telegram de cada lead). Lo ya cargado a mano no se debería reenviar: si hace falta
   excluirlo, anotar su clave en `storage/replayed.log` como
   `{"idempotency_key":"…","ok":true,"status":200,"error":"manual"}`.
4. Correrlo de verdad: `php tools/replay-leads.php --max-age-hours=0`.
5. Recién después activar el cron horario de arriba.

`storage/replayed.log` y `storage/replay-alert.json` no están en el repo (viven junto a
`leads.log`, el `.htaccess` de `storage/` los bloquea por web) — el script los crea solo.

## Resumen diario (`tools/lead-digest.php`)

Cuenta los pedidos de las últimas 24 h por resultado (`enviado`, `solo_log`, `fallo_crm`,
`retenido`, `descartado`, con el motivo de retenidos y descartados) y lo manda por
email/Telegram. Se manda aunque sean 0: así también confirma que cron y avisos siguen vivos.
`--dry-run` sólo imprime; `--hours=N` cambia la ventana.

## Rotación y retención (`tools/maintenance.php`, R5)

- `storage/leads.log` se rota el primer día del mes (la primera corrida después de que cambió
  el mes) a `storage/leads-AAAA-MM.log`, con permisos 0640. Correrlo todos los días es
  inofensivo.
- Los rotados de más de `leads_retention_months` (12 por defecto) se borran. Es lo que dice
  la sección "Conservación" de la política de privacidad.
- Borra las huellas de IP de `storage/throttle/` más viejas que la ventana del límite por IP
  (10 minutos): antes quedaba un archivo por IP para siempre.
- Un pedido de supresión (Ley 7593) se atiende a mano: borrar la persona en VenderCRM y sus
  líneas en `storage/leads*.log` (buscar por teléfono).

`--dry-run` muestra qué haría.

## Monitoreo (R2)

Crear dos monitores gratis en UptimeRobot (o similar), tipo *Keyword*, cada 5 minutos:

- `https://materiales.com.py/` — keyword `<title>`.
- `https://materiales.com.py/cotizar/` — keyword `name="consentimiento"` (si el formulario deja
  de renderizar, se pierden leads aunque la home ande).

Avisos al mismo email/Telegram de los leads.

## Verificación contra producción (`tools/prod-check.sh`, R3)

Las reglas de bloqueo están en `.htaccess` (producción) y replicadas en
`tools/router-cli.php` (dev/CI); CI verifica con `tools/check-rewrites.php` que las listas
coincidan, pero sólo el servidor real prueba el `.htaccess`. Después de cada deploy que toque
`.htaccess`, desde cualquier máquina con bash + curl:

```sh
bash tools/prod-check.sh                  # https://materiales.com.py
```

Chequea que las páginas públicas respondan (con el formulario), que todo lo interno y los
respaldos (`*.bak`, `*~`, `*.sql`, `*.log`, `*.sh`…) den 403 y que estén las cabeceras de
seguridad.

## Headers de seguridad (fase 14, decisión §1.24; S1)

Después de desplegar, verificar con `curl -sI https://materiales.com.py/` que la respuesta
trae `X-Content-Type-Options`, `Referrer-Policy`, `X-Frame-Options`, `Permissions-Policy` y
`Strict-Transport-Security` (o correr `bash tools/prod-check.sh`, que además prueba las
redirecciones). No hay `Content-Security-Policy` (ver `.htaccess` y plan §10 Backlog).

**https y sin www (S1).** `.htaccess` redirige con 301 `http://` → `https://` y `www.` → el
dominio sin www, sin depender del toggle "Force HTTPS" de hPanel. HSTS va con
`max-age=31536000`, sin `includeSubDomains` ni `preload`. Si alguna vez el certificado deja de
renovarse, los navegadores que ya visitaron el sitio no van a poder entrar por http durante
ese año: renovar el SSL es la solución, no sacar la cabecera. Si se agrega un subdominio con
SSL propio y se quiere `includeSubDomains`, primero confirmar que TODOS los subdominios
(webmail, etc.) sirven https válido.
