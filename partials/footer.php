<?php
/**
 * partials/footer.php — pie con trust stack + banner de cookies.
 *
 * TRUST STACK: los datos reales (razón social, RUC, IVA, dirección, teléfonos, horarios,
 * mapa) son un input humano de la fase 3 (plan §7). Cada bloque se renderiza SÓLO si el dato
 * existe en data/site.php: nunca se muestra un placeholder visible ni un dato inventado.
 * El embed de Google Maps se carga on-interaction (fase 4), no en el load de la página.
 */

declare(strict_types=1);

$site    = site();
$address = $site['address'] ?? [];
$whatsapp = preg_replace('/\D+/', '', (string) ($site['whatsapp'] ?? ''));
?>
</main>
<footer class="site-footer">
  <div class="site-footer__ribbon band--dark grain">
    <div class="wrap site-footer__trust">
      <p class="site-footer__name"><?= e($site['legal_name'] !== '' ? $site['legal_name'] : $site['brand']) ?></p>
      <?php if (($site['ruc'] ?? '') !== ''): ?><p>RUC: <?= e($site['ruc']) ?><?= ($site['iva_status'] ?? '') !== '' ? ' · ' . e($site['iva_status']) : '' ?></p><?php endif; ?>
      <?php if (($address['street'] ?? '') !== ''): ?><p><?= e($address['street']) ?>, <?= e($address['locality']) ?>, Paraguay</p><?php endif; ?>
      <?php if (($site['horarios'] ?? '') !== ''): ?><p><?= e($site['horarios']) ?></p><?php endif; ?>
      <ul class="site-footer__contact">
        <?php if ($whatsapp !== ''): ?><li><a href="https://wa.me/<?= e($whatsapp) ?>" data-ev="whatsapp_click" data-ev-loc="footer">WhatsApp</a></li><?php endif; ?>
        <?php if (($site['phone'] ?? '') !== ''): ?><li><a href="tel:<?= e($site['phone']) ?>" data-ev="call_click" data-ev-loc="footer"><?= e($site['phone']) ?></a></li><?php endif; ?>
        <?php if (($site['email'] ?? '') !== ''): ?><li><a href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a></li><?php endif; ?>
      </ul>
    </div>
  </div>
  <div class="site-footer__base band--dark">
    <div class="wrap">
      <nav class="site-footer__nav" aria-label="Pie">
        <a href="/materiales/">Materiales</a>
        <a href="/guias/">Guías</a>
        <a href="/cotizar/">Pedir cotización</a>
        <a href="/contacto/">Contacto</a>
        <a href="/politica-de-privacidad/">Política de privacidad</a>
      </nav>
      <p class="site-footer__area">Cotizaciones en <?= e(implode(', ', array_slice($site['area_served'] ?? [], 0, 6))) ?> y todo Gran Asunción.</p>
      <p class="site-footer__legal">&copy; <?= date('Y') ?> <?= e($site['brand']) ?></p>
    </div>
  </div>
</footer>
<?php
// Barra pegajosa de CTA en mobile (fase 9). En /cotizar/ y /gracias/ no va: en la primera
// el formulario ES la página, y en la segunda el pedido ya está hecho.
if (!in_array(page()['canonical'] ?? '', ['/cotizar/', '/gracias/'], true)) {
    require PUBLIC_ROOT . '/partials/cta.php';
}
?>
<?php require PUBLIC_ROOT . '/partials/cookie-banner.php'; ?>
</body>
</html>
