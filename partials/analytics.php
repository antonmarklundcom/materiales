<?php
/**
 * partials/analytics.php — carga de analítica, SIEMPRE detrás del consentimiento (plan §6).
 * Lo incluye header.php, así que corre en todas las páginas.
 *
 * Nada de tracking se descarga en el load: este partial sólo publica la configuración y
 * assets/js/analytics.js inyecta GA4 (consentimiento de ESTADÍSTICAS) y Meta Pixel
 * (consentimiento de MARKETING) recién cuando el visitante los acepta. Sin IDs en
 * data/site.php no se emite absolutamente nada — el sitio funciona igual (plan §4.5).
 *
 * vc-attribution.js es la excepción y va sitewide por decisión del plan §3: es una cookie
 * propia de primer toque que sirve para atribuir el lead que el propio visitante pide. Se
 * declara por nombre en /politica-de-privacidad/ (ver KNOWN-ISSUES #10).
 */

declare(strict_types=1);

$analyticsSite   = site();
$ga4Id           = trim((string) ($analyticsSite['ga4_id'] ?? ''));
$pixelId         = trim((string) ($analyticsSite['meta_pixel_id'] ?? ''));
$attributionSrc  = trim((string) ($analyticsSite['vc_attribution'] ?? ''));

// $leadEvent lo define /gracias/: ['token' => ..., 'material' => ..., 'categoria' => ...].
$leadEvent = isset($leadEvent) && is_array($leadEvent) ? $leadEvent : null;

$analyticsConfig = array_filter([
    'ga4_id'   => $ga4Id,
    'pixel_id' => $pixelId,
]);
?>
<?php if ($ga4Id !== ''): ?>
<?php /* C1 — Consent Mode v2, modo básico: el stub de gtag nace con TODO en "denied" antes de
       cualquier otro script, y analytics.js lo pasa a "granted" con gtag('consent','update')
       cuando el visitante acepta. gtag.js recién se descarga después de aceptar Estadísticas
       (lo que promete la política de privacidad: "hasta que las aceptes no descargamos ni
       ejecutamos ninguno de esos scripts"). El modo avanzado (gtag.js siempre, pings sin
       cookies para modelar conversiones) exige cambiar ese texto primero: decisión de Anton. */ ?>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', {ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied', analytics_storage: 'denied', wait_for_update: 500});
gtag('set', 'ads_data_redaction', true);
</script>
<?php endif; ?>
<?php if ($analyticsConfig !== [] || $leadEvent !== null): ?>
<script>
<?php if ($analyticsConfig !== []): ?>
window.matAnalytics = <?= json_encode($analyticsConfig, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;
<?php endif; ?>
<?php if ($leadEvent !== null): ?>
<?php /* La conversión se declara SIEMPRE que el handler haya emitido un token, haya o no
       proveedores de analítica configurados: es un hecho del sitio, no de GA4. Sin IDs
       nadie la consume, pero queda verificable en CI y lista para el día que se configuren. */ ?>
window.matLead = <?= json_encode($leadEvent, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;
<?php endif; ?>
</script>
<?php endif; ?>
<?php if ($analyticsConfig !== []): ?>
<script src="<?= e(asset('/assets/js/analytics.js')) ?>" defer></script>
<?php endif; ?>
<?php if ($attributionSrc !== ''): ?>
<script src="<?= e($attributionSrc) ?>" defer></script>
<?php endif; ?>
