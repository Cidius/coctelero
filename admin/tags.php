<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/RecipeAdmin.php';

use App\Auth;
use App\RecipeAdmin;

use function App\e;
use function App\url;

Auth::requireLogin();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    $checked = array_map('intval', array_keys($_POST['spirit'] ?? []));
    foreach (RecipeAdmin::allTagsWithUsage() as $t) {
        $shouldBeSpirit = in_array((int) $t['id'], $checked, true);
        if ((bool) $t['is_spirit'] !== $shouldBeSpirit) {
            RecipeAdmin::setTagSpirit((int) $t['id'], $shouldBeSpirit);
        }
    }
    $msg = 'Cambios guardados.';
}

$tags = RecipeAdmin::allTagsWithUsage();

admin_header('Tags');
?>
<div class="page-head">
    <h1>Tags</h1>
</div>

<?php if ($msg !== ''): ?><p class="alert ok"><?= e($msg) ?></p><?php endif; ?>

<p class="muted small">
    Tildá los tags que son un destilado o licor: en el listado de recetas
    esos son los únicos que se muestran en la línea de bebidas de cada
    tarjeta (en mobile es lo único que se ve de las etiquetas, no entran
    las demás). El resto siguen siendo etiquetas normales.
</p>

<form method="post" action="<?= e(url('admin/tags.php')) ?>">
    <?= Auth::csrfField() ?>
    <table class="list list-tags">
        <thead>
            <tr><th>Tag</th><th>Usos</th><th>Destilado/licor</th></tr>
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
                </tr>
            <?php endforeach; ?>
            <?php if ($tags === []): ?>
                <tr><td colspan="3" class="muted">Todavía no hay tags cargados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="form-actions">
        <button type="submit" class="btn primary">Guardar</button>
    </div>
</form>

<?php admin_footer(); ?>
