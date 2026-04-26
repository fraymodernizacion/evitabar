<?php

declare(strict_types=1);

return [
    'app_name' => 'Pase Evita',
    'base_url' => getenv('APP_BASE_URL') ?: 'http://localhost:8000',
    'timezone' => getenv('APP_TIMEZONE') ?: 'America/Argentina/Catamarca',
    'session' => [
        'name' => 'pase_evita_session',
        'lifetime' => 60 * 60 * 4,
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ],
    'security' => [
        'password_min_length' => 8,
    ],
];
