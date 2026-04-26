<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$user = current_user();
if (!$user) {
    header('Location: /public/login.php');
    exit;
}

if (in_array($user['role'], ['staff', 'admin'], true)) {
    header('Location: /admin/index.php');
    exit;
}

header('Location: /public/dashboard.php');
exit;
