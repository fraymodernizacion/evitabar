<?php

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $localConfig = [];
    $localConfigFile = __DIR__ . '/local.php';
    if (is_file($localConfigFile)) {
        $loaded = require $localConfigFile;
        if (is_array($loaded)) {
            $localConfig = $loaded;
        }
    }

    $host = ($localConfig['db.host'] ?? getenv('DB_HOST')) ?: '127.0.0.1';
    $port = ($localConfig['db.port'] ?? getenv('DB_PORT')) ?: '3306';
    $dbname = ($localConfig['db.name'] ?? getenv('DB_NAME')) ?: 'pase_evita';
    $user = ($localConfig['db.user'] ?? getenv('DB_USER')) ?: 'root';
    $pass = ($localConfig['db.pass'] ?? getenv('DB_PASS')) ?: '';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    return $pdo;
}
