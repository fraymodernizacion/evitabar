<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$user = require_login(['client']);
$redemptions = userRedemptionHistory((int) $user['id'], 30);
$visits = userRecentHistory((int) $user['id'], 30);

$pageTitle = 'Historial | Pase Evita';
require __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Cómo usar tu pase</h2>
    <ol class="steps-list">
        <li>Mostrá tu QR desde <strong>Mi Pase Evita</strong> cuando llegás o cuando quieras canjear un beneficio.</li>
        <li>El personal registra tu visita y valida qué beneficios están activos.</li>
        <li>Cada canje queda guardado en este historial.</li>
    </ol>
    <p class="muted">Si tenés dudas, el personal puede ayudarte a ver qué beneficio corresponde según tu nivel.</p>
</section>

<section class="card">
    <h2>Historial de canjes</h2>
    <?php if (empty($redemptions)): ?>
        <p class="muted">Todavía no tenés canjes registrados.</p>
    <?php else: ?>
        <ul class="history-list">
            <?php foreach ($redemptions as $redemption): ?>
                <li class="history-item">
                    <div class="history-meta">
                        <strong><?= e($redemption['title']) ?></strong>
                        <span><?= e($redemption['redeemed_at']) ?></span>
                    </div>
                    <p><?= e($redemption['notes'] ?: 'Sin observaciones') ?></p>
                    <?php if (!empty($redemption['staff_name'])): ?>
                        <small>Registrado por <?= e($redemption['staff_name']) ?></small>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="card">
    <h2>Historial de visitas</h2>
    <?php if (empty($visits)): ?>
        <p class="muted">Todavía no tenés visitas registradas.</p>
    <?php else: ?>
        <ul class="history-list">
            <?php foreach ($visits as $visit): ?>
                <li class="history-item">
                    <div class="history-meta">
                        <strong>Visita registrada</strong>
                        <span><?= e($visit['visit_date']) ?></span>
                    </div>
                    <p><?= e($visit['notes'] ?: 'Sin observaciones') ?></p>
                    <?php if (!empty($visit['staff_name'])): ?>
                        <small>Registrada por <?= e($visit['staff_name']) ?></small>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<nav class="bottom-nav">
    <a href="/public/dashboard.php">Inicio</a>
    <a href="/public/benefits.php">Beneficios</a>
    <a href="/public/qr.php">Mi Pase Evita</a>
    <a href="/public/logout.php">Salir</a>
</nav>
<?php require __DIR__ . '/../includes/footer.php'; ?>
