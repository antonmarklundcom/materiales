# Deploy — Hostinger (hosting compartido, PHP 8.x)

El deploy es un **webhook Git de Hostinger sobre `main`**. GitHub Actions nunca despliega
(plan §1.10); Actions sólo corre el check requerido del PR.

## Layout en el servidor

El repositorio se clona en la carpeta del dominio, **no** dentro de `public_html`, para que
`data/`, `content/`, `config/` y `storage/` queden fuera del docroot:

```
~/domains/materiales.com.py/
  public_html/     ← docroot (viene del repo)
  data/            ← del repo
  content/         ← del repo
  tools/  prompts/ plan.md ...
  config/          ← NO está en el repo, se crea a mano
  storage/         ← NO está en el repo, se crea a mano
```

## Puesta a punto (una vez)

1. hPanel → sitio → Advanced → **Git** → Connect with GitHub, repo
   `antonmarklundcom/materiales`, rama `main`, directorio de instalación
   `~/domains/materiales.com.py/` (la carpeta del dominio, no `public_html`).
2. Copiar el webhook que muestra Hostinger y pegarlo en GitHub → repo → Settings →
   Webhooks (content type `application/json`, evento *push*).
3. PHP 8.x activo en hPanel; confirmar que `curl` está habilitado (lo usa el handler de la
   fase 2).
4. Crear los directorios que no vienen del repo:

   ```sh
   mkdir -p ~/domains/materiales.com.py/{config,storage}
   chmod 750 ~/domains/materiales.com.py/config
   chmod 770 ~/domains/materiales.com.py/storage
   cp ~/domains/materiales.com.py/config.sample.php \
      ~/domains/materiales.com.py/config/vendercrm.php
   ```

   Después editar `config/vendercrm.php` con la URL y la API key del sitio en VenderCRM
   (fase 2). Ese archivo **nunca** se commitea.
5. Verificar que `https://materiales.com.py/data/site.php` devuelva 403/404 y no el archivo.
   Si lo devuelve, el repo quedó clonado dentro del docroot: mové la instalación.

## Checklist de go-live (fase 6)

- [ ] `data/site.php` → `staging_noindex` en `false`
- [ ] NAP real cargado en `data/site.php` (razón social, RUC, IVA, dirección, teléfonos,
      horarios) — hasta entonces esos bloques no se renderizan
- [ ] DNS de materiales.com.py apuntando a Hostinger, SSL emitido
- [ ] `https://materiales.com.py/sitemap.xml` responde 200 y sólo lista páginas `activa`
- [ ] `robots.txt` accesible y enlazando el sitemap
- [ ] Sitemap enviado en Search Console
