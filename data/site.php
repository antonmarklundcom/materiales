<?php
/**
 * data/site.php — sitewide constants: NAP, contact channels, analytics IDs, service area.
 *
 * PLACEHOLDERS: every value below marked "PENDIENTE" is empty on purpose. Real NAP data is a
 * phase-3 human input (plan §7). Empty values are NEVER rendered on-page and NEVER emitted in
 * JSON-LD — the header/footer/schema partials skip empty fields. Do not invent a business
 * name, RUC, address, phone or email here: fabricated NAP is an anti-fabrication violation
 * (seo-web-builds §0.1) and poisons NAP consistency with the future Google Business Profile.
 */

declare(strict_types=1);

return [
    // --- identity -----------------------------------------------------------------
    'brand'       => 'Materiales.com.py',
    'legal_name'  => '',            // PENDIENTE (fase 3): razón social exacta
    'ruc'         => '',            // PENDIENTE (fase 3)
    'iva_status'  => '',            // PENDIENTE (fase 3): p.ej. "IVA incluido" / "Contribuyente"
    'base_url'    => 'https://materiales.com.py',
    'locale'      => 'es-PY',
    'tagline'     => 'Cotizá materiales de construcción con proveedores verificados de Paraguay.',

    // --- contacto -----------------------------------------------------------------
    // WhatsApp/tel en E.164 sin espacios (+595...). Vacío = el canal no se renderiza.
    'whatsapp'    => '',            // PENDIENTE (fase 3) p.ej. +595981000000
    'phone'       => '',            // PENDIENTE (fase 3)
    'email'       => '',            // PENDIENTE (fase 3)
    'horarios'    => '',            // PENDIENTE (fase 3) p.ej. "Lunes a viernes 07:00–17:00"

    // --- dirección (patrón service-area: sólo si existe dirección real) -----------
    'address' => [
        'street'   => '',           // PENDIENTE (fase 3) — omitir en schema si vacío
        'locality' => 'Asunción',
        'region'   => 'Asunción',
        'country'  => 'PY',
    ],
    'maps_embed_url' => '',         // PENDIENTE (fase 3) — se carga on-interaction, no on-load

    // --- área de servicio (plan §6) ------------------------------------------------
    'area_served' => [
        'Asunción', 'Lambaré', 'Fernando de la Mora', 'San Lorenzo', 'Luque', 'Capiatá',
        'Mariano Roque Alonso', 'Villa Elisa', 'Ñemby', 'Limpio', 'Itauguá',
    ],

    // --- analítica (fase 2; vacío = el script no se carga) -------------------------
    'ga4_id'          => '',        // PENDIENTE (fase 2)
    'meta_pixel_id'   => '',        // PENDIENTE (fase 2)
    'vc_attribution'  => '',        // PENDIENTE (fase 2): {CRM_URL}/vc-attribution.js

    // --- flags ---------------------------------------------------------------------
    // staging_noindex: true hasta el go-live (fase 6, plan §7). Emite noindex sitewide.
    'staging_noindex' => true,
    // has_search: el sitio no tiene búsqueda interna todavía; SearchAction sólo se emite
    // cuando esto sea true (ver nota de desviación en el build log, plan §9).
    'has_search'      => false,
    // Versión del texto de consentimiento (plan §8.6). No cambiar sin actualizar la política.
    'consent_version' => 'proveedores-v1',
    'max_proveedores' => 3,
];
