<?php
// Configuración de Seguridad y Accesos - FAB LAB Perú
// Ubicación oficial: /fabpanel

if (file_exists(__DIR__ . '/secrets.php')) {
    require_once __DIR__ . '/secrets.php';
}

// Correo oficial del administrador
define('ADMIN_EMAIL', 'contacto@fablablima.org');

// Usuarios con acceso al panel
$AUTHORIZED_USERS = [
    'contacto@fablablima.org' => [
        'name' => 'Administración FAB LAB',
        'role' => 'admin',
        'password' => 'FabLab2026!',
    ],
    'henry@fablablima.org' => [
        'name' => 'Henry Sánchez',
        'role' => 'instructor',
        'password' => 'FabPeru*2026',
    ],
    'graceschwan@fablablima.org' => [
        'name' => 'Grace Schwan',
        'role' => 'instructor',
        'password' => 'FabPeru*2026',
    ],
    'carmengu1997@gmail.com' => [
        'name' => 'Carmen Gutiérrez',
        'role' => 'instructor',
        'password' => 'FabPeru*2026',
    ],
    'evkusi@gmail.com' => [
        'name' => 'Evelyn Cuadrado',
        'role' => 'instructor',
        'password' => 'FabPeru*2026',
    ],
    'hayashi@fablablima.org' => [
        'name' => 'Hayashi Mateo',
        'role' => 'instructor',
        'password' => 'FabPeru*2026',
    ],
    'baca.francheska@gmail.com' => [
        'name' => 'Francheska Baca',
        'role' => 'instructor',
        'password' => 'FabPeru*2026',
    ],
    'beno@fablablima.org' => [
        'name' => 'Beno Juarez',
        'role' => 'instructor',
        'password' => 'FabPeru*2026',
    ],
    'mmejia.fa@gmail.com' => [
        'name' => 'María Angela Mejía',
        'role' => 'instructor',
        'password' => 'FabPeru*2026',
    ],
    'estebanmiguel.valladares@gmail.com' => [
        'name' => 'Esteban Valladares',
        'role' => 'instructor',
        'password' => 'FabPeru*2026',
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
