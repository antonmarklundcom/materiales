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

## Replay de leads fallidos (fase 14, decisión §1.26)

`tools/replay-leads.php` reintenta los leads que quedaron con `outcome: 'fallo_crm'` en
`storage/leads.log` — es el "replay manual" del que habla el plan §3, ahora automático por
cron. Es idempotente por `idempotency_key`: nunca reenvía un lead cuyo `storage/replayed.log`
ya tenga `ok:true` para esa clave, así que correrlo de más no duplica nada en el CRM.

1. En hPanel → Advanced → **Cron Jobs**, agregar (ajustar el usuario/ruta real del hosting):

   ```
   0 * * * * /usr/bin/php /home/USUARIO/domains/materiales.com.py/public_html/tools/replay-leads.php >> /home/USUARIO/logs/replay-leads.log 2>&1
   ```

2. Confirmar que el binario de PHP en el cron es el mismo PHP 8.x del sitio (`/usr/bin/php`
   puede apuntar a una versión distinta en hosting compartido — verificar con `php -v` por
   SSH antes de pegar la línea).
3. `storage/replayed.log` no está en el repo (vive junto a `leads.log`, mismo `.htaccess` de
   `storage/` lo bloquea por web) — no hace falta crearlo a mano, el script lo crea en el
   primer reintento.
4. Probar en seco antes de confiar en el cron: `php tools/replay-leads.php --dry-run` lista
   lo pendiente sin enviar nada ni tocar `replayed.log`.

## Headers de seguridad (fase 14, decisión §1.24)

Después de desplegar, verificar con `curl -sI https://materiales.com.py/` que la respuesta
trae `X-Content-Type-Options`, `Referrer-Policy`, `X-Frame-Options` y `Permissions-Policy`.
`Strict-Transport-Security` queda comentada en `.htaccess` a propósito: activarla exige que
el SSL del dominio ya esté confirmado y estable — recién ahí descomentar esa línea y
redeployar. No hay `Content-Security-Policy` (ver `.htaccess` y plan §10 Backlog).
