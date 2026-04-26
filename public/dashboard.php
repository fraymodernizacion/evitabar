<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$user = require_login(['client']);
maybeApplyQuarterlyMaintenance((int) $user['id']);
$user = getUserById((int) $user['id']) ?? $user;

$progress = getVisitsNeededForNextAndMaintain((int) $user['id']);
$maintenanceStatus = getMaintenanceStatusForUser((int) $user['id']);
$activeBenefits = getUserActiveBenefits((int) $user['level']);
$levelThresholds = level_thresholds();
$appName = setting('program_name', 'Pase Evita') ?? 'Pase Evita';

$levelTarget = (int) $user['level'] === 1 ? 2 : 3;
$nextTargetVisits = $levelTarget === 2 ? $levelThresholds[2] : $levelThresholds[3];
$progressToNext = max(0, $nextTargetVisits - (int) $user['visits_count']);
$progressTotal = max(1, $nextTargetVisits - ($levelTarget === 2 ? $levelThresholds[1] : $levelThresholds[2]));
$progressDone = max(0, $progressTotal - $progressToNext);
$progressPercent = (int) min(100, round(($progressDone / $progressTotal) * 100));

$heroNumber = (int) $user['level'] === 3
    ? max(0, (int) $progress['maintain_needed'])
    : $progressToNext;

$heroTitle = sprintf('Te faltan %d visitas', $heroNumber);

$heroSubtitle = (int) $user['level'] === 3
    ? 'y mantenés tu nivel'
    : 'para subir de nivel';

$heroHint = (int) $user['level'] === 3
    ? sprintf('Tus visitas se revisan cada %s meses.', setting('maintenance_period_months', '3'))
    : 'para llegar al Nivel 3.';
$heroBenefits = array_slice($activeBenefits, 0, 4);

$maintenanceWarning = null;
if (!empty($maintenanceStatus['needs_warning']) && isset($maintenanceStatus['days_left'])) {
    $maintenanceWarning = sprintf(
        'Te quedan %d días para la revisión de nivel. Sumá %d visitas en este período para no bajar.',
        (int) $maintenanceStatus['days_left'],
        max(0, (int) $maintenanceStatus['minimum_visits'] - (int) $maintenanceStatus['recent_visits'])
    );
}

$pageTitle = 'Inicio | Pase Evita';
require __DIR__ . '/../includes/header.php';
?>
<?php if ($maintenanceWarning): ?>
<section class="card alert-card alert-card-warning">
    <strong>Atención, <?= e(user_first_name($user)) ?>.</strong>
    <p><?= e($maintenanceWarning) ?></p>
</section>
<?php endif; ?>

<section class="hero-poster">
    <div class="hero-copy">
        <p class="hero-greeting">¡Hola, <?= e(user_first_name($user)) ?>!</p>
        <p class="hero-premise">¡Premiamos tu lealtad!</p>
        <h2 class="hero-headline">A más visitas, más beneficios</h2>
    </div>

    <article class="phone-mockup" aria-label="Resumen de membresía">
        <div class="phone-screen">
            <div class="screen-brand-wrap">
                <div class="logo-frame logo-frame-screen" aria-hidden="true">
                    <img src="<?= e(asset_url('/public/assets/images/logo-evita-bar.png')) ?>" alt="">
                </div>
            </div>

            <div class="level-ring" style="--progress: <?= $progressPercent ?>%;">
                <div class="level-ring-inner">
                    <span class="level-label">Nivel</span>
                    <strong class="level-number"><?= (int) $user['level'] ?></strong>
                </div>
            </div>

            <div class="level-status">
                <strong><?= e($heroTitle) ?></strong>
                <p><?= e($heroSubtitle) ?></p>
                <small><?= e($heroHint) ?></small>
            </div>

            <div class="benefits-panel">
                <ul class="benefits-mini-list">
                    <?php foreach ($heroBenefits as $benefit): ?>
                        <li>
                            <span class="benefit-dot">★</span>
                            <div>
                                <strong><?= e($benefit['title']) ?></strong>
                                <p><?= e($benefit['conditions'] ?: $benefit['description']) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="pager-dots" aria-hidden="true">
                <span class="dot dot-active"></span>
                <span class="dot"></span>
            </div>

            <a class="phone-cta" href="/public/qr.php">MI PASE EVITA</a>
        </div>
    </article>
</section>

<section class="card stats-grid home-stats">
    <article>
        <p class="label">Visitas</p>
        <p class="value"><?= (int) $user['visits_count'] ?></p>
    </article>
    <article>
        <p class="label">Para subir</p>
        <p class="value"><?= (int) $progress['next_level_needed'] ?></p>
    </article>
    <article>
        <p class="label">Para mantener</p>
        <p class="value"><?= (int) $progress['maintain_needed'] ?></p>
    </article>
</section>

<?php if ((int) $user['level'] > 1): ?>
<section class="card">
    <h3>Revisión de nivel</h3>
    <p class="muted">
        Cada <?= (int) $maintenanceStatus['period_months'] ?> meses se revisa tu nivel.
        Para mantener el nivel actual necesitás <?= (int) $maintenanceStatus['minimum_visits'] ?> visitas en ese período.
    </p>
    <?php if (!empty($maintenanceStatus['days_left'])): ?>
        <p><strong><?= (int) $maintenanceStatus['days_left'] ?> días</strong> para la próxima revisión.</p>
    <?php endif; ?>
</section>
<?php endif; ?>

<section class="card">
    <h3>Beneficios activos</h3>
    <?php if (empty($activeBenefits)): ?>
        <p class="muted">Todavía no hay beneficios activos.</p>
    <?php else: ?>
        <ul class="benefit-list">
            <?php foreach ($activeBenefits as $benefit): ?>
                <li>
                    <strong><?= e($benefit['title']) ?></strong>
                    <p><?= e($benefit['description']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="card">
    <h3>Cómo usar tu pase</h3>
    <ol class="steps-list">
        <li>Mostrá tu QR desde <strong>Mi Pase Evita</strong> al llegar o cuando quieras canjear algo.</li>
        <li>El personal registra tu visita y verifica tu nivel y beneficios activos.</li>
        <li>Si canjeás un beneficio, queda guardado en tu historial.</li>
    </ol>
    <div class="bottom-cta-wrap">
        <a class="btn btn-secondary btn-wide" href="/public/history.php">Ver historial</a>
    </div>
</section>

<nav class="bottom-nav">
    <a href="/public/dashboard.php" class="active">Inicio</a>
    <a href="/public/benefits.php">Beneficios</a>
    <a href="/public/qr.php">Mi Pase Evita</a>
    <a href="/public/logout.php">Salir</a>
</nav>
<?php require __DIR__ . '/../includes/footer.php'; ?>
