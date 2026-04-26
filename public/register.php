<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if (current_user()) {
    header('Location: /public/dashboard.php');
    exit;
}

$errors = [];
$old = [
    'first_name' => '',
    'last_name' => '',
    'dni' => '',
    'phone' => '',
    'birthdate' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $old['first_name'] = normalize_person_name((string) ($_POST['first_name'] ?? ''));
    $old['last_name'] = normalize_person_name((string) ($_POST['last_name'] ?? ''));
    $old['dni'] = trim((string) ($_POST['dni'] ?? ''));
    $phoneInput = trim((string) ($_POST['phone'] ?? ''));
    $old['phone'] = $phoneInput;
    $old['birthdate'] = trim((string) ($_POST['birthdate'] ?? ''));
    $old['email'] = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirmation'] ?? '');

    if ($old['first_name'] === '' || !preg_match('/^[\p{L}][\p{L}\p{M}\' -]{1,49}$/u', $old['first_name'])) {
        $errors[] = 'Ingresá un nombre válido.';
    }

    if ($old['last_name'] === '' || !preg_match('/^[\p{L}][\p{L}\p{M}\' -]{1,49}$/u', $old['last_name'])) {
        $errors[] = 'Ingresá un apellido válido.';
    }

    if (!preg_match('/^[0-9]{7,10}$/', $old['dni'])) {
        $errors[] = 'El DNI debe tener entre 7 y 10 dígitos.';
    }

    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ingresá un email válido.';
    }

    if ($phoneInput === '' || !preg_match('/^[0-9+\s()-]{7,20}$/', $phoneInput)) {
        $errors[] = 'Ingresá un teléfono válido.';
    } else {
        $old['phone'] = sanitize_phone($phoneInput);
        if ($old['phone'] === '' || mb_strlen($old['phone']) < 8) {
            $errors[] = 'Ingresá un teléfono válido.';
        }
    }

    if ($old['birthdate'] === '') {
        $errors[] = 'Ingresá tu fecha de nacimiento.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (empty($errors)) {
        $check = db()->prepare('SELECT id FROM users WHERE dni = :dni OR email = :email OR phone = :phone LIMIT 1');
        $check->execute([
            'dni' => $old['dni'],
            'email' => $old['email'],
            'phone' => $old['phone'],
        ]);

        if ($check->fetch()) {
            $errors[] = 'Ya existe una cuenta con ese DNI, email o teléfono.';
        }
    }

    if (empty($errors)) {
        $fullName = normalize_person_name($old['first_name'] . ' ' . $old['last_name']);
        $insert = db()->prepare('INSERT INTO users (name, dni, phone, birthdate, email, password_hash, role, level, visits_count, qr_token, created_at, updated_at) VALUES (:name, :dni, :phone, :birthdate, :email, :password_hash, :role, :level, 0, :qr_token, NOW(), NOW())');
        $insert->execute([
            'name' => $fullName,
            'dni' => $old['dni'],
            'phone' => $old['phone'],
            'birthdate' => $old['birthdate'],
            'email' => $old['email'],
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'client',
            'level' => 1,
            'qr_token' => random_token(64),
        ]);

        flash('success', 'Cuenta creada con éxito. Ya podés iniciar sesión.');
        header('Location: /public/login.php');
        exit;
    }
}

$pageTitle = 'Registro | Pase Evita';
require __DIR__ . '/../includes/header.php';
?>
<section class="card auth-card">
    <h2>Sumate a Pase Evita</h2>
    <p class="muted">Tu membresía digital para beneficios en Evita Bar.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?= e($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <label>Nombre
            <input type="text" name="first_name" required autocomplete="given-name" inputmode="text" value="<?= e($old['first_name']) ?>">
        </label>
        <label>Apellido
            <input type="text" name="last_name" required autocomplete="family-name" inputmode="text" value="<?= e($old['last_name']) ?>">
        </label>
        <label>DNI
            <input type="text" name="dni" required autocomplete="off" inputmode="numeric" pattern="[0-9]{7,10}" maxlength="10" value="<?= e($old['dni']) ?>">
        </label>
        <label>Teléfono
            <input type="tel" name="phone" required autocomplete="tel" inputmode="numeric" pattern="[0-9+\\s()-]{7,20}" maxlength="20" value="<?= e($old['phone']) ?>">
        </label>
        <label>Fecha de nacimiento
            <input type="date" name="birthdate" required autocomplete="bday" value="<?= e($old['birthdate']) ?>">
        </label>
        <label>Email
            <input type="email" name="email" required autocomplete="email" value="<?= e($old['email']) ?>">
        </label>
        <label>Contraseña
            <div class="password-field">
                <input type="password" name="password" required autocomplete="new-password" data-password-input>
                <button type="button" class="password-toggle" data-password-toggle aria-label="Mostrar contraseña">👁</button>
            </div>
        </label>
        <label>Confirmá contraseña
            <div class="password-field">
                <input type="password" name="password_confirmation" required autocomplete="new-password" data-password-input>
                <button type="button" class="password-toggle" data-password-toggle aria-label="Mostrar contraseña">👁</button>
            </div>
        </label>
        <button class="btn btn-primary" type="submit">Crear mi pase</button>
    </form>

    <p class="auth-switch">¿Ya tenés cuenta? <a href="/public/login.php">Iniciar sesión</a></p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
