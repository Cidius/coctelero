<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/RecipeAdmin.php';
require_once __DIR__ . '/../src/Uploader.php';

use App\Auth;
use App\RecipeAdmin;
use App\Uploader;

use function App\e;
use function App\method_label;
use function App\recipe_image_url;
use function App\slugify;
use function App\url;

Auth::requireLogin();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$editing = $id > 0;

$recipe = $editing ? RecipeAdmin::find($id) : null;
if ($editing && $recipe === null) {
    http_response_code(404);
    admin_header('No encontrada');
    echo '<p class="alert error">Esa receta no existe.</p>';
    admin_footer();
    exit;
}

/** Valores actuales del form (para repoblar tras error). */
$v = [
    'name'             => $recipe['name'] ?? '',
    'glassware'        => $recipe['glassware'] ?? '',
    'ice'              => $recipe['ice'] ?? '',
    'method'           => $recipe['method'] ?? 'integrado',
    'method_other'     => $recipe['method_other'] ?? '',
    'method_detail'    => $recipe['method_detail'] ?? '',
    'garnish'          => $recipe['garnish'] ?? '',
    'description'      => $recipe['description'] ?? '',
    'ingredients_text' => $recipe['ingredients_text'] ?? '',
    'tags_text'        => $recipe['tags_text'] ?? '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();

    foreach (array_keys($v) as $k) {
        $v[$k] = trim((string) ($_POST[$k] ?? ''));
    }

    if ($v['name'] === '') {
        $errors[] = 'El nombre es obligatorio.';
    }
    if (!in_array($v['method'], RecipeAdmin::METHODS, true)) {
        $errors[] = 'Método inválido.';
    }
    if ($v['method'] === 'otro' && $v['method_other'] === '') {
        $errors[] = 'Si el método es «Otro», completá cuál.';
    }

    // Imagen
    $slugBase = slugify($v['name'] !== '' ? $v['name'] : ($recipe['slug'] ?? 'receta'));
    $newImage = null;
    $removeImage = isset($_POST['remove_image']);
    if (!$errors) {
        try {
            $newImage = Uploader::handle($_FILES['imagen'] ?? [], $slugBase);
        } catch (\RuntimeException $ex) {
            $errors[] = $ex->getMessage();
        }
    }

    if (!$errors) {
        $data = $v;
        $data['created_by'] = Auth::user()['id'] ?? null;

        $oldImage = $recipe['image_path'] ?? null;
        if ($newImage !== null) {
            $data['image_path'] = $newImage;
        } elseif ($removeImage) {
            $data['image_path'] = null;
        }

        try {
            if ($editing) {
                RecipeAdmin::update($id, $data);
                if (($newImage !== null || $removeImage) && $oldImage) {
                    Uploader::delete($oldImage);
                }
                flash_set('ok', 'Receta actualizada.');
            } else {
                $id = RecipeAdmin::create($data);
                flash_set('ok', 'Receta creada.');
            }
            header('Location: ' . url('admin/dashboard.php'));
            exit;
        } catch (\Throwable $ex) {
            if ($newImage !== null) {
                Uploader::delete($newImage); // no dejar huérfano
            }
            $errors[] = 'No se pudo guardar: ' . $ex->getMessage();
        }
    }
}

$currentImage = recipe_image_url($recipe['image_path'] ?? null);

admin_header($editing ? 'Editar receta' : 'Nueva receta');
?>
<div class="page-head">
    <h1><?= $editing ? 'Editar receta' : 'Nueva receta' ?></h1>
    <a class="btn" href="<?= e(url('admin/dashboard.php')) ?>">← Volver</a>
</div>

<?php foreach ($errors as $err): ?>
    <p class="alert error"><?= e($err) ?></p>
<?php endforeach; ?>

<form method="post" action="<?= e(url('admin/receta-form.php')) ?>" enctype="multipart/form-data" class="recipe-form">
    <?= Auth::csrfField() ?>
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= (int) $id ?>"><?php endif; ?>

    <label class="field">
        <span>Nombre *</span>
        <input type="text" name="name" value="<?= e($v['name']) ?>" required autofocus>
    </label>

    <div class="row">
        <label class="field">
            <span>Cristalería</span>
            <input type="text" name="glassware" value="<?= e($v['glassware']) ?>" list="glassware-list">
        </label>
        <label class="field">
            <span>Hielo</span>
            <input type="text" name="ice" value="<?= e($v['ice']) ?>" list="ice-list">
        </label>
    </div>
    <datalist id="ice-list">
        <option>Molido</option><option>En cubos</option><option>Cubo grande</option><option>Rolito/cubo</option>
    </datalist>
    <datalist id="glassware-list">
        <option>Vaso trago largo</option><option>Vaso Old Fashioned</option><option>Copa Cóctel</option>
        <option>Copa Cocktail</option><option>Copa Hurricane</option><option>Vaso corto</option>
    </datalist>

    <div class="row">
        <label class="field">
            <span>Método</span>
            <select name="method" id="method-select">
                <?php foreach (RecipeAdmin::METHODS as $m): ?>
                    <option value="<?= e($m) ?>" <?= $v['method'] === $m ? 'selected' : '' ?>>
                        <?= e(method_label($m)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field" id="method-other-field" <?= $v['method'] === 'otro' ? '' : 'hidden' ?>>
            <span>¿Cuál? (método «Otro»)</span>
            <input type="text" name="method_other" value="<?= e($v['method_other']) ?>">
        </label>
    </div>

    <label class="field">
        <span>Técnica / preparación</span>
        <input type="text" name="method_detail" value="<?= e($v['method_detail']) ?>"
               placeholder="Ej: Batido y doble colado">
    </label>

    <label class="field">
        <span>Ingredientes <small class="muted">— uno por línea</small></span>
        <textarea name="ingredients_text" rows="7"
                  placeholder="60 ml. de Ron Blanco&#10;30 ml. de Jugo de Limón&#10;10/12 hojas de menta"><?= e($v['ingredients_text']) ?></textarea>
    </label>

    <label class="field">
        <span>Decoración</span>
        <input type="text" name="garnish" value="<?= e($v['garnish']) ?>">
    </label>

    <label class="field">
        <span>Notas / historia <small class="muted">— opcional</small></span>
        <textarea name="description" rows="3"><?= e($v['description']) ?></textarea>
    </label>

    <div class="field">
        <span>Etiquetas <small class="muted">— separadas por coma</small></span>
        <input type="text" name="tags_text" id="tags-input" value="<?= e($v['tags_text']) ?>"
               autocomplete="off" data-endpoint="<?= e(url('api/tags.php')) ?>">
        <div id="tags-suggest" class="suggest" hidden></div>
    </div>

    <div class="field">
        <span>Imagen</span>
        <?php if ($currentImage): ?>
            <div class="current-image">
                <img src="<?= e($currentImage) ?>" alt="">
                <label class="inline"><input type="checkbox" name="remove_image" value="1"> Quitar imagen</label>
            </div>
        <?php endif; ?>
        <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
        <small class="muted">Se redimensiona y convierte a WebP en el servidor.</small>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn primary"><?= $editing ? 'Guardar cambios' : 'Crear receta' ?></button>
        <a class="btn" href="<?= e(url('admin/dashboard.php')) ?>">Cancelar</a>
    </div>
</form>

<script src="<?= e(url('assets/js/admin-form.js')) ?>" defer></script>
<?php
admin_footer();
