<?php
// Configuración de Seguridad y Accesos - FAB LAB Perú
// Este archivo protege las credenciales del panel admin.

// Cargar clave secreta desde secrets.php o variable de entorno
if (file_exists(__DIR__ . '/secrets.php')) {
    require_once __DIR__ . '/secrets.php';
}
if (!defined('GEMINI_API_KEY')) {
    define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: '');
}

define('GEMINI_MODEL', 'gemini-1.5-flash');

// Usuarios con acceso al panel
$AUTHORIZED_USERS = [
    'contacto@fablablima.org' => [
        'name' => 'Administración FAB LAB',
        'role' => 'admin',
        'password' => 'FabLab2026!',
    ],
    'beno@fablablima.org' => [
        'name' => 'Beno Juarez',
        'role' => 'instructor',
        'password' => 'BenoMaker2026!',
    ]
];

// Rutas a los almacenes de datos
define('DATA_DIR', __DIR__ . '/../data');
define('TALLERES_FILE', DATA_DIR . '/talleres.json');
define('PROPOSALS_FILE', DATA_DIR . '/proposals.json');
define('DATA_JS_FILE', __DIR__ . '/../data.js');
define('UPLOADS_DIR', __DIR__ . '/../images/uploads');
define('SITE_PHONE', '+51 989 984 480');
define('SITE_WHATSAPP', '51989984480');
