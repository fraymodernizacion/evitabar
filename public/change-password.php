<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$user = require_login(['client']);

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $newPassword = (string) ($_POST['new_password'] ?? '');
    $newPasswordConfirm = (string) ($_POST['new_password_confirmation'] ?? '');

    if (strlen($newPassword) < 8) {
        $errors[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
    }

    if ($newPassword !== $newPasswordConfirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (empty($errors)) {
        updateUserPassword((int) $user['id'], $newPassword);
        flash('success', 'Tu clave fue actualizada con éxito.');
        header('Location: /public/dashboard.php');
        exit;
    }
}

$pageTitle = 'Cambiar clave | Pase Evita';
require __DIR__ . '/../includes/header.php';
?>
<section class="card auth-card">
    <h2>Creá tu nueva clave</h2>
    <p class="muted">
        <?= userRequiresPasswordChange((int) $user['id']) ? 'Tu clave fue restablecida por un administrador. Definí una nueva para seguir.' : 'Podés actualizar tu clave cuando quieras.' ?>
    </p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= e($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <label>Nueva contraseña
            <div class="password-field">
                <input type="password" name="new_password" required autocomplete="new-password" data-password-input>
                <button type="button" class="password-toggle" data-password-toggle aria-label="Mostrar contraseña">👁</button>
            </div>
        </label>
        <label>Confirmá la contraseña
            <div class="password-field">
                <input type="password" name="new_password_confirmation" required autocomplete="new-password" data-password-input>
                <button type="button" class="password-toggle" data-password-toggle aria-label="Mostrar contraseña">👁</button>
            </div>
        </label>
        <button class="btn btn-primary" type="submit">Guardar nueva clave</button>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
