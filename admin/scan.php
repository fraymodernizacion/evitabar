<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$staff = require_admin_login(['staff', 'admin']);
verify_csrf();

$user = null;
$token = extractToken((string) ($_GET['token'] ?? $_POST['token'] ?? ''));
$manualDni = trim((string) ($_POST['manual_dni'] ?? ''));

if ($token !== '') {
    $user = getUserByToken($token);
}

if (!$user && $manualDni !== '') {
    $stmt = db()->prepare("SELECT * FROM users WHERE dni = :dni AND role = 'client' LIMIT 1");
    $stmt->execute(['dni' => $manualDni]);
    $user = $stmt->fetch() ?: null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action']) && $user) {
    $action = (string) $_POST['action'];

    if ($action === 'register_visit') {
        $force = isset($_POST['force']) && $_POST['force'] === '1';
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $result = registerVisit((int) $user['id'], (int) $staff['id'], $force, $notes);
        flash($result['ok'] ? 'success' : 'error', $result['message']);
    }

    if ($action === 'redeem_benefit') {
        $benefitId = (int) ($_POST['benefit_id'] ?? 0);
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $result = redeemBenefit((int) $user['id'], $benefitId, (int) $staff['id'], $notes);
        flash($result['ok'] ? 'success' : 'error', $result['message']);
    }

    header('Location: /admin/scan.php?token=' . urlencode((string) $user['qr_token']));
    exit;
}

$availableBenefits = $user ? getUserActiveBenefits((int) $user['level']) : [];
$visits = $user ? userRecentHistory((int) $user['id']) : [];
$redemptions = $user ? userRedemptionHistory((int) $user['id']) : [];

$pageTitle = 'Escanear QR | Admin';
require __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Escaneo de QR</h2>
    <p class="muted">Permití cámara o ingresá token/DNI manualmente.</p>

    <div class="scanner-wrap">
        <video id="scanner-video" playsinline muted></video>
        <p id="scanner-status" class="muted">Esperando inicio del escáner...</p>
    </div>

    <form method="get" class="form-grid">
        <label>Token QR
            <input type="text" name="token" value="<?= e($token) ?>" placeholder="Pegá token o URL completa">
        </label>
        <button class="btn btn-secondary" type="submit">Buscar por token</button>
    </form>

    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <label>DNI manual
            <input type="text" name="manual_dni" placeholder="Ej: 30111222">
        </label>
        <button class="btn btn-secondary" type="submit">Buscar por DNI</button>
    </form>
</section>

<?php if ($user): ?>
<section class="card">
    <h3>Cliente detectado</h3>
    <p><strong><?= e($user['name']) ?></strong></p>
    <p class="muted">Nivel <?= (int) $user['level'] ?> · Visitas <?= (int) $user['visits_count'] ?></p>
</section>

<section class="card action-grid">
    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($user['qr_token']) ?>">
        <input type="hidden" name="action" value="register_visit">
        <label>Observación
            <textarea name="notes" rows="2"></textarea>
        </label>
        <label class="checkbox-row"><input type="checkbox" name="force" value="1"> Forzar si bloquea por 2 horas</label>
        <button class="btn btn-primary" type="submit">Registrar visita</button>
    </form>

    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($user['qr_token']) ?>">
        <input type="hidden" name="action" value="redeem_benefit">
        <label>Beneficio
            <select name="benefit_id" required>
                <option value="">Seleccionar</option>
                <?php foreach ($availableBenefits as $benefit): ?>
                    <option value="<?= (int) $benefit['id'] ?>"><?= e($benefit['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Observación
            <textarea name="notes" rows="2"></textarea>
        </label>
        <button class="btn btn-secondary" type="submit">Canjear beneficio</button>
    </form>
</section>

<section class="card">
    <h3>Historial reciente</h3>
    <?php if (empty($visits)): ?>
        <p class="muted">Sin visitas registradas.</p>
    <?php else: ?>
        <ul class="timeline">
            <?php foreach ($visits as $visit): ?>
                <li>
                    <strong><?= e($visit['visit_date']) ?></strong>
                    <p><?= e($visit['notes'] ?? 'Sin observaciones') ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="card">
    <h3>Historial de canjes</h3>
    <?php if (empty($redemptions)): ?>
        <p class="muted">Sin canjes registrados.</p>
    <?php else: ?>
        <ul class="timeline">
            <?php foreach ($redemptions as $redemption): ?>
                <li>
                    <strong><?= e($redemption['redeemed_at']) ?> · <?= e($redemption['title']) ?></strong>
                    <p><?= e($redemption['notes'] ?: 'Sin observaciones') ?></p>
                    <?php if (!empty($redemption['staff_name'])): ?>
                        <small>Registrado por <?= e($redemption['staff_name']) ?></small>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php elseif ($token !== '' || $manualDni !== ''): ?>
<section class="card"><p class="muted">No se encontró cliente para ese dato.</p></section>
<?php endif; ?>

<section class="card action-grid">
    <a class="btn btn-secondary" href="/admin/index.php">Volver al dashboard</a>
    <a class="btn btn-ghost" href="/admin/logout.php">Salir</a>
</section>

<script src="<?= e(asset_url('/lib/scanner/barcode-scanner.js')) ?>"></script>
<script src="<?= e(asset_url('/public/assets/js/scan.js')) ?>" defer></script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
