<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function app_config(): array
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../config/app.php';
    }

    return $config;
}

function app_version(): string
{
    static $version = null;

    if ($version !== null) {
        return $version;
    }

    $config = app_config();
    $candidates = [
        __DIR__ . '/../config/app.php',
        __DIR__ . '/../includes/functions.php',
        __DIR__ . '/../includes/header.php',
        __DIR__ . '/../includes/footer.php',
        __DIR__ . '/../public/assets/css/styles.css',
        __DIR__ . '/../public/assets/js/app.js',
        __DIR__ . '/../public/sw.js',
    ];

    $mtime = 0;
    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            $mtime = max($mtime, (int) filemtime($candidate));
        }
    }

    $version = $mtime > 0 ? (string) $mtime : (string) ($config['version'] ?? '1.0.0');

    return $version;
}

function asset_url(string $path): string
{
    $path = '/' . ltrim($path, '/');
    $filesystemPath = __DIR__ . '/..' . $path;

    $version = app_version();
    if (is_file($filesystemPath)) {
        $version = (string) filemtime($filesystemPath);
    }

    return $path . '?v=' . rawurlencode($version);
}

function setting(string $key, ?string $default = null): ?string
{
    static $cache = null;

    if ($cache === null) {
        $cache = [];
        $stmt = db()->query('SELECT setting_key, setting_value FROM settings');
        foreach ($stmt->fetchAll() as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $cache[$key] ?? $default;
}

function to_int_setting(string $key, int $default): int
{
    return (int) (setting($key, (string) $default) ?? $default);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $flashes;
}

function random_token(int $length = 64): string
{
    return bin2hex(random_bytes((int) ceil($length / 2)));
}

function sanitize_phone(string $phone): string
{
    return preg_replace('/[^0-9+]/', '', $phone) ?? '';
}

function normalize_person_name(string $value): string
{
    $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');

    return $value;
}

function user_first_name(array $user): string
{
    $fullName = trim((string) ($user['name'] ?? ''));
    if ($fullName === '') {
        return 'Hola';
    }

    $parts = preg_split('/\s+/u', $fullName) ?: [];
    $first = trim((string) ($parts[0] ?? ''));

    return $first !== '' ? $first : 'Hola';
}

function user_full_name(array $user): string
{
    return trim((string) ($user['name'] ?? ''));
}

function level_thresholds(): array
{
    return [
        1 => to_int_setting('level_1_min', 0),
        2 => to_int_setting('level_2_min', 4),
        3 => to_int_setting('level_3_min', 8),
    ];
}

function calculateLevelFromVisits(int $visitsCount): int
{
    $thresholds = level_thresholds();

    if ($visitsCount >= $thresholds[3]) {
        return 3;
    }

    if ($visitsCount >= $thresholds[2]) {
        return 2;
    }

    return 1;
}

function calculateUserLevel(int $userId): int
{
    $stmt = db()->prepare('SELECT visits_count FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        return 1;
    }

    return calculateLevelFromVisits((int) $user['visits_count']);
}

function applyLevelForUser(int $userId): void
{
    $newLevel = calculateUserLevel($userId);
    $stmt = db()->prepare('UPDATE users SET level = :level, updated_at = NOW() WHERE id = :id');
    $stmt->execute([
        'level' => $newLevel,
        'id' => $userId,
    ]);
}

function getVisitsNeededForNextAndMaintain(int $userId): array
{
    $stmt = db()->prepare('SELECT id, level, visits_count FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        return [
            'next_level_needed' => 0,
            'maintain_needed' => 0,
            'current_level' => 1,
        ];
    }

    $level = (int) $user['level'];
    $visitsCount = (int) $user['visits_count'];
    $thresholds = level_thresholds();

    $nextNeeded = 0;
    if ($level === 1) {
        $nextNeeded = max(0, $thresholds[2] - $visitsCount);
    } elseif ($level === 2) {
        $nextNeeded = max(0, $thresholds[3] - $visitsCount);
    }

    $periodMonths = to_int_setting('maintenance_period_months', 3);
    $maintainNeeded = 0;

    if ($level > 1) {
        $minForMaintain = to_int_setting('maintain_level_' . $level, $level === 2 ? 2 : 3);
        $stmt = db()->prepare('SELECT COUNT(*) as c FROM visits WHERE user_id = :user_id AND visit_date >= DATE_SUB(NOW(), INTERVAL :months MONTH)');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':months', $periodMonths, PDO::PARAM_INT);
        $stmt->execute();
        $recent = (int) ($stmt->fetch()['c'] ?? 0);
        $maintainNeeded = max(0, $minForMaintain - $recent);
    }

    return [
        'next_level_needed' => $nextNeeded,
        'maintain_needed' => $maintainNeeded,
        'current_level' => $level,
    ];
}

function maybeApplyQuarterlyMaintenance(int $userId): void
{
    $stmt = db()->prepare('SELECT id, level, updated_at FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        return;
    }

    $level = (int) $user['level'];
    if ($level <= 1) {
        return;
    }

    // Regla de mantenimiento trimestral configurable desde settings.
    $months = to_int_setting('maintenance_period_months', 3);
    $maintainMin = to_int_setting('maintain_level_' . $level, $level === 2 ? 2 : 3);

    $visitsStmt = db()->prepare('SELECT COUNT(*) AS c FROM visits WHERE user_id = :user_id AND visit_date >= DATE_SUB(NOW(), INTERVAL :months MONTH)');
    $visitsStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $visitsStmt->bindValue(':months', $months, PDO::PARAM_INT);
    $visitsStmt->execute();
    $recentVisits = (int) ($visitsStmt->fetch()['c'] ?? 0);

    if ($recentVisits >= $maintainMin) {
        return;
    }

    $newLevel = max(1, $level - 1);

    $update = db()->prepare('UPDATE users SET level = :new_level, updated_at = NOW() WHERE id = :id');
    $update->execute([
        'new_level' => $newLevel,
        'id' => $userId,
    ]);

    error_log(sprintf('PaseEvita maintenance user=%d level=%d->%d recentVisits=%d', $userId, $level, $newLevel, $recentVisits));
}

function canRegisterVisitNow(int $userId, int $minutesWindow): array
{
    $stmt = db()->prepare('SELECT visit_date FROM visits WHERE user_id = :user_id ORDER BY visit_date DESC LIMIT 1');
    $stmt->execute(['user_id' => $userId]);
    $last = $stmt->fetch();

    if (!$last) {
        return ['allowed' => true, 'minutes_left' => 0];
    }

    $lastTime = new DateTimeImmutable($last['visit_date']);
    $now = new DateTimeImmutable('now');
    $diffSeconds = $now->getTimestamp() - $lastTime->getTimestamp();
    $windowSeconds = $minutesWindow * 60;

    if ($diffSeconds >= $windowSeconds) {
        return ['allowed' => true, 'minutes_left' => 0];
    }

    $left = (int) ceil(($windowSeconds - $diffSeconds) / 60);

    return ['allowed' => false, 'minutes_left' => $left];
}

function registerVisit(int $userId, int $staffId, bool $force, string $note): array
{
    $minutesWindow = to_int_setting('visit_block_minutes', 120);
    // Evita doble carga accidental de visitas en períodos cortos.
    $can = canRegisterVisitNow($userId, $minutesWindow);

    if (!$can['allowed'] && !$force) {
        return [
            'ok' => false,
            'message' => 'Ya se registró una visita reciente. Faltan ' . $can['minutes_left'] . ' minutos.',
        ];
    }

    $notes = trim($note);
    if ($force && $notes === '') {
        $notes = 'Carga forzada por staff dentro de ventana de bloqueo.';
    }

    $stmt = db()->prepare('INSERT INTO visits (user_id, staff_id, visit_date, notes, created_at) VALUES (:user_id, :staff_id, NOW(), :notes, NOW())');
    $stmt->execute([
        'user_id' => $userId,
        'staff_id' => $staffId,
        'notes' => $notes !== '' ? $notes : null,
    ]);

    db()->prepare('UPDATE users SET visits_count = visits_count + 1, updated_at = NOW() WHERE id = :id')->execute(['id' => $userId]);
    applyLevelForUser($userId);

    return [
        'ok' => true,
        'message' => 'Visita registrada correctamente.',
    ];
}

function activeBenefitsByLevel(): array
{
    $stmt = db()->query('SELECT * FROM benefits WHERE active = 1 ORDER BY required_level ASC, sort_order ASC, id ASC');
    $items = $stmt->fetchAll();

    $grouped = [1 => [], 2 => [], 3 => []];
    foreach ($items as $item) {
        $grouped[(int) $item['required_level']][] = $item;
    }

    return $grouped;
}

function getUserActiveBenefits(int $level): array
{
    $stmt = db()->prepare('SELECT * FROM benefits WHERE active = 1 AND required_level <= :lvl ORDER BY required_level ASC, sort_order ASC, id ASC');
    $stmt->execute(['lvl' => $level]);

    return $stmt->fetchAll();
}

function redeemBenefit(int $userId, int $benefitId, int $staffId, string $note): array
{
    $benefitStmt = db()->prepare('SELECT * FROM benefits WHERE id = :id AND active = 1 LIMIT 1');
    $benefitStmt->execute(['id' => $benefitId]);
    $benefit = $benefitStmt->fetch();

    if (!$benefit) {
        return ['ok' => false, 'message' => 'El beneficio no está disponible.'];
    }

    $userStmt = db()->prepare('SELECT level FROM users WHERE id = :id LIMIT 1');
    $userStmt->execute(['id' => $userId]);
    $user = $userStmt->fetch();

    if (!$user) {
        return ['ok' => false, 'message' => 'Cliente no encontrado.'];
    }

    if ((int) $user['level'] < (int) $benefit['required_level']) {
        return ['ok' => false, 'message' => 'El nivel del cliente no alcanza para este beneficio.'];
    }

    $stmt = db()->prepare('INSERT INTO redemptions (user_id, benefit_id, staff_id, redeemed_at, notes) VALUES (:user_id, :benefit_id, :staff_id, NOW(), :notes)');
    $stmt->execute([
        'user_id' => $userId,
        'benefit_id' => $benefitId,
        'staff_id' => $staffId,
        'notes' => trim($note) !== '' ? trim($note) : null,
    ]);

    return ['ok' => true, 'message' => 'Beneficio canjeado correctamente.'];
}

function adminDashboardStats(): array
{
    $totalUsers = (int) db()->query("SELECT COUNT(*) AS c FROM users WHERE role = 'client'")->fetch()['c'];
    $visitsToday = (int) db()->query('SELECT COUNT(*) AS c FROM visits WHERE DATE(visit_date) = CURDATE()')->fetch()['c'];
    $redemptionsToday = (int) db()->query('SELECT COUNT(*) AS c FROM redemptions WHERE DATE(redeemed_at) = CURDATE()')->fetch()['c'];

    $byLevel = db()->query("SELECT level, COUNT(*) AS c FROM users WHERE role = 'client' GROUP BY level ORDER BY level ASC")->fetchAll();
    $levels = [1 => 0, 2 => 0, 3 => 0];
    foreach ($byLevel as $row) {
        $levels[(int) $row['level']] = (int) $row['c'];
    }

    return [
        'total_users' => $totalUsers,
        'visits_today' => $visitsToday,
        'redemptions_today' => $redemptionsToday,
        'users_by_level' => $levels,
    ];
}

function userRecentHistory(int $userId, int $limit = 8): array
{
    $stmt = db()->prepare('SELECT v.visit_date, v.notes, u.name AS staff_name FROM visits v LEFT JOIN users u ON u.id = v.staff_id WHERE v.user_id = :user_id ORDER BY v.visit_date DESC LIMIT :lim');
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function userRedemptionHistory(int $userId, int $limit = 8): array
{
    $stmt = db()->prepare('SELECT r.redeemed_at, r.notes, b.title, u.name AS staff_name FROM redemptions r INNER JOIN benefits b ON b.id = r.benefit_id LEFT JOIN users u ON u.id = r.staff_id WHERE r.user_id = :user_id ORDER BY r.redeemed_at DESC LIMIT :lim');
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function findUsers(string $query): array
{
    $search = '%' . trim($query) . '%';
    $stmt = db()->prepare("SELECT * FROM users WHERE role = 'client' AND (name LIKE :q OR dni LIKE :q OR phone LIKE :q OR email LIKE :q) ORDER BY created_at DESC LIMIT 30");
    $stmt->execute(['q' => $search]);

    return $stmt->fetchAll();
}

function getUserByToken(string $token): ?array
{
    $stmt = db()->prepare("SELECT * FROM users WHERE qr_token = :token AND role = 'client' LIMIT 1");
    $stmt->execute(['token' => trim($token)]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function getUserById(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function scanUrlFromToken(string $token): string
{
    $appConfig = require __DIR__ . '/../config/app.php';
    $baseUrl = rtrim($appConfig['base_url'], '/');

    return $baseUrl . '/admin/scan.php?token=' . urlencode($token);
}

function extractToken(string $raw): string
{
    $candidate = trim($raw);

    if ($candidate === '') {
        return '';
    }

    if (strpos($candidate, 'token=') !== false) {
        $parts = parse_url($candidate);
        if ($parts && isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (!empty($query['token']) && is_string($query['token'])) {
                return trim($query['token']);
            }
        }

        if (preg_match('/token=([a-zA-Z0-9]+)/', $candidate, $matches) === 1) {
            return $matches[1];
        }
    }

    return $candidate;
}
