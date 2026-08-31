<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/RecipeAdmin.php';

use App\Auth;
use App\RecipeAdmin;

use function App\e;
use function App\url;

Auth::requireLogin();

$recipes = RecipeAdmin::listAll(true);
$flash = flash_take();

admin_header('Papelera');
?>
<div class="page-head">
    <h1>Papelera <span class="muted">(<?= count($recipes) ?>)</span></h1>
    <a class="btn" href="<?= e(url('admin/dashboard.php')) ?>">← Volver</a>
</div>

<?php if ($flash): ?>
    <p class="alert <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></p>
<?php endif; ?>

<p class="muted">Las recetas borradas no aparecen en el sitio. Restaurar las vuelve a publicar.</p>

<table class="list">
    <thead><tr><th>Nombre</th><th>Borrada</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($recipes as $r): ?>
        <tr>
            <td><?= e($r['name']) ?><div class="muted small"><?= e($r['slug']) ?></div></td>
            <td class="muted small"><?= e(substr((string) $r['deleted_at'], 0, 16)) ?></td>
            <td class="actions">
                <form method="post" action="<?= e(url('admin/receta-restore.php')) ?>">
                    <?= Auth::csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                    <button type="submit" class="link">Restaurar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if ($recipes === []): ?>
        <tr><td colspan="3" class="muted">La papelera está vacía.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php
admin_footer();
