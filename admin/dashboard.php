<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/RecipeAdmin.php';

use App\Auth;
use App\RecipeAdmin;

use function App\e;
use function App\recipe_image_url;
use function App\url;

Auth::requireLogin();

$recipes = RecipeAdmin::listAll(false);
$trashed = RecipeAdmin::trashedCount();
$flash = flash_take();

admin_header('Recetas');
?>
<div class="page-head">
    <h1>Recetas <span class="muted">(<?= count($recipes) ?>)</span></h1>
    <a class="btn primary" href="<?= e(url('admin/receta-form.php')) ?>">+ Nueva receta</a>
</div>

<?php if ($flash): ?>
    <p class="alert <?= e($flash['type']) ?>"><?= e($flash['msg']) ?></p>
<?php endif; ?>

<table class="list">
    <thead>
        <tr><th></th><th>Nombre</th><th>Método</th><th>Ingr.</th><th>Tags</th><th>Vistas</th><th>Actualizada</th><th></th></tr>
    </thead>
    <tbody>
    <?php foreach ($recipes as $r): ?>
        <?php $img = recipe_image_url($r['image_path']); ?>
        <tr>
            <td class="thumb-cell">
                <?php if ($img): ?><img src="<?= e($img) ?>" alt=""><?php else: ?><span class="noimg">—</span><?php endif; ?>
            </td>
            <td>
                <a href="<?= e(url('admin/receta-form.php?id=' . (int) $r['id'])) ?>"><?= e($r['name']) ?></a>
                <div class="muted small"><?= e($r['slug']) ?></div>
            </td>
            <td><?= e($r['method']) ?></td>
            <td><?= (int) $r['ingredients'] ?></td>
            <td><?= (int) $r['tags'] ?></td>
            <td><?= number_format((int) $r['views'], 0, ',', '.') ?></td>
            <td class="muted small"><?= e(substr((string) $r['updated_at'], 0, 16)) ?></td>
            <td class="actions">
                <a href="<?= e(url('admin/receta-form.php?id=' . (int) $r['id'])) ?>">Editar</a>
                <form method="post" action="<?= e(url('admin/receta-delete.php')) ?>"
                      onsubmit="return confirm('¿Mandar «<?= e($r['name']) ?>» a la papelera?');">
                    <?= Auth::csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                    <button type="submit" class="link danger">Borrar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if ($recipes === []): ?>
        <tr><td colspan="8" class="muted">No hay recetas todavía.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php if ($trashed > 0): ?>
    <p class="muted"><a href="<?= e(url('admin/papelera.php')) ?>">Papelera (<?= $trashed ?>)</a></p>
<?php endif; ?>
<?php
admin_footer();
