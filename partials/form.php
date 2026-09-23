<?php
/**
 * partials/form.php — el formulario de cotización (plan §3). Se incluye en /cotizar/ y en
 * cada página de categoría y de material.
 *
 * Variables opcionales que puede definir la página que lo incluye:
 *   $formSlug   slug de categoría o material a preseleccionar ('' = sin preselección)
 *   $formOrigen ruta interna a la que volver si el handler rechaza el envío
 *   $formTitle  encabezado del bloque
 *   $formList   C11 "Pegá tu lista": 'off' (por defecto), 'toggle' (enlace que abre el modo
 *               lista) u 'open' (el mensaje ya es la lista). Sólo lo usa /cotizar/. La lista
 *               viaja en el MISMO campo `mensaje`: el payload del CRM no cambia.
 *
 * Los campos y el texto de consentimiento son FUNDACIONALES (plan §4.4 y §8.6): cambiarlos
 * después del lanzamiento invalida la constancia de consentimiento guardada en el CRM.
 */

declare(strict_types=1);

$formSlug   = isset($formSlug) ? (string) $formSlug : '';
$formOrigen = lead_safe_path(isset($formOrigen) ? (string) $formOrigen : '/cotizar/');
$formTitle  = isset($formTitle) ? (string) $formTitle : 'Pedí tu cotización';
// C3: 'hero' = versión corta en el héroe de material/categoría. Arranca con cantidad y
// WhatsApp y se despliega (forms.js) al resto de los campos, consentimiento incluido: MISMO
// handler, MISMOS campos y MISMO texto de consentimiento que el formulario completo. Sin JS se
// ve completa. Los errores del servidor se muestran sólo en el formulario completo (#cotizar).
$formVariant = isset($formVariant) ? (string) $formVariant : 'full';
$isHero      = $formVariant === 'hero';
$formList    = $isHero ? 'off' : (isset($formList) ? (string) $formList : 'off');
$maxProv    = (int) site('max_proveedores', 3);
$stamp      = lead_form_stamp();

// El handler vuelve con ?error= y SÓLO los campos no personales ya tipeados (material,
// cantidad, ciudad). Nombre, teléfono y mensaje NO viajan en la URL a propósito: la URL
// termina en el historial, en el Referer y en `page_location` de GA4, y ahí no van datos
// personales. Retipear el teléfono es justamente lo que se le está pidiendo al visitante.
$formError = $isHero ? '' : (string) ($_GET['error'] ?? '');
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
<section class="lead-form card card--accent<?= $isHero ? ' lead-form--hero' : '' ?><?= $formList === 'open' ? ' lead-form--list' : '' ?>" id="<?= $isHero ? 'cotizar-rapido' : 'cotizar' ?>">
  <?php if ($isHero): ?>
  <h2 class="lead-form__title"><?= e($formTitle) ?></h2>
  <p class="lead-form__lead lead-form__benefits">Gratis · hasta <?= $maxProv ?> proveedores verificados · normalmente responden en el día</p>
  <?php else: ?>
  <h2 class="lead-form__title"><?= e($formTitle) ?></h2>
  <p class="lead-form__lead">
    Hasta <?= $maxProv ?> proveedores verificados te escriben por WhatsApp con su precio.
    Es gratis y sin compromiso.
  </p>
  <?php endif; ?>

  <?php if (isset($formErrors[$formError])): ?>
  <p class="lead-form__error" id="lead-form-error" role="alert"><?= e($formErrors[$formError]) ?></p>
  <?php endif; ?>

  <?php /* novalidate: la validación la hace assets/js/forms.js (C5) con mensajes propios, y
         el servidor la repite siempre. */ ?>
  <form class="lead-form__form" action="/cotizar/enviar.php" method="post" novalidate data-lead-form<?= $isHero ? ' data-lead-compact' : '' ?>>
    <p class="lead-form__legend">Los campos con <span aria-hidden="true">*</span> son obligatorios.</p>
<?php
// Cada campo se arma una vez y se imprime en el orden de la variante: el completo sigue el
// orden de siempre; el del héroe pone primero cantidad y WhatsApp y guarda el resto (con el
// consentimiento) en el bloque que se despliega.
$formFields = [];
ob_start(); ?>
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
<?php $formFields['material'] = (string) ob_get_clean();
ob_start(); ?>
    <label class="lead-form__field">
      <span>¿Qué cantidad?</span>
      <input name="cantidad" type="text" maxlength="200" autocomplete="off"
             value="<?= e($old('cantidad')) ?>" placeholder="Ej: 30 bolsas, 2 camiones, 500 kg">
    </label>
<?php $formFields['cantidad'] = (string) ob_get_clean();
ob_start(); ?>
    <label class="lead-form__field">
      <span>¿En qué ciudad o zona?</span>
      <input name="ciudad" type="text" maxlength="200" autocomplete="address-level2"
             value="<?= e($old('ciudad')) ?>" placeholder="Ej: Luque, Asunción, San Lorenzo">
    </label>
<?php $formFields['ciudad'] = (string) ob_get_clean();
ob_start(); ?>
    <label class="lead-form__field">
      <span>Tu nombre</span>
      <input name="nombre" type="text" maxlength="200" autocomplete="name"
             value="">
    </label>
<?php $formFields['nombre'] = (string) ob_get_clean();
ob_start(); ?>
    <label class="lead-form__field is-required">
      <span>Tu WhatsApp <em>(por acá te pasan el precio)</em></span>
      <input name="telefono" type="tel" inputmode="tel" maxlength="30" required autocomplete="tel"
             placeholder="0981 123 456"
             <?= $formError === 'telefono' ? ' aria-invalid="true" aria-describedby="lead-form-error" autofocus' : '' ?>>
    </label>
<?php $formFields['telefono'] = (string) ob_get_clean();
ob_start(); ?>
    <p class="lead-form__list-toggle"><a href="/cotizar/?lista=1#cotizar" data-lead-list-toggle data-ev="list_paste_open" data-ev-loc="cotizar">¿Tenés una lista de materiales? Pegala entera acá</a></p>
<?php $formFields['listToggle'] = (string) ob_get_clean();
ob_start(); ?>
    <label class="lead-form__field" data-lead-list-field>
      <?php if ($formList === 'open'): ?>
      <span data-lead-list-label>Pegá tu lista de materiales <em>(una línea por material, con la cantidad)</em></span>
      <textarea name="mensaje" rows="10" maxlength="5000" placeholder="Ej:&#10;30 bolsas de cemento&#10;2 m³ de arena lavada&#10;1 millar de ladrillo hueco de 12"></textarea>
      <?php else: ?>
      <span data-lead-list-label>¿Algo más que tengan que saber? <em>(opcional)</em></span>
      <textarea name="mensaje" rows="3" maxlength="5000"></textarea>
      <?php endif; ?>
    </label>
<?php $formFields['mensaje'] = (string) ob_get_clean();
ob_start(); ?>
    <label class="lead-form__consent is-required">
      <input type="checkbox" name="consentimiento" value="1" required
             <?= $formError === 'consentimiento' ? ' aria-invalid="true" aria-describedby="lead-form-error"' : '' ?>>
      <span>
        Acepto que mis datos sean compartidos con proveedores del rubro para recibir
        cotizaciones. Ver la <a href="/politica-de-privacidad/">Política de privacidad</a>.
      </span>
    </label>
<?php $formFields['consent'] = (string) ob_get_clean();
// C11: en modo lista el mensaje (la lista) va primero; material y cantidad quedan opcionales
// al final. En modo 'toggle' el enlace a la lista abre el formulario.
// Rediseño: la home (sin material en la URL) abre el héroe con material + cantidad + WhatsApp
// ($formLeadFields); las páginas de dinero ya saben el material y siguen con cantidad +
// WhatsApp. Sólo cambia el ORDEN visual: el set de campos y sus nombres son siempre los mismos.
$formLeadFields = $isHero && isset($formLeadFields) ? array_values(array_intersect((array) $formLeadFields, array_keys($formFields))) : ['cantidad', 'telefono'];
$formOrder = match (true) {
    $isHero              => [$formLeadFields, array_values(array_diff(['material', 'cantidad', 'ciudad', 'nombre', 'telefono', 'mensaje', 'consent'], $formLeadFields))],
    $formList === 'open' => [['mensaje', 'ciudad', 'nombre', 'telefono', 'material', 'cantidad', 'consent'], []],
    $formList === 'toggle' => [['listToggle', 'material', 'cantidad', 'ciudad', 'nombre', 'telefono', 'mensaje', 'consent'], []],
    default              => [['material', 'cantidad', 'ciudad', 'nombre', 'telefono', 'mensaje', 'consent'], []],
};
echo implode("\n\n", array_map(static fn (string $k): string => $formFields[$k], $formOrder[0])), "\n";
if ($formOrder[1] !== []) {
    echo '<div class="lead-form__more" data-lead-more>', "\n";
    echo implode("\n\n", array_map(static fn (string $k): string => $formFields[$k], $formOrder[1])), "\n";
    echo "</div>\n";
}
?>

    <?php /* Honeypot: los bots lo completan, las personas no lo ven nunca. */ ?>
    <input class="lead-form__honeypot" name="hp_extra" tabindex="-1" autocomplete="off" aria-hidden="true" value="">

    <?php /* Trampa de tiempo: sello firmado del momento del render (partials/lead.php). */ ?>
    <input type="hidden" name="ts" value="<?= e($stamp['ts']) ?>">
    <input type="hidden" name="tsg" value="<?= e($stamp['sig']) ?>">
    <input type="hidden" name="origen" value="<?= e($formOrigen) ?>">

    <button class="lead-form__submit btn btn--primary" type="submit" data-ev="form_submit_attempt" data-ev-loc="<?= $isHero ? 'hero-' : '' ?><?= e($formSlug !== '' ? $formSlug : 'cotizar') ?><?= $formList === 'open' ? '-lista' : '' ?>"><?= $isHero ? 'Pedir precio' : 'Pedir cotización' ?></button>
    <p class="lead-form__note">Sin costo. No publicamos tu teléfono en ningún lado.</p>
  </form>
</section>
