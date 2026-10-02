<?php
// Site configuration.
//
// Values come from the environment (via .env – see .env.example), with sensible
// defaults so the app still boots if a key is missing. Real environment
// variables override the .env file, so production can be configured without
// editing any files.
require_once __DIR__ . '/app/env.php';

return [
    'site_name'   => env('SITE_NAME', 'Nexus Pharma'),
    'tagline'     => env('TAGLINE', 'Trusted pharmaceutical solutions'),
    'email'       => env('EMAIL', 'nexuspharmaceutical02@gmail.com'),
    'currency'    => env('CURRENCY', '$'),
    'per_page'    => env_int('PER_PAGE', 16),
    // MySQL database config
    'db_host'     => env('DB_HOST', '127.0.0.1'),
    'db_name'     => env('DB_NAME', 'nexus'),
    'db_user'     => env('DB_USERNAME', 'root'),
    'db_pass'     => env('DB_PASSWORD', ''),   // set DB_PASSWORD in your .env
    'db_port'     => env_int('DB_PORT', 3306),
    'admin_user'  => env('ADMIN_USER', 'admin'),
    'admin_pass'  => env('ADMIN_PASS', 'change-me-now'),   // CHANGE THIS in .env before using anywhere real
    'free_ship_over' => env_float('FREE_SHIP_OVER', 75),
    'shipping_flat'  => env_float('SHIPPING_FLAT', 6.95),
    // External links
    'crypto_guide_url' => env('CRYPTO_GUIDE_URL', ''),
];
