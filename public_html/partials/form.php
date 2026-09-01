<?php
/**
 * partials/form.php — el formulario de cotización (plan §3). Se incluye en /cotizar/ y en
 * cada página de categoría y de material.
 *
 * Variables opcionales que puede definir la página que lo incluye:
 *   $formSlug   slug de categoría o material a preseleccionar ('' = sin preselección)
 *   $formOrigen ruta interna a la que volver si el handler rechaza el envío
 *   $formTitle  encabezado del bloque
 *
 * Los campos y el texto de consentimiento son FUNDACIONALES (plan §4.4 y §8.6): cambiarlos
 * después del lanzamiento invalida la constancia de consentimiento guardada en el CRM.
 */

declare(strict_types=1);

$formSlug   = isset($formSlug) ? (string) $formSlug : '';
$formOrigen = lead_safe_path(isset($formOrigen) ? (string) $formOrigen : '/cotizar/');
$formTitle  = isset($formTitle) ? (string) $formTitle : 'Pedí tu cotización';
$maxProv    = (int) site('max_proveedores', 3);
$stamp      = lead_form_stamp();

// El handler vuelve con ?error= y SÓLO los campos no personales ya tipeados (material,
// cantidad, ciudad). Nombre, teléfono y mensaje NO viajan en la URL a propósito: la URL
// termina en el historial, en el Referer y en `page_location` de GA4, y ahí no van datos
// personales. Retipear el teléfono es justamente lo que se le está pidiendo al visitante.
$formError = (string) ($_GET['error'] ?? '');
$formErrors = [
    'telefono'      => 'Revisá el teléfono: necesitamos un número paraguayo, por ejemplo 0981 123 456.',
    'consentimiento'=> 'Para poder pasarle tu pedido a los proveedores necesitamos que marques la casilla.',
];
$old = static fn(string $key): string => (string) ($_GET[$key] ?? '');

// Preselección: la que fija la página manda; si no, la que vuelve del handler o ?m= de un
// enlace "pedí cotización de X".
if ($formSlug === '') {
    $formSlug = (string) ($_GET['m'] ?? '');
}

// Opciones del selector: categorías primero, y debajo sus materiales (namespace plano, §2).
$formCategories = categories_ordered();
?>
<section class="lead-form" id="cotizar">
  <h2 class="lead-form__title"><?= e($formTitle) ?></h2>
  <p class="lead-form__lead">
    Hasta <?= $maxProv ?> proveedores verificados te escriben por WhatsApp con su precio.
    Es gratis y sin compromiso.
  </p>

  <?php if (isset($formErrors[$formError])): ?>
  <p class="lead-form__error" role="alert"><?= e($formErrors[$formError]) ?></p>
  <?php endif; ?>

  <form class="lead-form__form" action="/cotizar/enviar.php" method="post" novalidate>
    <label class="lead-form__field">
      <span>¿Qué material necesitás?</span>
      <select name="material">
        <option value="">Elegí el material (o dejalo en blanco si no estás seguro)</option>
        <?php foreach ($formCategories as $categorySlug => $category): ?>
        <optgroup label="<?= e($category['name']) ?>">
          <option value="<?= e($categorySlug) ?>"<?= $formSlug === $categorySlug ? ' selected' : '' ?>><?= e($category['name']) ?> (toda la categoría)</option>
          <?php foreach (materials_in($categorySlug) as $materialSlug => $material): ?>
          <option value="<?= e($materialSlug) ?>"<?= $formSlug === $materialSlug ? ' selected' : '' ?>><?= e($material['name']) ?></option>
          <?php endforeach; ?>
        </optgroup>
        <?php endforeach; ?>
      </select>
    </label>

    <label class="lead-form__field">
      <span>¿Qué cantidad?</span>
      <input name="cantidad" type="text" maxlength="200" autocomplete="off"
             value="<?= e($old('cantidad')) ?>" placeholder="Ej: 30 bolsas, 2 camiones, 500 kg">
    </label>

    <label class="lead-form__field">
      <span>¿En qué ciudad o zona?</span>
      <input name="ciudad" type="text" maxlength="200" autocomplete="address-level2"
             value="<?= e($old('ciudad')) ?>" placeholder="Ej: Luque, Asunción, San Lorenzo">
    </label>

    <label class="lead-form__field">
      <span>Tu nombre</span>
      <input name="nombre" type="text" maxlength="200" required autocomplete="name"
             value="">
    </label>

    <label class="lead-form__field">
      <span>Tu WhatsApp <em>(por acá te pasan el precio)</em></span>
      <input name="telefono" type="tel" inputmode="tel" maxlength="30" required autocomplete="tel"
             placeholder="0981 123 456"
             <?= $formError === 'telefono' ? ' aria-invalid="true" autofocus' : '' ?>>
    </label>

    <label class="lead-form__field">
      <span>¿Algo más que tengan que saber? <em>(opcional)</em></span>
      <textarea name="mensaje" rows="3" maxlength="5000"></textarea>
    </label>

    <label class="lead-form__consent">
      <input type="checkbox" name="consentimiento" value="1" required>
      <span>
        Acepto que mis datos sean compartidos con proveedores del rubro para recibir
        cotizaciones. Ver la <a href="/politica-de-privacidad/">Política de privacidad</a>.
      </span>
    </label>

    <?php /* Honeypot: los bots lo completan, las personas no lo ven nunca. */ ?>
    <input name="website" tabindex="-1" autocomplete="off" aria-hidden="true"
           style="position:absolute;left:-9999px" value="">

    <?php /* Trampa de tiempo: sello firmado del momento del render (partials/lead.php). */ ?>
    <input type="hidden" name="ts" value="<?= e($stamp['ts']) ?>">
    <input type="hidden" name="tsg" value="<?= e($stamp['sig']) ?>">
    <input type="hidden" name="origen" value="<?= e($formOrigen) ?>">

    <button class="lead-form__submit" type="submit">Pedir cotización</button>
    <p class="lead-form__note">Sin costo. No publicamos tu teléfono en ningún lado.</p>
  </form>
</section>
