#!/usr/bin/env php
<?php
/**
 * tools/maintenance.php — limpieza de storage/ para cron diario (improvement report #2, R5).
 *
 * 1. Rotación mensual de storage/leads.log: si su última escritura es de un mes anterior al
 *    actual, se renombra a storage/leads-AAAA-MM.log (mes de esa última escritura) con 0640.
 *    El rename se hace con el mismo flock que usa lead_log(), así que un pedido que llega en
 *    ese instante cae en el archivo rotado o en el nuevo, nunca se pierde. Correrlo todos los
 *    días es inofensivo: sólo rota una vez por mes.
 * 2. Retención: borra los leads-AAAA-MM.log de más de 'leads_retention_months' meses
 *    (config/vendercrm.php; por defecto LEAD_LOG_RETENTION_MONTHS = 12). Es lo que promete la
 *    sección "Conservación" de /politica-de-privacidad/: si cambiás el plazo, cambiá ese texto.
 * 3. storage/throttle/: borra las huellas de IP más viejas que la ventana del límite por IP
 *    (LEAD_IP_WINDOW_SECONDS) — ya no limitan nada y antes quedaban para siempre.
 *
 * Uso: php tools/maintenance.php [--dry-run] [--storage=DIR]
 *   --storage= sólo para el fixture de CI (tools/smoke.php).
 *
 * Cron sugerido (hPanel), una vez por día — ver DEPLOY.md:
 *   30 4 * * * /usr/bin/php /home/USUARIO/domains/materiales.com.py/public_html/tools/maintenance.php >> /home/USUARIO/logs/maintenance.log 2>&1
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('maintenance.php es sólo CLI.');
}

require __DIR__ . '/../partials/init.php';
require __DIR__ . '/../partials/lead.php';

$dryRun  = in_array('--dry-run', $argv, true);
$storage = STORAGE_DIR;
$now     = time();
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--storage=')) {
        $storage = substr($arg, 10);
    } elseif (str_starts_with($arg, '--now=')) {
        // Reloj inyectado, sólo para el fixture de CI.
        $now = (int) substr($arg, 6);
    }
}
$say = static function (string $msg) use ($dryRun): void {
    fwrite(STDOUT, ($dryRun ? '[dry-run] ' : '') . $msg . "\n");
};

// ---- 1. rotación mensual ---------------------------------------------------------------
$log = $storage . '/leads.log';
if (is_file($log)) {
    clearstatcache(true, $log);
    $month = gmdate('Y-m', (int) filemtime($log));
    if ($month < gmdate('Y-m', $now)) {
        $target = $storage . '/leads-' . $month . '.log';
        for ($n = 2; is_file($target); $n++) {
            $target = $storage . '/leads-' . $month . '-' . $n . '.log';
        }
        if ($dryRun) {
            $say('rotaría leads.log → ' . basename($target));
        } else {
            $handle = @fopen($log, 'r');
            if ($handle !== false && @flock($handle, LOCK_EX)) {
                $renamed = @rename($log, $target);
                flock($handle, LOCK_UN);
                fclose($handle);
                if ($renamed) {
                    @chmod($target, 0640);
                    $say('rotado leads.log → ' . basename($target));
                } else {
                    fwrite(STDERR, "No se pudo rotar {$log}.\n");
                }
            } else {
                fwrite(STDERR, "No se pudo bloquear {$log} para rotarlo.\n");
            }
        }
    }
}

// ---- 2. retención -------------------------------------------------------------------------
$months = lead_config()['leads_retention_months'];
$cutoff = gmdate('Y-m', (int) strtotime('-' . $months . ' months', $now));
foreach (glob($storage . '/leads-[0-9][0-9][0-9][0-9]-[0-9][0-9]*.log') ?: [] as $rotated) {
    if (preg_match('/leads-(\d{4}-\d{2})/', basename($rotated), $m) === 1 && $m[1] < $cutoff) {
        if (!$dryRun) {
            @unlink($rotated);
        }
        $say('borrado por retención (> ' . $months . ' meses): ' . basename($rotated));
    }
}

// ---- 3. huellas de IP vencidas ------------------------------------------------------------
$purged = 0;
foreach (glob($storage . '/throttle/*') ?: [] as $file) {
    if (is_file($file) && basename($file) !== '.htaccess'
        && $now - (int) filemtime($file) > LEAD_IP_WINDOW_SECONDS) {
        if (!$dryRun) {
            @unlink($file);
        }
        $purged++;
    }
}
$say("throttle: {$purged} huellas de IP vencidas borradas.");
exit(0);
