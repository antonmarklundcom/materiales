#!/usr/bin/env php
<?php
/**
 * tools/lead-digest.php — resumen diario de leads para cron (improvement report #2, B3/R1).
 *
 * Cuenta las líneas de storage/leads.log (y del último leads-AAAA-MM.log rotado) de las
 * últimas N horas por resultado: enviado, solo_log, fallo_crm, retenido y descartado, con el
 * motivo de los retenidos y descartados. Lo manda por los canales de lead_alert() (email /
 * Telegram de config/vendercrm.php) y lo imprime por stdout.
 *
 * Se manda aunque no haya ningún lead: un "0 leads" diario también confirma que el cron y el
 * canal de aviso siguen vivos. Si ningún canal está configurado sólo imprime.
 *
 * Uso: php tools/lead-digest.php [--hours=24] [--dry-run] [--log=ARCHIVO]
 *   --dry-run  imprime el resumen sin mandarlo.
 *   --log=     sólo para el fixture de CI (tools/smoke.php); repetible.
 *
 * Cron sugerido (hPanel), todos los días a las 7:00 de Paraguay (UTC-3 → 10:00 UTC; ajustar
 * a la hora del servidor) — ver DEPLOY.md:
 *   0 10 * * * /usr/bin/php /home/USUARIO/domains/materiales.com.py/public_html/tools/lead-digest.php >> /home/USUARIO/logs/lead-digest.log 2>&1
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('lead-digest.php es sólo CLI.');
}

require __DIR__ . '/../partials/init.php';
require __DIR__ . '/../partials/lead.php';

$dryRun   = in_array('--dry-run', $argv, true);
$hours    = 24;
$logFiles = [];
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--hours=')) {
        $hours = max(1, (int) substr($arg, 8));
    } elseif (str_starts_with($arg, '--log=')) {
        $logFiles[] = substr($arg, 6);
    }
}
if ($logFiles === []) {
    $rotated = glob(STORAGE_DIR . '/leads-[0-9][0-9][0-9][0-9]-[0-9][0-9]*.log') ?: [];
    sort($rotated);
    if ($rotated !== []) {
        $logFiles[] = end($rotated);
    }
    $logFiles[] = STORAGE_DIR . '/leads.log';
}

$since    = time() - $hours * 3600;
$outcomes = ['enviado' => 0, 'solo_log' => 0, 'fallo_crm' => 0, 'retenido' => 0, 'descartado' => 0];
$reasons  = ['retenido' => [], 'descartado' => []];
$tipos    = ['cotizacion' => 0, 'proveedor' => 0];
$unparsable = 0;

foreach ($logFiles as $file) {
    $handle = is_file($file) ? @fopen($file, 'r') : false;
    if ($handle === false) {
        continue;
    }
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $record = json_decode($line, true);
        if (!is_array($record)) {
            $unparsable++;
            continue;
        }
        $ts = strtotime((string) ($record['ts'] ?? ''));
        if ($ts === false || $ts < $since) {
            continue;
        }
        $outcome = (string) ($record['outcome'] ?? '');
        if (!isset($outcomes[$outcome])) {
            continue;
        }
        $outcomes[$outcome]++;
        if (isset($reasons[$outcome])) {
            $reason = (string) ($record['reason'] ?? 'sin motivo');
            $reasons[$outcome][$reason] = ($reasons[$outcome][$reason] ?? 0) + 1;
        }
        if ($outcome !== 'descartado') {
            $isSupplier = (string) ($record['payload']['fields']['tipo'] ?? '') === 'proveedor';
            $tipos[$isSupplier ? 'proveedor' : 'cotizacion']++;
        }
    }
    fclose($handle);
}

$received = $outcomes['enviado'] + $outcomes['solo_log'] + $outcomes['fallo_crm'] + $outcomes['retenido'];
$lines = [
    "Últimas {$hours} h (hasta " . gmdate('Y-m-d H:i') . ' UTC):',
    "  Pedidos recibidos: {$received} ({$tipos['cotizacion']} cotizaciones, {$tipos['proveedor']} altas de proveedor)",
    "  enviado (ya en VenderCRM): {$outcomes['enviado']}",
    "  solo_log (sin CRM, cargar a mano): {$outcomes['solo_log']}",
    "  fallo_crm (los reintenta replay-leads): {$outcomes['fallo_crm']}",
    "  retenido (revisar a mano): {$outcomes['retenido']}",
    "  descartado (filtro anti-bot, sin contacto válido): {$outcomes['descartado']}",
];
foreach ($reasons as $outcome => $byReason) {
    if ($byReason !== []) {
        arsort($byReason);
        $parts = [];
        foreach ($byReason as $reason => $n) {
            $parts[] = "{$reason} {$n}";
        }
        $lines[] = "  Motivos {$outcome}: " . implode(', ', $parts);
    }
}
if ($unparsable > 0) {
    $lines[] = "  ¡{$unparsable} líneas ilegibles en el log!";
}
$text = implode("\n", $lines);
fwrite(STDOUT, $text . "\n");

if (!$dryRun) {
    $problems = $outcomes['solo_log'] + $outcomes['fallo_crm'] + $outcomes['retenido'];
    $subject  = "[materiales.com.py] Resumen diario: {$received} pedidos"
        . ($problems > 0 ? " ({$problems} para revisar)" : '');
    if (!lead_alert($subject, $text . "\n\nDetalle en storage/leads.log.")) {
        fwrite(STDERR, "Sin notify_email ni Telegram en config/vendercrm.php: el resumen sólo se imprimió.\n");
    }
}
exit(0);
