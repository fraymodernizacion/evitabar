<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

$admin = require_admin_login(['admin']);
verify_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'create' || $action === 'update') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $requiredLevel = (int) ($_POST['required_level'] ?? 1);
        $conditions = trim((string) ($_POST['conditions'] ?? ''));
        $active = isset($_POST['active']) ? 1 : 0;
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);

        if ($title === '' || $description === '') {
            flash('error', 'Título y descripción son obligatorios.');
            header('Location: /admin/benefits.php');
            exit;
        }

        if ($action === 'create') {
            $stmt = db()->prepare('INSERT INTO benefits (title, description, required_level, conditions, active, sort_order, created_at, updated_at) VALUES (:title, :description, :required_level, :conditions, :active, :sort_order, NOW(), NOW())');
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'required_level' => $requiredLevel,
                'conditions' => $conditions,
                'active' => $active,
                'sort_order' => $sortOrder,
            ]);
            flash('success', 'Beneficio creado.');
        }

        if ($action === 'update' && $id > 0) {
            $stmt = db()->prepare('UPDATE benefits SET title = :title, description = :description, required_level = :required_level, conditions = :conditions, active = :active, sort_order = :sort_order, updated_at = NOW() WHERE id = :id');
            $stmt->execute([
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'required_level' => $requiredLevel,
                'conditions' => $conditions,
                'active' => $active,
                'sort_order' => $sortOrder,
            ]);
            flash('success', 'Beneficio actualizado.');
        }
    }

    if ($action === 'toggle' && !empty($_POST['id'])) {
        $id = (int) $_POST['id'];
        db()->prepare('UPDATE benefits SET active = IF(active = 1, 0, 1), updated_at = NOW() WHERE id = :id')->execute(['id' => $id]);
        flash('success', 'Estado de beneficio actualizado.');
    }

    header('Location: /admin/benefits.php');
    exit;
}

$editing = null;
if (!empty($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM benefits WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

$list = db()->query('SELECT * FROM benefits ORDER BY required_level ASC, sort_order ASC, id ASC')->fetchAll();

$pageTitle = 'ABM Beneficios | Admin';
require __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2><?= $editing ? 'Editar beneficio' : 'Nuevo beneficio' ?></h2>

    <form method="post" class="form-grid">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
        <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $editing['id'] ?>"><?php endif; ?>

        <label>Título
            <input type="text" name="title" required value="<?= e((string) ($editing['title'] ?? '')) ?>">
        </label>
        <label>Descripción
            <textarea name="description" rows="3" required><?= e((string) ($editing['description'] ?? '')) ?></textarea>
        </label>
        <label>Nivel requerido
            <select name="required_level">
                <option value="1" <?= ((int) ($editing['required_level'] ?? 1) === 1) ? 'selected' : '' ?>>Nivel 1</option>
                <option value="2" <?= ((int) ($editing['required_level'] ?? 1) === 2) ? 'selected' : '' ?>>Nivel 2</option>
                <option value="3" <?= ((int) ($editing['required_level'] ?? 1) === 3) ? 'selected' : '' ?>>Nivel 3</option>
            </select>
        </label>
        <label>Condiciones
            <textarea name="conditions" rows="2"><?= e((string) ($editing['conditions'] ?? '')) ?></textarea>
        </label>
        <label>Orden
            <input type="number" name="sort_order" value="<?= (int) ($editing['sort_order'] ?? 10) ?>">
        </label>
        <label class="checkbox-row"><input type="checkbox" name="active" value="1" <?= (!isset($editing['active']) || (int) $editing['active'] === 1) ? 'checked' : '' ?>> Activo</label>
        <button class="btn btn-primary" type="submit"><?= $editing ? 'Guardar cambios' : 'Crear beneficio' ?></button>
    </form>
</section>

<section class="card">
    <h3>Listado de beneficios</h3>
    <ul class="result-list">
        <?php foreach ($list as $item): ?>
            <li>
                <div>
                    <strong><?= e($item['title']) ?></strong>
                    <span>Nivel <?= (int) $item['required_level'] ?> · Orden <?= (int) $item['sort_order'] ?> · <?= (int) $item['active'] === 1 ? 'Activo' : 'Inactivo' ?></span>
                </div>
                <div class="inline-actions">
                    <a class="btn btn-secondary btn-small" href="/admin/benefits.php?edit=<?= (int) $item['id'] ?>">Editar</a>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                        <button class="btn btn-ghost btn-small" type="submit">Activar/Desactivar</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<section class="card action-grid">
    <a class="btn btn-secondary" href="/admin/index.php">Volver al dashboard</a>
    <a class="btn btn-ghost" href="/admin/logout.php">Salir</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
