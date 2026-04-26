<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../lib/qrcode/EmbeddedQr.php';

$user = require_login(['client']);
$scanUrl = scanUrlFromToken($user['qr_token']);
$qrDataUri = EmbeddedQr::asDataUri($scanUrl, 320);
$qrRemoteUrl = EmbeddedQr::remoteUrl($scanUrl, 320);
$activeBenefits = getUserActiveBenefits((int) $user['level']);
$redeemedBenefitIds = userRedeemedBenefitIds((int) $user['id']);

$pageTitle = 'Mi QR | Pase Evita';
require __DIR__ . '/../includes/header.php';
?>
<section class="card qr-card">
    <h2>Mi Pase Evita</h2>
    <p class="muted">Mostrá este QR al personal del bar.</p>

    <div class="qr-frame">
        <?php if ($qrDataUri !== ''): ?>
            <img src="<?= e($qrDataUri) ?>" alt="Código QR de cliente">
        <?php else: ?>
            <img src="<?= e($qrRemoteUrl) ?>" alt="Código QR de cliente">
        <?php endif; ?>
    </div>

    <div class="user-badge">
        <p><strong><?= e($user['name']) ?></strong></p>
        <p>Nivel <?= (int) $user['level'] ?></p>
    </div>

    <h3>Beneficios activos</h3>
    <ul class="benefit-list compact">
        <?php foreach ($activeBenefits as $benefit): ?>
            <li>
                <div class="benefit-line">
                    <strong><?= e($benefit['title']) ?></strong>
                    <?php if (!empty($redeemedBenefitIds[(int) $benefit['id']])): ?>
                        <span class="badge badge-small badge-redeemed">Ya canjeado</span>
                    <?php else: ?>
                        <span class="badge badge-small">Activo</span>
                    <?php endif; ?>
                </div>
                <p><?= e($benefit['conditions']) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<section class="card">
    <h3>Cómo usar tu pase</h3>
    <ol class="steps-list compact">
        <li>Mostrá este QR al personal cuando llegás o cuando querés canjear un beneficio.</li>
        <li>El equipo del bar registra tu visita y valida tus beneficios activos.</li>
        <li>Todo lo que se canjea queda guardado en tu historial.</li>
    </ol>
    <div class="bottom-cta-wrap">
        <a class="btn btn-secondary btn-wide" href="/public/history.php">Ver historial</a>
    </div>
</section>

<nav class="bottom-nav">
    <a href="/public/dashboard.php">Inicio</a>
    <a href="/public/benefits.php">Beneficios</a>
    <a href="/public/qr.php" class="active">Mi Pase Evita</a>
    <a href="/public/logout.php">Salir</a>
</nav>
<?php require __DIR__ . '/../includes/footer.php'; ?>
