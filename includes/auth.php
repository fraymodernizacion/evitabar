<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function attempt_login(string $identifier, string $password, array $allowedRoles = ['client', 'staff', 'admin']): bool
{
    $sql = 'SELECT * FROM users WHERE (email = :email_identifier OR dni = :dni_identifier) LIMIT 1';
    $stmt = db()->prepare($sql);
    $stmt->execute([
        'email_identifier' => trim($identifier),
        'dni_identifier' => trim($identifier),
    ]);
    $user = $stmt->fetch();

    if (!$user) {
        return false;
    }

    if (!in_array($user['role'], $allowedRoles, true)) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['role'] = $user['role'];

    return true;
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}

function require_login(array $roles = ['client', 'staff', 'admin']): array
{
    $user = current_user();
    if (!$user) {
        header('Location: /public/login.php');
        exit;
    }

    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('No tenés permisos para acceder a esta sección.');
    }

    $scriptName = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
    if (
        $user['role'] === 'client'
        && userRequiresPasswordChange((int) $user['id'])
        && !in_array($scriptName, ['change-password.php', 'login.php', 'logout.php'], true)
    ) {
        header('Location: /public/change-password.php');
        exit;
    }

    return $user;
}

function require_admin_login(array $roles = ['staff', 'admin']): array
{
    $user = current_user();
    if (!$user) {
        header('Location: /admin/login.php');
        exit;
    }

    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('No tenés permisos para acceder al panel.');
    }

    return $user;
}
