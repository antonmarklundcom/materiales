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
 * Uso: php tools/replay-leads.php [--dry-run]
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
    }
}

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

$pending  = 0;
$sentNow  = 0;
$stillBad = 0;

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }

    $record = json_decode($line, true);
    if (!is_array($record) || ($record['outcome'] ?? '') !== 'fallo_crm') {
        continue;
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
        $sentNow++;
        fwrite(STDOUT, "OK  idempotency_key={$idempotencyKey} status={$result['status']}\n");
    } else {
        $stillBad++;
        fwrite(STDOUT, "FAIL idempotency_key={$idempotencyKey} status={$result['status']} error={$result['error']}\n");
    }
}
fclose($handle);

fwrite(STDOUT, sprintf(
    "replay-leads: %d pendientes, %d reenviados OK, %d siguen fallando%s.\n",
    $pending,
    $sentNow,
    $stillBad,
    $dryRun ? ' (dry-run, no se envió nada)' : ''
));

exit($stillBad > 0 ? 1 : 0);
