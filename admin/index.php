<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$staff = require_admin_login(['staff', 'admin']);
maybeApplyQuarterlyMaintenance((int) $staff['id']);
$stats = adminDashboardStats();

$pageTitle = 'Dashboard Admin | Pase Evita';
require __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Dashboard</h2>
    <p class="muted">Hola, <?= e($staff['name']) ?> (<?= e($staff['role']) ?>)</p>
</section>

<section class="stats-grid card">
    <article>
        <p class="label">Clientes registrados</p>
        <p class="value"><?= (int) $stats['total_users'] ?></p>
    </article>
    <article>
        <p class="label">Visitas hoy</p>
        <p class="value"><?= (int) $stats['visits_today'] ?></p>
    </article>
    <article>
        <p class="label">Canjes hoy</p>
        <p class="value"><?= (int) $stats['redemptions_today'] ?></p>
    </article>
</section>

<section class="card">
    <h3>Clientes por nivel</h3>
    <ul class="inline-list">
        <li>Nivel 1: <?= (int) $stats['users_by_level'][1] ?></li>
        <li>Nivel 2: <?= (int) $stats['users_by_level'][2] ?></li>
        <li>Nivel 3: <?= (int) $stats['users_by_level'][3] ?></li>
    </ul>
</section>

<section class="card action-grid">
    <a class="btn btn-primary" href="/admin/scan.php">Escanear / cargar QR</a>
    <a class="btn btn-secondary" href="/admin/user.php">Buscar cliente</a>
    <?php if (($staff['role'] ?? '') === 'admin'): ?>
        <a class="btn btn-secondary" href="/admin/benefits.php">Gestionar beneficios</a>
    <?php endif; ?>
    <a class="btn btn-ghost" href="/admin/logout.php">Cerrar sesión</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
