<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if (current_user() && in_array((string) $_SESSION['role'], ['staff', 'admin'], true)) {
    header('Location: /admin/index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $identifier = trim((string) ($_POST['identifier'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (attempt_login($identifier, $password, ['staff', 'admin'])) {
        flash('success', 'Bienvenido al panel de Evita Bar.');
        header('Location: /admin/index.php');
        exit;
    }

    $error = 'Credenciales inválidas para staff/admin.';
}

$pageTitle = 'Login Admin | Pase Evita';
require __DIR__ . '/../includes/header.php';
?>
<section class="card auth-card">
    <h2>Ingreso de personal</h2>
    <p class="muted">Solo staff y admin.</p>

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
        <button class="btn btn-primary" type="submit">Entrar al panel</button>
    </form>
    <p class="auth-switch"><a href="/public/login.php">Ir a login de clientes</a></p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
