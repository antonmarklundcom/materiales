<?php
/**
 * Desktop WhatsApp shortcut; the quote form remains the primary CTA.
 */

declare(strict_types=1);

$whatsapp = preg_replace('/\D+/', '', (string) site('whatsapp'));
if ($whatsapp === '') {
    return;
}
?>
<a class="wa-float" href="https://wa.me/<?= e($whatsapp) ?>" data-ev="whatsapp_click" data-ev-loc="float-desktop" aria-label="Escribinos por WhatsApp">
  <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.3 4.7 4.8-1.3A10 10 0 1 0 12 2Zm5.3 14.1c-.2.6-1.2 1.2-1.7 1.2-.4 0-1 .1-3-.8-2.5-1-4.1-3.6-4.2-3.8-.1-.2-1-1.3-1-2.5s.6-1.8.8-2c.2-.2.5-.3.6-.3h.5c.2 0 .4 0 .6.4l.8 2c.1.2.1.3 0 .5l-.3.5-.3.3c-.1.1-.2.3-.1.5.1.2.6 1.1 1.4 1.8 1 .9 1.8 1.2 2 1.3.2.1.4.1.5-.1l.7-.8c.2-.2.3-.2.5-.1l2 .9c.2.1.4.2.4.3.1.2.1.8-.2 1.4Z"/></svg>
</a>
