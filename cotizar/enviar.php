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

// ---- 1. Bots: honeypot y trampa de tiempo (plan §3.1) --------------------------------
// Se acepta en silencio: el bot ve un 303 a /gracias/ y se va. No se postea nada al CRM y
// no se le dice nunca qué lo delató.
$stampReason = lead_form_stamp_reason(
    (string) ($_POST['ts'] ?? ''),
    (string) ($_POST['tsg'] ?? ''),
    $now
);
$botReason = !empty($_POST['website']) ? 'honeypot' : $stampReason;

if ($botReason !== '') {
    lead_log([
        'ts'      => gmdate('c', $now),
        'outcome' => 'descartado',
        'reason'  => $botReason,
        'ip'      => lead_ip_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? '')),
        'ua'      => mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 300),
    ]);
    enviar_redirect('/gracias/');
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
$resolved = lead_resolve_slug($slug) ?? [
    'material' => '', 'material_name' => '', 'categoria' => '', 'categoria_name' => '',
    'presupuesto_band' => '',
];

$payload = lead_build_payload($resolved + [
    'phone_raw'       => $phoneRaw,
    'idempotency_key' => lead_idempotency_key($phone, $now),
    'nombre'          => (string) ($_POST['nombre'] ?? ''),
    'mensaje'         => (string) ($_POST['mensaje'] ?? ''),
    'cantidad'        => (string) ($_POST['cantidad'] ?? ''),
    'ciudad'          => (string) ($_POST['ciudad'] ?? ''),
    'page_url'        => url($origen),
    'referrer'        => (string) ($_SERVER['HTTP_REFERER'] ?? ''),
    'attribution'     => lead_attribution($_POST, $_COOKIE),
    'consent_at'      => gmdate('c', $now),
]);

$crm = ['status' => 0, 'body' => '', 'error' => 'sin configuración de CRM', 'ms' => 0];
if (lead_crm_configured()) {
    $crm = lead_send($payload, lead_config());
}

// ---- 5. leads.log SIEMPRE, haya CRM o no (plan §3.5) ----------------------------------
// Es el respaldo ante caída del CRM (el replay manual del que habla el plan) y la pista de
// auditoría del consentimiento. Se guarda el teléfono NORMALIZADO además del tipeado para
// poder reconstruir la clave de idempotencia en un replay.
lead_log([
    'ts'          => gmdate('c', $now),
    'outcome'     => lead_send_ok($crm) ? 'enviado' : (lead_crm_configured() ? 'fallo_crm' : 'solo_log'),
    'phone_e164'  => $phone,
    'crm'         => ['status' => $crm['status'], 'ms' => $crm['ms'], 'error' => $crm['error'], 'body' => $crm['body']],
    'payload'     => $payload,
    'ip'          => lead_ip_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? '')),
]);

// ---- 6. PRG a /gracias/ (plan §3.6) ---------------------------------------------------
// `k` es un token de un solo uso: /gracias/ dispara los eventos de analítica una vez por
// token y los recuerda en sessionStorage, así refrescar la página no infla las conversiones.
$query = ['k' => bin2hex(random_bytes(8))];
if ($resolved['material'] !== '') {
    $query = ['m' => $resolved['material']] + $query;
}
enviar_redirect('/gracias/?' . http_build_query($query));
