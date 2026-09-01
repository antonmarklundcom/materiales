# materiales.com.py

Sitio de contenido SEO en español paraguayo (voseo) para materiales de construcción. Capta
pedidos de cotización y los entrega a proveedores. Stack: HTML estático + PHP en hosting
compartido de Hostinger. Sin Node, sin framework, sin base de datos.

El plan completo (decisiones, taxonomía, flujo de leads, fases) vive en [`plan.md`](plan.md).
Los prompts por fase están en [`prompts/`](prompts/).

## Estructura

```
public_html/          ← docroot en el servidor
  index.php             homepage
  materiales/index.php  router de /materiales/ y /materiales/{slug}/
  guias/index.php       router de /guias/ y /guias/{slug}/
  cotizar/ gracias/ contacto/ politica-de-privacidad/ 404.php
  sitemap.php           se sirve como /sitemap.xml (reescritura)
  robots.txt  .htaccess
  partials/             init, header, footer, schema, banner de cookies
  assets/               css, js, img
data/                 ← FUERA del docroot: site, categories, materials, guides (arrays PHP)
content/              ← FUERA del docroot: prosa por página (fases 5–6)
config/               ← FUERA del docroot y del repo: config/vendercrm.php (ver config.sample.php)
storage/              ← FUERA del docroot y del repo: leads.log
tools/                ← smoke test, router de desarrollo, render check
```

### El namespace de slugs es plano

`/materiales/{slug}/` sirve **tanto** categorías (`/materiales/hierro/`) como materiales
(`/materiales/piedra-bruta/`). La jerarquía vive en la miga de pan y los enlaces internos,
no en la URL. `tools/smoke.php` falla si un slug se repite entre `data/categories.php` y
`data/materials.php`.

`status` controla la publicación: `activa` entra en el sitemap y es indexable; `proxima`
renderiza un aviso, va con `noindex` y no entra en el sitemap.

## Desarrollo local

```sh
php -S 127.0.0.1:8080 -t public_html tools/router-cli.php   # http://127.0.0.1:8080/
php tools/smoke.php                                          # integridad de los datos
./tools/render-check.sh                                      # rutas + sitemap + 404
```

`tools/router-cli.php` replica las reescrituras de `public_html/.htaccess`. Si tocás una
regla, tocá las dos.

## CI y deploy

- CI (`.github/workflows/ci.yml`): un solo job en cada PR — `php -l`, smoke y render check.
- Deploy: push a `main` → webhook Git de Hostinger. GitHub Actions nunca despliega.
- Detalle de servidor: [`DEPLOY.md`](DEPLOY.md).
