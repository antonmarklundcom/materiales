<?php
/**
 * config.sample.php — plantilla de config/vendercrm.php.
 *
 * EN EL SERVIDOR: copiar a  <dominio>/config/vendercrm.php  (FUERA de public_html).
 * NUNCA se commitea el archivo real: .gitignore ignora /config/ y /storage/.
 *
 *   cp config.sample.php config/vendercrm.php   &&   editar los valores
 *
 * Si el archivo no existe o la clave está vacía, el handler de la fase 2 NO rompe: registra
 * el lead en storage/leads.log y el visitante igual llega a /gracias/ (plan §4.5).
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

    // OPCIONAL. Endurece la trampa de tiempo del formulario: con esto, el sello que firma
    // partials/lead.php deja de depender de la api_key. Poné cualquier cadena aleatoria
    // larga (p. ej. `openssl rand -hex 32`). Si queda vacío el formulario funciona igual.
    'form_secret' => '',
];
