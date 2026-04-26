<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if (current_user()) {
    header('Location: /public/index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $identifier = trim((string) ($_POST['identifier'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (attempt_login($identifier, $password, ['client'])) {
        $user = current_user();
        if ($user) {
            maybeApplyQuarterlyMaintenance((int) $user['id']);
        }
        flash('success', 'Bienvenido a Pase Evita.');
        header('Location: /public/dashboard.php');
        exit;
    }

    $error = 'Credenciales inválidas.';
}

$pageTitle = 'Ingresar | Pase Evita';
require __DIR__ . '/../includes/header.php';
?>
<section class="card auth-card">
    <h2>Ingresá a tu Pase Evita</h2>
    <p class="muted">Usá tu email o DNI.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <label>Email o DNI
            <input type="text" name="identifier" required>
        </label>
        <label>Contraseña
            <div class="password-field">
                <input type="password" name="password" required autocomplete="current-password" data-password-input>
                <button type="button" class="password-toggle" data-password-toggle aria-label="Mostrar contraseña">👁</button>
            </div>
        </label>
        <button class="btn btn-primary" type="submit">Entrar</button>
    </form>

    <p class="auth-switch">¿No tenés cuenta? <a href="/public/register.php">Registrate</a></p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
