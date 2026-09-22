<?php
/**
 * cotizar/enviar.php — shell HTTP del pipeline de leads (plan §3, paso a paso).
 *
 * Este archivo NO tiene lógica de negocio: valida, decide un redirect y delega todo lo demás
 * en partials/lead.php (que CI ejercita como unidades, sin servidor).
 *
 * Invariante que manda sobre todo lo demás: el visitante SIEMPRE termina en una página útil.
 * Ningún fallo del CRM, del log ni del disco produce una pantalla de error — un visitante que
 * llenó el formulario y vio un error es un lead perdido (skill vendercrm-lead-capture, regla 5).
 *
 * Patrón PRG: siempre se responde con 303, nunca con HTML. Refrescar /gracias/ no puede
 * reenviar nada, y lo que el PRG no atrapa lo atrapa la clave de idempotencia.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/partials/init.php';
require PUBLIC_ROOT . '/partials/lead.php';

/** Redirect 303 + fin. Único punto de salida del handler. */
function enviar_redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    header('Cache-Control: no-store');
    exit;
}

/**
 * Registra un lead en leads.log y avisa (lead_notify). Un solo lugar para las dos cosas: un
 * pedido que se registra sin avisar es justamente el que nadie contesta.
 */
function enviar_log_and_notify(array $record): void
{
    $logged = lead_log($record);
    if (!$logged) {
        $record['log_error'] = 'no se pudo escribir storage/leads.log';
    }
    lead_notify($record);
}

/**
 * Pedido con teléfono y consentimiento válidos que el filtro anti-bot o el límite por IP
 * frenaron. Antes se descartaba SIN el payload: una persona real (IP compartida, pestaña
 * abierta de ayer, autocompletado del navegador en el campo trampa) veía /gracias/ y
 * esperaba una respuesta que nunca iba a llegar. Ahora se guarda completo como `retenido` y
 * se avisa: nada se manda al CRM automáticamente, lo revisa una persona.
 */
function enviar_retain(string $reason, int $now, string $phone, array $payload, string $redirect): never
{
    enviar_log_and_notify([
        'ts'         => gmdate('c', $now),
        'outcome'    => 'retenido',
        'reason'     => $reason,
        'phone_e164' => $phone,
        'payload'    => $payload,
        'ip'         => lead_ip_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? '')),
        'ua'         => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
    ]);
    enviar_redirect($redirect);
}

/** Query string con sólo los campos NO personales, para repoblar el formulario tras un error. */
function enviar_error_path(string $origen, string $error, array $post): never
{
    $query = ['error' => $error];
    foreach (['material' => 'm', 'cantidad' => 'cantidad', 'ciudad' => 'ciudad'] as $field => $key) {
        $value = trim((string) ($post[$field] ?? ''));
        if ($value !== '') {
            $query[$key] = mb_substr($value, 0, 200);
        }
    }
    enviar_redirect(lead_safe_path($origen) . '?' . http_build_query($query) . '#cotizar');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    // Alguien llegó por GET (un enlace, un crawler): no es un envío, es la página.
    enviar_redirect('/cotizar/');
}

$origen = lead_safe_path((string) ($_POST['origen'] ?? '/cotizar/'));
$now    = time();
$isSupplier = (string) ($_POST['tipo'] ?? '') === 'proveedor';

/** Payload del comprador (plan §3.3 y §3.4). */
$buildBuyerPayload = static function (string $phoneRaw, string $idempotencyKey, array $resolved) use ($origen, $now): array {
    return lead_build_payload($resolved + [
        'phone_raw'       => $phoneRaw,
        'idempotency_key' => $idempotencyKey,
        'nombre'          => (string) ($_POST['nombre'] ?? ''),
        'mensaje'         => (string) ($_POST['mensaje'] ?? ''),
        'cantidad'        => (string) ($_POST['cantidad'] ?? ''),
        'ciudad'          => (string) ($_POST['ciudad'] ?? ''),
        'page_url'        => url($origen),
        'referrer'        => (string) ($_SERVER['HTTP_REFERER'] ?? ''),
        'attribution'     => lead_attribution($_POST, $_COOKIE),
        'consent_at'      => gmdate('c', $now),
    ]);
};

/** Payload del alta de proveedor (fase 10, decisión §1.17). */
$buildSupplierPayload = static function (string $phoneRaw, string $idempotencyKey) use ($now): array {
    return lead_build_supplier_payload([
        'phone_raw'       => $phoneRaw,
        'idempotency_key' => $idempotencyKey,
        'nombre'          => (string) ($_POST['nombre'] ?? ''),
        'empresa'         => trim((string) ($_POST['empresa'] ?? '')),
        'rubros'          => lead_supplier_rubros($_POST['rubros'] ?? []),
        'ciudad'          => (string) ($_POST['ciudad'] ?? ''),
        'mensaje'         => (string) ($_POST['mensaje'] ?? ''),
        'page_url'        => url('/proveedores/'),
        'referrer'        => (string) ($_SERVER['HTTP_REFERER'] ?? ''),
        'attribution'     => lead_attribution($_POST, $_COOKIE),
        'consent_at'      => gmdate('c', $now),
    ]);
};

$emptyResolved = [
    'material' => '', 'material_name' => '', 'categoria' => '', 'categoria_name' => '',
    'presupuesto_band' => '',
];

// ---- 1. Bots: honeypot y trampa de tiempo (plan §3.1) --------------------------------
// Se acepta en silencio: el bot ve un 303 a /gracias/ y se va. No se postea nada al CRM y
// no se le dice nunca qué lo delató. `website` es el nombre viejo del campo trampa (lo
// autocompletaban algunos navegadores); se sigue leyendo por las páginas ya renderizadas.
$stampReason = lead_form_stamp_reason(
    (string) ($_POST['ts'] ?? ''),
    (string) ($_POST['tsg'] ?? ''),
    $now
);
$honeypot  = !empty($_POST['hp_extra']) || !empty($_POST['website']);
$botReason = $honeypot ? 'honeypot' : $stampReason;

if ($botReason !== '') {
    // Con teléfono y consentimiento válidos puede ser una persona: se retiene, no se tira.
    $botPhoneRaw = trim((string) ($_POST['telefono'] ?? ''));
    $botPhone    = lead_normalize_phone($botPhoneRaw);
    if ($botPhone !== null && ($_POST['consentimiento'] ?? '') === '1') {
        if ($isSupplier) {
            enviar_retain($botReason, $now, $botPhone,
                $buildSupplierPayload($botPhoneRaw, lead_idempotency_key($botPhone, $now, 'proveedor')),
                '/proveedores/?ok=1#gracias');
        }
        $botResolved = lead_resolve_slug((string) ($_POST['material'] ?? '')) ?? $emptyResolved;
        enviar_retain($botReason, $now, $botPhone,
            $buildBuyerPayload($botPhoneRaw, lead_idempotency_key($botPhone, $now, $botResolved['material']), $botResolved),
            '/gracias/');
    }
    lead_log([
        'ts'      => gmdate('c', $now),
        'outcome' => 'descartado',
        'reason'  => $botReason,
        'ip'      => lead_ip_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? '')),
        'ua'      => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
    ]);
    enviar_redirect('/gracias/');
}

// ---- 1bis. Alta de proveedor (fase 10, decisión §1.17) --------------------------------
// Mismo handler, misma trampa de bots, misma idempotencia; otro formulario, otro payload y
// otra versión de consentimiento. El camino del COMPRADOR sigue abajo sin un byte de cambio:
// un POST de comprador no trae `tipo` y acá nunca se le pone uno por defecto.
if ($isSupplier) {
    /** Vuelve a /proveedores/ conservando sólo los campos no personales ya tipeados. */
    $provError = static function (string $error): never {
        $query = ['error' => $error];
        foreach (['empresa' => 'empresa', 'ciudad' => 'ciudad'] as $field => $key) {
            $value = trim((string) ($_POST[$field] ?? ''));
            if ($value !== '') {
                $query[$key] = mb_substr($value, 0, 200);
            }
        }
        $rubros = lead_supplier_rubros($_POST['rubros'] ?? []);
        if ($rubros !== []) {
            $query['rubros'] = implode(',', $rubros);
        }
        enviar_redirect('/proveedores/?' . http_build_query($query) . '#sumate');
    };

    $provPhoneRaw = trim((string) ($_POST['telefono'] ?? ''));
    $provPhone    = lead_normalize_phone($provPhoneRaw);
    $provEmpresa  = trim((string) ($_POST['empresa'] ?? ''));
    $provRubros   = lead_supplier_rubros($_POST['rubros'] ?? []);

    if ($provEmpresa === '') {
        $provError('empresa');
    }
    if ($provRubros === []) {
        $provError('rubros');
    }
    if ($provPhone === null) {
        $provError('telefono');
    }
    if (($_POST['consentimiento'] ?? '') !== '1') {
        $provError('consentimiento');
    }

    $provIdemKey = lead_idempotency_key($provPhone, $now, 'proveedor');
    $provPayload = $buildSupplierPayload($provPhoneRaw, $provIdemKey);
    if (lead_ip_throttled((string) ($_SERVER['REMOTE_ADDR'] ?? ''), $provIdemKey, $now)) {
        enviar_retain('limite_ip', $now, $provPhone, $provPayload, '/proveedores/?ok=1#gracias');
    }

    $provCrm = ['status' => 0, 'body' => '', 'error' => 'sin configuración de CRM', 'ms' => 0];
    if (lead_crm_configured()) {
        $provCrm = lead_send($provPayload, lead_config());
    }

    enviar_log_and_notify([
        'ts'         => gmdate('c', $now),
        'outcome'    => lead_send_ok($provCrm) ? 'enviado' : (lead_crm_configured() ? 'fallo_crm' : 'solo_log'),
        'phone_e164' => $provPhone,
        'crm'        => ['status' => $provCrm['status'], 'ms' => $provCrm['ms'], 'error' => $provCrm['error'], 'body' => $provCrm['body']],
        'payload'    => $provPayload,
        'ip'         => lead_ip_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? '')),
    ]);

    // PRG igual que el comprador; el acuse se muestra en la misma página, no en /gracias/
    // (esa página habla de cotizaciones que van a llegar, que no es lo que pasó acá).
    enviar_redirect('/proveedores/?ok=1#gracias');
}

// ---- 2. Validación (plan §3.2): teléfono plausible y consentimiento marcado -----------
// Son las DOS únicas causas por las que se le devuelve el formulario al visitante. Todo lo
// demás (nombre vacío, material sin elegir) entra igual: el filtro de calidad es el repaso
// manual en el CRM, no este archivo (plan §8.1).
$phoneRaw = trim((string) ($_POST['telefono'] ?? ''));
$phone    = lead_normalize_phone($phoneRaw);
if ($phone === null) {
    enviar_error_path($origen, 'telefono', $_POST);
}

if (($_POST['consentimiento'] ?? '') !== '1') {
    enviar_error_path($origen, 'consentimiento', $_POST);
}

// ---- 3–4. Payload (plan §3.3 y §3.4) --------------------------------------------------
$slug     = (string) ($_POST['material'] ?? '');
$resolved = lead_resolve_slug($slug) ?? $emptyResolved;

// La clave lleva el material pedido: dos pedidos distintos de la misma persona en la misma
// hora son dos leads, no un duplicado (ver lead_idempotency_key()).
$idempotencyKey = lead_idempotency_key($phone, $now, $resolved['material']);
$payload        = $buildBuyerPayload($phoneRaw, $idempotencyKey, $resolved);

if (lead_ip_throttled((string) ($_SERVER['REMOTE_ADDR'] ?? ''), $idempotencyKey, $now)) {
    enviar_retain('limite_ip', $now, $phone, $payload, '/gracias/');
}

$crm = ['status' => 0, 'body' => '', 'error' => 'sin configuración de CRM', 'ms' => 0];
if (lead_crm_configured()) {
    $crm = lead_send($payload, lead_config());
}

// ---- 5. leads.log SIEMPRE, haya CRM o no (plan §3.5) ----------------------------------
// Es el respaldo ante caída del CRM (el replay manual del que habla el plan) y la pista de
// auditoría del consentimiento. Se guarda el teléfono NORMALIZADO además del tipeado para
// poder reconstruir la clave de idempotencia en un replay.
// El token de /gracias/ se genera antes de registrar: su primera mitad es la REFERENCIA que
// /gracias/ muestra y pone en el mensaje de WhatsApp (C8), y queda en la línea del log para
// encontrar el pedido cuando el cliente escribe "ref. AB12CD34". No viaja al CRM: el
// contrato del payload no cambia.
$conversionToken = lead_conversion_token();
enviar_log_and_notify([
    'ts'          => gmdate('c', $now),
    'outcome'     => lead_send_ok($crm) ? 'enviado' : (lead_crm_configured() ? 'fallo_crm' : 'solo_log'),
    'ref'         => lead_reference($conversionToken),
    'phone_e164'  => $phone,
    'crm'         => ['status' => $crm['status'], 'ms' => $crm['ms'], 'error' => $crm['error'], 'body' => $crm['body']],
    'payload'     => $payload,
    'ip'          => lead_ip_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? '')),
]);

// ---- 6. PRG a /gracias/ (plan §3.6) ---------------------------------------------------
// `k` es un token de un solo uso: /gracias/ dispara los eventos de analítica una vez por
// token y los recuerda en sessionStorage, así refrescar la página no infla las conversiones.
// Va firmado (lead_conversion_token): un /gracias/?k= tipeado a mano no cuenta.
$query = ['k' => $conversionToken];
if ($resolved['material'] !== '') {
    $query = ['m' => $resolved['material']] + $query;
}
enviar_redirect('/gracias/?' . http_build_query($query));
