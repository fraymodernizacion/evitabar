<?php

declare(strict_types=1);

$config = require __DIR__ . '/../config/app.php';

date_default_timezone_set($config['timezone']);

if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session']['name']);

    session_set_cookie_params([
        'lifetime' => $config['session']['lifetime'],
        'path' => '/',
        'domain' => '',
        'secure' => $config['session']['secure'],
        'httponly' => $config['session']['httponly'],
        'samesite' => $config['session']['samesite'],
    ]);

    session_start();

    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = time();
    }

    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }

    $inactiveFor = time() - (int) $_SESSION['last_activity'];
    if ($inactiveFor > (int) $config['session']['lifetime']) {
        session_unset();
        session_destroy();
        session_start();
    }

    $_SESSION['last_activity'] = time();

    if ((time() - (int) $_SESSION['created_at']) > 900) {
        session_regenerate_id(true);
        $_SESSION['created_at'] = time();
    }
}
