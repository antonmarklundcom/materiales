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
 * Uso: php tools/replay-leads.php [--dry-run] [--max-age-hours=N]
 * Exit codes: 0 = todo enviado u OK sin pendientes; 1 = quedó al menos un fallo tras el
 * intento; 2 = error de entorno (leads.log ilegible, CRM no configurado).
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

$dryRun = in_array('--dry-run', $argv, true);

// Tope por defecto para reintentar un lead 'solo_log' (plan §4.5): pasado este tiempo desde
// que se registró, ya no se reenvía automáticamente — un pedido de hace semanas o meses no es
// algo que un proveedor deba recibir de sorpresa el día que se activa el CRM. 'fallo_crm' no
// tiene tope: son fallos técnicos recientes de un CRM que ya estaba configurado, no backlog.
$maxAgeHours = 72;

// --log= y --replayed= son sólo para el fixture de CI (tools/smoke.php): en producción
// siempre se usan los dos archivos reales de STORAGE_DIR. Nunca se exponen por web (el
// guard de PHP_SAPI de arriba ya lo impide).
$logFile      = STORAGE_DIR . '/leads.log';
$replayedFile = STORAGE_DIR . '/replayed.log';
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--log=')) {
        $logFile = substr($arg, 6);
    } elseif (str_starts_with($arg, '--replayed=')) {
        $replayedFile = substr($arg, 11);
    } elseif (str_starts_with($arg, '--max-age-hours=')) {
        $maxAgeHours = max(0, (int) substr($arg, 16));
    }
}
$maxAgeSeconds = $maxAgeHours * 3600;
$now           = time();

if (!is_file($logFile)) {
    fwrite(STDERR, "No existe {$logFile}: nada que reintentar.\n");
    exit(0);
}

if (!$dryRun && !lead_crm_configured()) {
    fwrite(STDERR, "config/vendercrm.php no existe o está incompleto: no se puede reintentar sin CRM configurado.\n");
    exit(2);
}

/** Claves de idempotencia ya reintentadas CON ÉXITO (200/201) — nunca se reenvían de nuevo. */
function replay_already_sent(string $replayedFile): array
{
    $sent = [];
    if (!is_file($replayedFile)) {
        return $sent;
    }
    $handle = @fopen($replayedFile, 'r');
    if ($handle === false) {
        return $sent;
    }
    while (($line = fgets($handle)) !== false) {
        $record = json_decode(trim($line), true);
        if (is_array($record) && ($record['ok'] ?? false) === true && is_string($record['idempotency_key'] ?? null)) {
            $sent[$record['idempotency_key']] = true;
        }
    }
    fclose($handle);
    return $sent;
}

function replay_append(string $replayedFile, array $record): void
{
    $line = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($line === false) {
        return;
    }
    @file_put_contents($replayedFile, $line . "\n", FILE_APPEND | LOCK_EX);
}

$alreadySent = replay_already_sent($replayedFile);

$handle = @fopen($logFile, 'r');
if ($handle === false) {
    fwrite(STDERR, "No se pudo abrir {$logFile}.\n");
    exit(2);
}

// Reconciliar todo el log antes de reenviar: un éxito puede seguir a un fallo anterior.
while (($line = fgets($handle)) !== false) {
    $record = json_decode(trim($line), true);
    if (!is_array($record) || ($record['outcome'] ?? '') !== 'enviado') {
        continue;
    }
    $payload = is_array($record['payload'] ?? null) ? $record['payload'] : [];
    $idempotencyKey = (string) ($payload['idempotency_key'] ?? '');
    if ($idempotencyKey !== '') {
        $alreadySent[$idempotencyKey] = true;
    }
}
rewind($handle);

$pending    = 0;
$sentNow    = 0;
$stillBad   = 0;
$unparsable = 0;
$tooOld     = 0;

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }

    $record = json_decode($line, true);
    if (!is_array($record)) {
        // Línea corrupta (escritura a medias, disco lleno a mitad de un append): se cuenta en
        // vez de desaparecer en silencio — este log es el único respaldo de un lead.
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

    $pending++;

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
        $alreadySent[$idempotencyKey] = true;
        $sentNow++;
        fwrite(STDOUT, "OK  idempotency_key={$idempotencyKey} status={$result['status']}\n");
    } else {
        $stillBad++;
        fwrite(STDOUT, "FAIL idempotency_key={$idempotencyKey} status={$result['status']} error={$result['error']}\n");
    }
}
fclose($handle);

fwrite(STDOUT, sprintf(
    "replay-leads: %d pendientes, %d reenviados OK, %d siguen fallando, %d descartados por" .
    " antiguos (solo_log > %dh), %d líneas ilegibles%s.\n",
    $pending,
    $sentNow,
    $stillBad,
    $tooOld,
    $maxAgeHours,
    $unparsable,
    $dryRun ? ' (dry-run, no se envió nada)' : ''
));

exit($stillBad > 0 ? 1 : 0);
