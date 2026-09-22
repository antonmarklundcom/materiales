#!/usr/bin/env php
<?php
/**
 * tools/replay-leads.php — CLI que reintenta los leads que el CRM rechazó (plan §1.26).
 *
 * Lee storage/leads.log línea por línea (JSONL): cada línea con outcome === 'fallo_crm' es
 * un lead que llegó al sitio pero no al CRM. Este script reenvía el mismo payload con
 * lead_send() y anota en storage/replayed.log qué líneas ya se reintentaron, idempotente por
 * el idempotency_key del payload: una línea cuyo CRM ya devolvió 200/201 (acá o en un replay
 * anterior) nunca se reenvía dos veces.
 *
 * outcome === 'solo_log' es un lead que llegó mientras el CRM todavía no estaba configurado
 * (plan §4.5): también se reintenta, pero sólo si no es demasiado viejo — el día que se
 * configure el CRM, este script no debe volcarle de una todo el historial de meses a los
 * proveedores. Tope por defecto: 72 horas, ajustable con --max-age-hours=N (ver $maxAgeHours).
 *
 * Tope de reintentos (R1): un lead que el CRM rechaza con un 4xx permanente (400, 401, 403,
 * 404, 409, 422…; no 408/425/429, que son transitorios) no se vuelve a mandar nunca: repetir
 * el mismo payload no lo va a arreglar. Cualquier otro fallo se reintenta hasta
 * --max-attempts=N veces (24 por defecto = un día de cron horario) y después se abandona.
 * Los abandonados se listan en cada corrida para cargarlos a mano.
 *
 * Alerta (R1): si la corrida termina con exit ≠ 0 (y no es --dry-run), avisa por los mismos
 * canales que lead_notify() (email / Telegram de config/vendercrm.php), como mucho una vez
 * cada 24 h por el mismo código de salida (storage/replay-alert.json), para que un cron
 * horario no inunde el chat. --no-alert la apaga.
 *
 * Lee storage/leads.log y el último storage/leads-AAAA-MM.log rotado (tools/maintenance.php
 * rota el log cada mes): un fallo del 31 no se pierde porque el 1.º se rotó el archivo.
 *
 * Uso: php tools/replay-leads.php [--dry-run] [--max-age-hours=N] [--max-attempts=N] [--no-alert]
 * Exit codes: 0 = todo enviado u OK sin pendientes; 1 = quedó al menos un fallo tras el
 * intento o se abandonó un lead en esta corrida; 2 = error de entorno (leads.log ilegible,
 * CRM no configurado).
 *
 * Cron sugerido (hPanel), cada hora — ver DEPLOY.md:
 *   0 * * * * /usr/bin/php /home/USUARIO/public_html/tools/replay-leads.php >> /home/USUARIO/logs/replay-leads.log 2>&1
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('replay-leads.php es sólo CLI (plan §1.26): no hay endpoint web.');
}

require __DIR__ . '/../partials/init.php';
require __DIR__ . '/../partials/lead.php';

$dryRun  = in_array('--dry-run', $argv, true);
$noAlert = in_array('--no-alert', $argv, true);

// Tope por defecto para reintentar un lead 'solo_log' (plan §4.5): pasado este tiempo desde
// que se registró, ya no se reenvía automáticamente — un pedido de hace semanas o meses no es
// algo que un proveedor deba recibir de sorpresa el día que se activa el CRM. 'fallo_crm' no
// tiene tope: son fallos técnicos recientes de un CRM que ya estaba configurado, no backlog.
$maxAgeHours = 72;

// --log= y --replayed= son sólo para el fixture de CI (tools/smoke.php): en producción
// siempre se usan los dos archivos reales de STORAGE_DIR. Nunca se exponen por web (el
// guard de PHP_SAPI de arriba ya lo impide).
$logFiles     = [];
$replayedFile = STORAGE_DIR . '/replayed.log';
$maxAttempts  = 24;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--log=')) {
        $logFiles[] = substr($arg, 6);
    } elseif (str_starts_with($arg, '--max-attempts=')) {
        $maxAttempts = max(1, (int) substr($arg, 15));
    } elseif (str_starts_with($arg, '--replayed=')) {
        $replayedFile = substr($arg, 11);
    } elseif (str_starts_with($arg, '--max-age-hours=')) {
        $maxAgeHours = max(0, (int) substr($arg, 16));
    }
}
$maxAgeSeconds = $maxAgeHours * 3600;
$now           = time();

if ($logFiles === []) {
    $rotated = glob(STORAGE_DIR . '/leads-[0-9][0-9][0-9][0-9]-[0-9][0-9]*.log') ?: [];
    sort($rotated);
    if ($rotated !== []) {
        $logFiles[] = end($rotated);
    }
    $logFiles[] = STORAGE_DIR . '/leads.log';
}
$logFiles = array_values(array_filter($logFiles, 'is_file'));

/**
 * Alerta de corrida fallida, deduplicada: como mucho una por código de salida cada 24 h. El
 * estado vive junto a replayed.log (en CI, en el dir temporal del fixture).
 */
function replay_alert(int $code, string $summary, string $stateDir, bool $enabled): void
{
    if (!$enabled) {
        return;
    }
    $stateFile = $stateDir . '/replay-alert.json';
    $state = is_file($stateFile) ? json_decode((string) @file_get_contents($stateFile), true) : null;
    if (is_array($state) && (int) ($state['code'] ?? -1) === $code && time() - (int) ($state['ts'] ?? 0) < 86400) {
        return;
    }
    $sent = lead_alert(
        '[materiales.com.py] replay-leads terminó con error (exit ' . $code . ')',
        $summary . "\n\nRevisá storage/leads.log y storage/replayed.log (ver DEPLOY.md → Replay de leads)."
    );
    if ($sent) {
        @file_put_contents($stateFile, json_encode(['code' => $code, 'ts' => time()]) . "\n", LOCK_EX);
    }
}

/** 4xx que no se arregla reintentando el mismo payload. 408/425/429 son transitorios. */
function replay_is_permanent(int $status): bool
{
    return $status >= 400 && $status < 500 && !in_array($status, [408, 425, 429], true);
}

if ($logFiles === []) {
    fwrite(STDERR, "No existe storage/leads.log: nada que reintentar.\n");
    exit(0);
}

if (!$dryRun && !lead_crm_configured()) {
    $msg = "config/vendercrm.php no existe o está incompleto: no se puede reintentar sin CRM configurado.";
    fwrite(STDERR, $msg . "\n");
    replay_alert(2, $msg . ' Mientras tanto los leads quedan como solo_log en storage/leads.log.', dirname($replayedFile), !$noAlert);
    exit(2);
}

/**
 * Historial de replayed.log por clave: éxitos (nunca se reenvían), intentos fallidos y
 * rechazos permanentes (nunca se reintentan).
 *
 * @return array{sent: array<string,bool>, attempts: array<string,int>, permanent: array<string,int>}
 */
function replay_history(string $replayedFile): array
{
    $history = ['sent' => [], 'attempts' => [], 'permanent' => []];
    $handle = is_file($replayedFile) ? @fopen($replayedFile, 'r') : false;
    if ($handle === false) {
        return $history;
    }
    while (($line = fgets($handle)) !== false) {
        $record = json_decode(trim($line), true);
        $key = is_array($record) ? ($record['idempotency_key'] ?? null) : null;
        if (!is_string($key)) {
            continue;
        }
        if (($record['ok'] ?? false) === true) {
            $history['sent'][$key] = true;
            continue;
        }
        $history['attempts'][$key] = ($history['attempts'][$key] ?? 0) + 1;
        if (replay_is_permanent((int) ($record['status'] ?? 0))) {
            $history['permanent'][$key] = (int) $record['status'];
        }
    }
    fclose($handle);
    return $history;
}

function replay_append(string $replayedFile, array $record): void
{
    $line = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($line === false) {
        return;
    }
    @file_put_contents($replayedFile, $line . "\n", FILE_APPEND | LOCK_EX);
}

/** Todas las líneas de los logs a leer, en orden. Una línea ilegible vuelve como null. */
function replay_lines(array $logFiles): Generator
{
    foreach ($logFiles as $file) {
        $handle = @fopen($file, 'r');
        if ($handle === false) {
            throw new RuntimeException("No se pudo abrir {$file}.");
        }
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if ($line !== '') {
                $record = json_decode($line, true);
                yield is_array($record) ? $record : null;
            }
        }
        fclose($handle);
    }
}

$history     = replay_history($replayedFile);
$alreadySent = $history['sent'];

$pending    = 0;
$sentNow    = 0;
$stillBad   = 0;
$unparsable = 0;
$tooOld     = 0;
$gaveUpNow  = 0;
$abandoned  = [];

try {
    // Reconciliar todo el log antes de reenviar: un éxito puede seguir a un fallo anterior.
    foreach (replay_lines($logFiles) as $record) {
        if (is_array($record) && ($record['outcome'] ?? '') === 'enviado') {
            $key = (string) ($record['payload']['idempotency_key'] ?? '');
            if ($key !== '') {
                $alreadySent[$key] = true;
            }
        }
    }

    foreach (replay_lines($logFiles) as $record) {
        if ($record === null) {
            // Línea corrupta (escritura a medias, disco lleno a mitad de un append): se cuenta
            // en vez de desaparecer en silencio — este log es el único respaldo de un lead.
            $unparsable++;
            continue;
        }
        $outcome = $record['outcome'] ?? '';
        if (!in_array($outcome, ['fallo_crm', 'solo_log'], true)) {
            continue;
        }

        if ($outcome === 'solo_log' && $maxAgeSeconds > 0) {
            $ts = strtotime((string) ($record['ts'] ?? ''));
            if ($ts !== false && ($now - $ts) > $maxAgeSeconds) {
                $tooOld++;
                continue;
            }
        }

        $payload = is_array($record['payload'] ?? null) ? $record['payload'] : [];
        $idempotencyKey = (string) ($payload['idempotency_key'] ?? '');
        if ($idempotencyKey === '' || isset($alreadySent[$idempotencyKey])) {
            continue;
        }

        if (isset($history['permanent'][$idempotencyKey])) {
            $abandoned[$idempotencyKey] = 'HTTP ' . $history['permanent'][$idempotencyKey] . ' permanente';
            continue;
        }
        if (($history['attempts'][$idempotencyKey] ?? 0) >= $maxAttempts) {
            $abandoned[$idempotencyKey] = ($history['attempts'][$idempotencyKey]) . ' intentos';
            continue;
        }

        $pending++;
        // Una misma clave puede aparecer dos veces en el log (doble envío): se trata una vez.
        $alreadySent[$idempotencyKey] = true;

        if ($dryRun) {
            fwrite(STDOUT, "[dry-run] reenviaría idempotency_key={$idempotencyKey}\n");
            continue;
        }

        $result = lead_send($payload, lead_config());
        $ok     = lead_send_ok($result);

        replay_append($replayedFile, [
            'ts'              => gmdate('c'),
            'idempotency_key' => $idempotencyKey,
            'ok'              => $ok,
            'status'          => $result['status'],
            'error'           => $result['error'],
        ]);

        if ($ok) {
            $sentNow++;
            fwrite(STDOUT, "OK  idempotency_key={$idempotencyKey} status={$result['status']}\n");
            continue;
        }
        $stillBad++;
        $attempts = ($history['attempts'][$idempotencyKey] ?? 0) + 1;
        if (replay_is_permanent((int) $result['status']) || $attempts >= $maxAttempts) {
            $gaveUpNow++;
            $abandoned[$idempotencyKey] = replay_is_permanent((int) $result['status'])
                ? 'HTTP ' . $result['status'] . ' permanente'
                : $attempts . ' intentos';
        }
        fwrite(STDOUT, "FAIL idempotency_key={$idempotencyKey} status={$result['status']} error={$result['error']}\n");
    }
} catch (RuntimeException $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    if (!$dryRun) {
        replay_alert(2, $e->getMessage(), dirname($replayedFile), !$noAlert);
    }
    exit(2);
}

foreach ($abandoned as $key => $why) {
    fwrite(STDOUT, "ABANDONADO idempotency_key={$key} ({$why}) — cargarlo a mano en VenderCRM\n");
}

$summary = sprintf(
    "replay-leads: %d pendientes, %d reenviados OK, %d siguen fallando, %d abandonados (tope %d" .
    " intentos o 4xx permanente), %d descartados por antiguos (solo_log > %dh), %d líneas ilegibles%s.",
    $pending,
    $sentNow,
    $stillBad,
    count($abandoned),
    $maxAttempts,
    $tooOld,
    $maxAgeHours,
    $unparsable,
    $dryRun ? ' (dry-run, no se envió nada)' : ''
);
fwrite(STDOUT, $summary . "\n");

$exit = $stillBad > 0 || $gaveUpNow > 0 ? 1 : 0;
if ($exit !== 0 && !$dryRun) {
    replay_alert($exit, $summary, dirname($replayedFile), !$noAlert);
}
exit($exit);
