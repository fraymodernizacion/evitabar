<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$user = require_login(['client']);
$grouped = activeBenefitsByLevel();
$redeemedBenefitIds = userRedeemedBenefitIds((int) $user['id']);

$pageTitle = 'Beneficios | Pase Evita';
require __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Beneficios disponibles</h2>
    <p class="muted">Tu nivel actual: <strong>Nivel <?= (int) $user['level'] ?></strong></p>
</section>

<section class="card">
    <h3>Cómo usar tu pase</h3>
    <ol class="steps-list compact">
        <li>Mostrá tu QR al personal para registrar tu visita o canjear beneficios.</li>
        <li>El mozo verifica qué beneficios te corresponden según tu nivel.</li>
        <li>Todo queda guardado y lo ves en tu historial.</li>
    </ol>
    <div class="bottom-cta-wrap">
        <a class="btn btn-secondary btn-wide" href="/public/history.php">Ver historial</a>
    </div>
</section>

<?php foreach ([1, 2, 3] as $level): ?>
    <section class="card">
        <h3>Nivel <?= $level ?></h3>
        <?php if (empty($grouped[$level])): ?>
            <p class="muted">Sin beneficios cargados para este nivel.</p>
        <?php else: ?>
            <ul class="benefit-list">
                <?php foreach ($grouped[$level] as $benefit): ?>
                    <?php $isRedeemed = !empty($redeemedBenefitIds[(int) $benefit['id']]); ?>
                    <li class="<?= (int) $user['level'] >= $level ? 'is-active-benefit' : 'is-locked-benefit' ?><?= $isRedeemed ? ' is-redeemed-benefit' : '' ?>">
                        <div class="benefit-line">
                            <strong><?= e($benefit['title']) ?></strong>
                            <?php if ($isRedeemed): ?>
                                <span class="badge badge-small badge-redeemed">Ya canjeado</span>
                            <?php elseif ((int) $user['level'] >= $level): ?>
                                <span class="badge badge-small">Activo</span>
                            <?php endif; ?>
                        </div>
                        <p><?= e($benefit['description']) ?></p>
                        <?php if (!empty($benefit['conditions'])): ?>
                            <small><?= e($benefit['conditions']) ?></small>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
<?php endforeach; ?>

<nav class="bottom-nav">
    <a href="/public/dashboard.php">Inicio</a>
    <a href="/public/benefits.php" class="active">Beneficios</a>
    <a href="/public/qr.php">Mi Pase Evita</a>
    <a href="/public/logout.php">Salir</a>
</nav>
<?php require __DIR__ . '/../includes/footer.php'; ?>
