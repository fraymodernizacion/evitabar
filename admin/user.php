<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$staff = require_admin_login(['staff', 'admin']);
verify_csrf();

$query = trim((string) ($_GET['q'] ?? ''));
$users = $query !== '' ? findUsers($query) : [];
$selectedUser = null;

if (!empty($_GET['id'])) {
    $selectedUser = getUserById((int) $_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action']) && !empty($_POST['user_id'])) {
    $userId = (int) $_POST['user_id'];
    $action = (string) $_POST['action'];

    if ($action === 'register_visit') {
        $force = isset($_POST['force']) && $_POST['force'] === '1';
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $result = registerVisit($userId, (int) $staff['id'], $force, $notes);
        flash($result['ok'] ? 'success' : 'error', $result['message']);
    }

    if ($action === 'redeem_benefit') {
        $benefitId = (int) ($_POST['benefit_id'] ?? 0);
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $result = redeemBenefit($userId, $benefitId, (int) $staff['id'], $notes);
        flash($result['ok'] ? 'success' : 'error', $result['message']);
    }

    header('Location: /admin/user.php?id=' . $userId);
    exit;
}

if ($selectedUser) {
    maybeApplyQuarterlyMaintenance((int) $selectedUser['id']);
    $selectedUser = getUserById((int) $selectedUser['id']);
}

$availableBenefits = $selectedUser ? getUserActiveBenefits((int) $selectedUser['level']) : [];
$visits = $selectedUser ? userRecentHistory((int) $selectedUser['id']) : [];
$redemptions = $selectedUser ? userRedemptionHistory((int) $selectedUser['id']) : [];

$pageTitle = 'Buscar clientes | Admin';
require __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Buscar cliente</h2>
    <form method="get" class="search-row">
        <input type="text" name="q" value="<?= e($query) ?>" placeholder="Nombre, DNI, teléfono o email">
        <button class="btn btn-primary" type="submit">Buscar</button>
    </form>
</section>

<?php if ($query !== ''): ?>
<section class="card">
    <h3>Resultados</h3>
    <?php if (empty($users)): ?>
        <p class="muted">No se encontraron clientes.</p>
    <?php else: ?>
        <ul class="result-list">
            <?php foreach ($users as $u): ?>
                <li>
                    <a href="/admin/user.php?id=<?= (int) $u['id'] ?>">
                        <strong><?= e($u['name']) ?></strong>
                        <span><?= e($u['dni']) ?> · Nivel <?= (int) $u['level'] ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if ($selectedUser): ?>
<section class="card">
    <h3><?= e($selectedUser['name']) ?></h3>
    <p class="muted">DNI <?= e($selectedUser['dni']) ?> · <?= e($selectedUser['phone']) ?></p>
    <p><strong>Nivel <?= (int) $selectedUser['level'] ?></strong> · Visitas acumuladas: <?= (int) $selectedUser['visits_count'] ?></p>
</section>

<section class="card action-grid">
    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="user_id" value="<?= (int) $selectedUser['id'] ?>">
        <input type="hidden" name="action" value="register_visit">
        <label>Observación opcional
            <textarea name="notes" rows="2" placeholder="Ej: visita de grupo"></textarea>
        </label>
        <label class="checkbox-row"><input type="checkbox" name="force" value="1"> Forzar carga si está en ventana de bloqueo</label>
        <button class="btn btn-primary" type="submit">Registrar visita</button>
    </form>

    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="user_id" value="<?= (int) $selectedUser['id'] ?>">
        <input type="hidden" name="action" value="redeem_benefit">
        <label>Canjear beneficio
            <select name="benefit_id" required>
                <option value="">Seleccionar</option>
                <?php foreach ($availableBenefits as $benefit): ?>
                    <option value="<?= (int) $benefit['id'] ?>"><?= e($benefit['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Observación opcional
            <textarea name="notes" rows="2"></textarea>
        </label>
        <button class="btn btn-secondary" type="submit">Canjear beneficio</button>
    </form>
</section>

<section class="card">
    <h3>Historial de visitas</h3>
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
                    <p><?= e($redemption['notes'] ?? 'Sin observaciones') ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php endif; ?>

<section class="card action-grid">
    <a class="btn btn-secondary" href="/admin/index.php">Volver al dashboard</a>
    <a class="btn btn-ghost" href="/admin/logout.php">Salir</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
