# content/

Prosa por página, en PHP plano (`<?php ?>` sólo si hace falta escapar algo; en general es
HTML). El router incluye el archivo si existe y, si no, muestra un aviso de "en preparación".

```
content/categorias/{slug}.php   ← cuerpo de /materiales/{categoria}/
content/materiales/{slug}.php   ← cuerpo de /materiales/{material}/
content/guias/{slug}.php        ← cuerpo de /guias/{slug}/
```

Reglas: el `<h1>` lo imprime el router, no el archivo de contenido (un solo H1 por página).
Empezá en `<h2>`. Las FAQ visibles salen de `faq[]` en los archivos de datos, para que el
`FAQPage` JSON-LD y el texto visible no puedan divergir.

La prosa se escribe en las fases 5 y 6; las fases 1–3 dejan estos directorios vacíos a
propósito.
