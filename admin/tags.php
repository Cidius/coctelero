<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/RecipeAdmin.php';

use App\Auth;
use App\RecipeAdmin;

use function App\e;
use function App\url;

Auth::requireLogin();

$errors = [];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'create') {
            RecipeAdmin::createTag((string) ($_POST['name'] ?? ''));
            $msg = 'Tag agregado.';
        } elseif ($action === 'rename') {
            RecipeAdmin::renameTag((int) ($_POST['id'] ?? 0), (string) ($_POST['name'] ?? ''));
            $msg = 'Cambios guardados.';
        } elseif ($action === 'delete') {
            if (RecipeAdmin::deleteTag((int) ($_POST['id'] ?? 0))) {
                $msg = 'Tag eliminado.';
            } else {
                $errors[] = 'No se puede borrar: hay recetas que lo usan.';
            }
        } elseif ($action === 'save_spirit') {
            $checked = array_map('intval', array_keys($_POST['spirit'] ?? []));
            foreach (RecipeAdmin::allTagsWithUsage() as $t) {
                $shouldBeSpirit = in_array((int) $t['id'], $checked, true);
                if ((bool) $t['is_spirit'] !== $shouldBeSpirit) {
                    RecipeAdmin::setTagSpirit((int) $t['id'], $shouldBeSpirit);
                }
            }
            $msg = 'Cambios guardados.';
        }
    } catch (\InvalidArgumentException $ex) {
        $errors[] = $ex->getMessage();
    }
}

$tags = RecipeAdmin::allTagsWithUsage();

$editId = (int) ($_GET['edit'] ?? 0);
$editingTag = null;
foreach ($tags as $t) {
    if ((int) $t['id'] === $editId) {
        $editingTag = $t;
        break;
    }
}

admin_header('Tags');
?>
<div class="page-head">
    <h1>Tags</h1>
</div>

<?php foreach ($errors as $err): ?><p class="alert error"><?= e($err) ?></p><?php endforeach; ?>
<?php if ($msg !== ''): ?><p class="alert ok"><?= e($msg) ?></p><?php endif; ?>

<p class="muted small">
    Tildá los tags que son un destilado o licor: en el listado de recetas
    esos son los únicos que se muestran en la línea de bebidas de cada
    tarjeta (en mobile es lo único que se ve de las etiquetas, no entran
    las demás). El resto siguen siendo etiquetas normales.
</p>

<?php if ($editingTag !== null): ?>
    <form method="post" action="<?= e(url('admin/tags.php')) ?>" class="inline-form">
        <?= Auth::csrfField() ?>
        <input type="hidden" name="id" value="<?= (int) $editingTag['id'] ?>">
        <label class="field">
            <span>Renombrar tag</span>
            <input type="text" name="name" value="<?= e($editingTag['name']) ?>" required maxlength="60" autofocus>
        </label>
        <button type="submit" name="action" value="rename" class="btn primary">Guardar</button>
        <a class="btn" href="<?= e(url('admin/tags.php')) ?>">Cancelar</a>
        <?php if ((int) $editingTag['uses'] === 0): ?>
            <button type="submit" name="action" value="delete" formnovalidate class="link danger"
                    onclick="return confirm('¿Borrar este tag?');">Borrar</button>
        <?php endif; ?>
    </form>
<?php else: ?>
    <form method="post" action="<?= e(url('admin/tags.php')) ?>" class="inline-form">
        <?= Auth::csrfField() ?>
        <label class="field">
            <span>Nuevo tag</span>
            <input type="text" name="name" placeholder="Ej: Limoncello" required maxlength="60">
        </label>
        <button type="submit" name="action" value="create" class="btn primary">Agregar</button>
    </form>
<?php endif; ?>

<form method="post" action="<?= e(url('admin/tags.php')) ?>">
    <?= Auth::csrfField() ?>
    <table class="list list-tags">
        <thead>
            <tr><th>Tag</th><th>Usos</th><th>Destilado/licor</th><th class="actions">Acciones</th></tr>
        </thead>
        <tbody>
            <?php foreach ($tags as $t): ?>
                <tr>
                    <td><?= e($t['name']) ?></td>
                    <td class="muted"><?= (int) $t['uses'] ?></td>
                    <td>
                        <label class="inline">
                            <input type="checkbox" name="spirit[<?= (int) $t['id'] ?>]" value="1"
                                   <?= $t['is_spirit'] ? 'checked' : '' ?>>
                        </label>
                    </td>
                    <td class="actions">
                        <a href="<?= e(url('admin/tags.php?edit=' . (int) $t['id'])) ?>">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($tags === []): ?>
                <tr><td colspan="4" class="muted">Todavía no hay tags cargados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="form-actions">
        <button type="submit" name="action" value="save_spirit" class="btn primary">Guardar destilado/licor</button>
    </div>
</form>

<?php admin_footer(); ?>
