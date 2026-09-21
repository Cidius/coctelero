<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/Glassware.php';

use App\Auth;
use App\Glassware;

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
            Glassware::create((string) ($_POST['name'] ?? ''));
            $msg = 'Cristalería agregada.';
        } elseif ($action === 'rename') {
            Glassware::rename((int) ($_POST['id'] ?? 0), (string) ($_POST['name'] ?? ''));
            $msg = 'Cambios guardados.';
        } elseif ($action === 'delete') {
            if (Glassware::delete((int) ($_POST['id'] ?? 0))) {
                $msg = 'Cristalería eliminada.';
            } else {
                $errors[] = 'No se puede borrar: hay recetas que la usan.';
            }
        }
    } catch (\InvalidArgumentException $ex) {
        $errors[] = $ex->getMessage();
    }
}

$editId = (int) ($_GET['edit'] ?? 0);
$items = Glassware::allWithUsage();

admin_header('Cristalería');
?>
<div class="page-head">
    <h1>Cristalería</h1>
</div>

<?php foreach ($errors as $err): ?><p class="alert error"><?= e($err) ?></p><?php endforeach; ?>
<?php if ($msg !== ''): ?><p class="alert ok"><?= e($msg) ?></p><?php endif; ?>

<form method="post" action="<?= e(url('admin/cristaleria.php')) ?>" class="inline-form">
    <?= Auth::csrfField() ?>
    <input type="hidden" name="action" value="create">
    <label class="field">
        <span>Nueva cristalería</span>
        <input type="text" name="name" placeholder="Ej: Copa Nick and Nora" required maxlength="120">
    </label>
    <button type="submit" class="btn primary">Agregar</button>
</form>

<table class="list list-glassware">
    <thead><tr><th>Nombre</th><th>Usos</th><th class="actions">Acciones</th></tr></thead>
    <tbody>
        <?php foreach ($items as $g): ?>
            <?php if ($editId === (int) $g['id']): ?>
                <tr>
                    <td colspan="3">
                        <form method="post" action="<?= e(url('admin/cristaleria.php')) ?>" class="inline-form">
                            <?= Auth::csrfField() ?>
                            <input type="hidden" name="action" value="rename">
                            <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                            <input type="text" name="name" value="<?= e($g['name']) ?>" required maxlength="120" autofocus>
                            <button type="submit" class="btn primary">Guardar</button>
                            <a class="btn" href="<?= e(url('admin/cristaleria.php')) ?>">Cancelar</a>
                        </form>
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td><?= e($g['name']) ?></td>
                    <td class="muted"><?= (int) $g['uses'] ?></td>
                    <td class="actions">
                        <a href="<?= e(url('admin/cristaleria.php?edit=' . (int) $g['id'])) ?>">Editar</a>
                        <?php if ((int) $g['uses'] === 0): ?>
                            <form method="post" action="<?= e(url('admin/cristaleria.php')) ?>"
                                  onsubmit="return confirm('¿Borrar esta cristalería?');">
                                <?= Auth::csrfField() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                                <button type="submit" class="link danger">Borrar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($items === []): ?>
            <tr><td colspan="3" class="muted">Todavía no hay cristalerías cargadas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php admin_footer(); ?>
