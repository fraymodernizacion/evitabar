<?php

declare(strict_types=1);

$localOverrides = [];
$localConfigFile = __DIR__ . '/local.php';
if (is_file($localConfigFile)) {
    $loaded = require $localConfigFile;
    if (is_array($loaded)) {
        $localOverrides = $loaded;
    }
}

return [
    'app_name' => $localOverrides['app_name'] ?? 'Pase Evita',
    'base_url' => ($localOverrides['app.base_url'] ?? getenv('APP_BASE_URL')) ?: 'http://localhost:8000',
    'timezone' => ($localOverrides['app.timezone'] ?? getenv('APP_TIMEZONE')) ?: 'America/Argentina/Catamarca',
    'session' => [
        'name' => 'pase_evita_session',
        'lifetime' => 60 * 60 * 4,
        'secure' => (bool) ($localOverrides['session.secure'] ?? false),
        'httponly' => true,
        'samesite' => $localOverrides['session.samesite'] ?? 'Lax',
    ],
    'security' => [
        'password_min_length' => 8,
    ],
];
