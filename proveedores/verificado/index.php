<?php
/**
 * proveedores/verificado/index.php — sello "Proveedor verificado" (improvement report #2, A1).
 *
 * Cada proveedor verificado puede poner el sello en su web, con un enlace a esta página: es un
 * backlink por proveedor y, para el comprador, una forma de ver qué significa el sello. Lo que
 * la página afirma sobre la verificación es exactamente lo que ya dice /proveedores/ (una
 * llamada para verificar que la empresa existe y vende lo que dice) — nada más.
 */

declare(strict_types=1);

require dirname(__DIR__, 2) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/schema.php';

$badgeUrl    = url('/assets/img/sello-proveedor-verificado.svg');
$pageUrl     = url('/proveedores/verificado/');
$snippet     = '<a href="' . $pageUrl . '" target="_blank" rel="noopener">'
    . '<img src="' . $badgeUrl . '" alt="Proveedor verificado en ' . site('brand') . '" width="240" height="72" loading="lazy">'
    . '</a>';
$breadcrumbs = [['Inicio', '/'], ['Para proveedores', '/proveedores/'], ['Sello de proveedor verificado', null]];

page([
    'title'       => 'Sello de proveedor verificado | Materiales.com.py',
    'meta'        => 'Qué significa el sello "Proveedor verificado en Materiales.com.py" y cómo ponerlo en tu web si ya sos proveedor verificado.',
    'canonical'   => '/proveedores/verificado/',
    'breadcrumbs' => $breadcrumbs,
    'schema'      => [schema_breadcrumbs($breadcrumbs)],
    'body_class'  => 'page-verificado',
]);

require PUBLIC_ROOT . '/partials/header.php';
?>
<div class="page-hero band--dark grain bleed">
  <div class="wrap">
    <h1>Sello de proveedor verificado</h1>
    <p class="lead">Qué significa y cómo ponerlo en tu web si ya sos proveedor verificado.</p>
  </div>
</div>
<div class="field wrap">
  <div class="field__panel">
    <p class="badge-preview"><img src="/assets/img/sello-proveedor-verificado.svg" alt="Proveedor verificado en <?= e(site('brand')) ?>" width="240" height="72"></p>

    <div class="prose">
      <h2>Qué significa</h2>
      <p>
        Que la empresa se sumó en <a href="/proveedores/">Para proveedores</a> con sus rubros y su
        zona, y que la llamamos para verificar que existe y que vende lo que dice. Por eso puede
        recibir pedidos de cotización de esos rubros. El sello no es una recomendación de precio
        ni de calidad: el precio y las condiciones las pasa cada proveedor, y el comprador compara.
      </p>

      <h2>Cómo ponerlo en tu web</h2>
      <p>
        Si ya sos proveedor verificado, copiá este código y pegalo en tu sitio (en el pie o en la
        página de contacto). La imagen se sirve desde nuestro sitio, así que siempre se ve igual.
      </p>
    </div>

    <label class="lead-form__field badge-snippet">
      <span>Código del sello</span>
      <textarea readonly rows="4" data-badge-snippet><?= e($snippet) ?></textarea>
    </label>
    <p><button type="button" class="btn btn--primary" data-copy-snippet data-ev="cta_click" data-ev-loc="sello-copiar">Copiar código</button></p>

    <div class="prose">
      <h2>Reglas de uso</h2>
      <ul>
        <li>Sólo lo pueden usar empresas que completaron la verificación.</li>
        <li>Si una empresa deja de estar verificada, tiene que sacar el sello de su web.</li>
        <li>Si ves el sello en una empresa que no parece estar verificada, avisanos por
          <a href="/contacto/">Contacto</a>.</li>
      </ul>
      <p>¿Todavía no estás? <a href="/proveedores/#sumate">Sumate como proveedor</a>.</p>
    </div>
  </div>
</div>
<script>
(function () {
  var btn = document.querySelector('[data-copy-snippet]');
  var box = document.querySelector('[data-badge-snippet]');
  if (!btn || !box) return;
  btn.addEventListener('click', function () {
    box.select();
    var done = function () { btn.textContent = 'Copiado ✓'; };
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(box.value).then(done, function () { document.execCommand('copy'); done(); });
    } else {
      document.execCommand('copy'); done();
    }
  });
})();
</script>
<?php require PUBLIC_ROOT . '/partials/footer.php'; ?>
