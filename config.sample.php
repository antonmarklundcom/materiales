<?php
/**
 * config.sample.php — plantilla de config/vendercrm.php.
 *
 * EN EL SERVIDOR: copiar a  public_html/config/vendercrm.php  (el repo ES el docroot en este
 * hosting; config/ queda bloqueada por .htaccess — ver DEPLOY.md).
 * NUNCA se commitea el archivo real: .gitignore ignora /config/ y /storage/.
 *
 *   cp config.sample.php config/vendercrm.php   &&   editar los valores
 *
 * Si el archivo no existe o la clave está vacía, el handler NO rompe: registra el lead
 * completo en storage/leads.log y el visitante igual llega a /gracias/ (plan §4.5). Ese modo
 * "sólo-log" es el que corre en CI, así que está probado en cada PR. Cuando aparezca la
 * config, los leads que hayan quedado en el log se pueden reenviar a mano al CRM: cada línea
 * trae el payload exacto, incluida su idempotency_key, así que un reenvío no duplica nada.
 *
 * RUTEO DE LEADS (fase 10): el sitio manda un único `source` ('site:materiales') y
 * distingue los dos formularios con `fields.tipo`: las altas de /proveedores/ viajan con
 * `fields.tipo = 'proveedor'` y los pedidos de cotización NO mandan `tipo` en absoluto. La
 * regla que separa unos de otros se configura en VenderCRM, nunca en el PHP del sitio.
 *
 * Los IDs de GA4 y del Meta Pixel NO van acá: van en data/site.php (ga4_id, meta_pixel_id,
 * vc_attribution), porque no son secretos y se emiten en el HTML.
 * La clave y la URL las genera VenderCRM al crear el registro del sitio en **Sitios**
 * (input humano de la fase 2, plan §7).
 */

declare(strict_types=1);

return [
    // Base del CRM, sin barra final. Ej: 'https://app.vendercrm.com'
    'url'     => '',

    // Clave de API del SITIO (se muestra una sola vez al crearla). Viaja en X-Api-Key.
    'api_key' => '',

    // Timeout de la request al CRM, en segundos (plan §3).
    'timeout' => 10,

    // OPCIONAL. Sin este valor, partials/lead.php genera y persiste un secreto aleatorio
    // propio la primera vez que lo necesita (storage/.form-secret, nunca en el repo), así
    // que el formulario ya es seguro sin tocar esto. Cargá un valor acá sólo si querés fijar
    // o rotar el secreto vos mismo — una cadena aleatoria larga (p. ej. `openssl rand -hex
    // 32`). Cambiarlo invalida cualquier formulario ya renderizado en el momento del cambio.
    'form_secret' => '',

    // AVISOS DE LEAD (recomendado): sin esto, un pedido que queda en modo sólo-log o que el
    // CRM rechaza no le llega a nadie. Email vía mail() de PHP (Hostinger lo trae) y/o
    // Telegram (crear un bot con @BotFather, y el chat_id del chat donde querés el aviso).
    'notify_email'       => '',
    'notify_from'        => '',      // vacío = no-reply@materiales.com.py
    'telegram_bot_token' => '',
    'telegram_chat_id'   => '',
    // 'todos' = aviso por cada lead; 'problemas' = sólo solo_log, fallo_crm y retenido.
    'notify_on'          => 'todos',

    // Meses que se guardan los storage/leads-AAAA-MM.log rotados (tools/maintenance.php). Si
    // lo cambiás, cambiá también la sección "Conservación" de /politica-de-privacidad/.
    'leads_retention_months' => 12,
];
