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
