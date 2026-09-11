<?php
/**
 * partials/form-proveedor.php — formulario de alta de proveedores (plan §11.2, decisión
 * §1.17). Postea al MISMO handler que el formulario del comprador (/cotizar/enviar.php) con
 * un `tipo=proveedor` oculto: el ruteo por `fields.tipo` se configura en VenderCRM, nunca
 * acá (plan §3).
 *
 * El texto de consentimiento de esta página NO es el del comprador: un proveedor consiente
 * que lo contactemos por pedidos de su rubro, no que lo compartan con proveedores. Versión
 * propia en data/site.php → consent_version_proveedor. Cambiarlo es una parada §4.4 y además
 * rompe el guard de tools/smoke.php.
 *
 * Honeypot y sello firmado son los mismos de partials/form.php: la trampa de bots no se
 * duplica, se reutiliza (partials/lead.php).
 */

declare(strict_types=1);

$provStamp      = lead_form_stamp();
$provCategories = array_filter(categories_ordered(), 'is_published');
$provError      = (string) ($_GET['error'] ?? '');
$provErrors     = [
    'telefono'       => 'Revisá el teléfono: necesitamos un número paraguayo, por ejemplo 0981 123 456.',
    'consentimiento' => 'Para poder contactarte necesitamos que marques la casilla.',
    'empresa'        => 'Escribí el nombre de tu empresa o corralón.',
    'rubros'         => 'Marcá al menos un rubro: es lo que define qué pedidos te llegan.',
];
$provOld = static fn(string $key): string => (string) ($_GET[$key] ?? '');
$provOldRubros = array_filter(explode(',', $provOld('rubros')));
?>
<section class="lead-form card card--accent" id="sumate">
  <h2 class="lead-form__title">Sumate como proveedor</h2>
  <p class="lead-form__lead">
    Cargá tu rubro y tu zona. Te escribimos por WhatsApp para verificar la empresa y
    explicarte cómo llegan los pedidos.
  </p>

  <?php if (isset($provErrors[$provError])): ?>
  <p class="lead-form__error" role="alert"><?= e($provErrors[$provError]) ?></p>
  <?php endif; ?>

  <form class="lead-form__form" action="/cotizar/enviar.php" method="post" novalidate>
    <input type="hidden" name="tipo" value="proveedor">

    <label class="lead-form__field">
      <span>Nombre de la empresa o corralón</span>
      <input name="empresa" type="text" maxlength="200" required autocomplete="organization"
             value="<?= e($provOld('empresa')) ?>"
             <?= $provError === 'empresa' ? ' aria-invalid="true" autofocus' : '' ?>>
    </label>

    <?php // Grupo de casillas sin <fieldset> (el borde por defecto del navegador rompe el
          // sistema visual) y FUERA de .lead-form__field: esa clase estira cualquier input
          // que tenga adentro a 3rem de alto, que es lo correcto para un campo de texto y
          // no para una casilla. El gap va inline porque site.css lo escribe la fase 11. ?>
    <div class="lead-form__field">
      <span id="rubros-label">¿Qué rubros vendés?</span>
      <p class="lead-form__note">Marcá todos los que trabajes: sólo te llegan pedidos de esos rubros.</p>
    </div>
    <div role="group" aria-labelledby="rubros-label" style="display:grid;gap:.5rem">
      <?php foreach ($provCategories as $provSlug => $provCategory): ?>
      <label class="lead-form__consent">
        <input type="checkbox" name="rubros[]" value="<?= e($provSlug) ?>"<?= in_array($provSlug, $provOldRubros, true) ? ' checked' : '' ?>>
        <span><?= e($provCategory['name']) ?></span>
      </label>
      <?php endforeach; ?>
    </div>

    <label class="lead-form__field">
      <span>¿Desde qué ciudad entregás?</span>
      <input name="ciudad" type="text" maxlength="200" autocomplete="address-level2"
             value="<?= e($provOld('ciudad')) ?>" placeholder="Ej: Luque, Asunción, San Lorenzo">
    </label>

    <label class="lead-form__field">
      <span>Tu nombre</span>
      <input name="nombre" type="text" maxlength="200" required autocomplete="name" value="">
    </label>

    <label class="lead-form__field">
      <span>Tu WhatsApp <em>(por acá te escribimos)</em></span>
      <input name="telefono" type="tel" inputmode="tel" maxlength="30" required autocomplete="tel"
             placeholder="0981 123 456"
             <?= $provError === 'telefono' ? ' aria-invalid="true" autofocus' : '' ?>>
    </label>

    <label class="lead-form__field">
      <span>¿Algo más que tengamos que saber? <em>(opcional)</em></span>
      <textarea name="mensaje" rows="3" maxlength="5000" placeholder="Ej: entregamos con camión propio hasta 40 km"></textarea>
    </label>

    <label class="lead-form__consent">
      <input type="checkbox" name="consentimiento" value="1" required>
      <span>
        Acepto que Materiales.com.py guarde mis datos para contactarme sobre pedidos de
        cotización de mi rubro. Ver la <a href="/politica-de-privacidad/">Política de privacidad</a>.
      </span>
    </label>

    <?php /* Honeypot: los bots lo completan, las personas no lo ven nunca. */ ?>
    <input name="website" tabindex="-1" autocomplete="off" aria-hidden="true"
           style="position:absolute;left:-9999px" value="">

    <?php /* Trampa de tiempo: sello firmado del momento del render (partials/lead.php). */ ?>
    <input type="hidden" name="ts" value="<?= e($provStamp['ts']) ?>">
    <input type="hidden" name="tsg" value="<?= e($provStamp['sig']) ?>">
    <input type="hidden" name="origen" value="/proveedores/">

    <button class="lead-form__submit btn btn--primary" type="submit" data-ev="form_submit" data-ev-loc="proveedores">Quiero recibir pedidos</button>
    <p class="lead-form__note">No publicamos tus datos en el sitio. Te contactamos nosotros.</p>
  </form>
</section>
