<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../lib/qrcode/EmbeddedQr.php';

$user = require_login(['client']);
$scanUrl = scanUrlFromToken($user['qr_token']);
$qrDataUri = EmbeddedQr::asDataUri($scanUrl, 320);
$qrRemoteUrl = EmbeddedQr::remoteUrl($scanUrl, 320);
$activeBenefits = getUserActiveBenefits((int) $user['level']);

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
                <strong><?= e($benefit['title']) ?></strong>
                <p><?= e($benefit['conditions']) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<nav class="bottom-nav">
    <a href="/public/dashboard.php">Inicio</a>
    <a href="/public/benefits.php">Beneficios</a>
    <a href="/public/qr.php" class="active">Mi Pase Evita</a>
    <a href="/public/logout.php">Salir</a>
</nav>
<?php require __DIR__ . '/../includes/footer.php'; ?>
