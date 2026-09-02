<?php
declare(strict_types=1);

/**
 * Detalle de una receta: /receta.php?slug=negroni
 */

require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/Recipe.php';

use App\Recipe;

use function App\boot_errors;
use function App\e;
use function App\method_label;
use function App\recipe_image_url;
use function App\url;

boot_errors();

$slug = strtolower(trim((string) ($_GET['slug'] ?? '')));
$recipe = preg_match('/^[a-z0-9-]{1,180}$/', $slug) ? Recipe::findBySlug($slug) : null;

header('Content-Type: text/html; charset=utf-8');

if ($recipe === null) {
    http_response_code(404);
    ?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Receta no encontrada</title>
        <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
    </head>
    <body>
    <main class="wrap detail">
        <p class="back"><a href="<?= e(url('/')) ?>">← Volver</a></p>
        <div class="empty"><strong>Esa receta no existe</strong>Puede que haya cambiado de nombre.</div>
    </main>
    </body>
    </html>
    <?php
    exit;
}

$img = recipe_image_url($recipe['image_path'] ?? null);
$methodTxt = method_label($recipe['method'], $recipe['method_other'] ?? null);
if (!empty($recipe['method_detail'])) {
    $methodTxt .= ' — ' . $recipe['method_detail'];
}

$specs = [];
if (!empty($recipe['family']))    $specs['Familia']     = $recipe['family'];
if (!empty($recipe['volume']))    $specs['Volumen']     = Recipe::VOLUMES[$recipe['volume']] ?? $recipe['volume'];
if (!empty($recipe['moment']))    $specs['Momento']     = Recipe::MOMENTS[$recipe['moment']] ?? $recipe['moment'];
if (!empty($recipe['glassware'])) $specs['Cristalería'] = $recipe['glassware'];
if (!empty($recipe['ice']))       $specs['Hielo']       = $recipe['ice'];
$specs['Método'] = $methodTxt;
if (!empty($recipe['garnish']))   $specs['Decoración']  = $recipe['garnish'];

$metaDesc = $recipe['name'] . ' — '
    . implode(', ', array_map(static fn($i) => $i['raw_text'], array_slice($recipe['ingredients'], 0, 4)));
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($recipe['name']) ?> · Recetario de Cócteles</title>
    <meta name="description" content="<?= e(mb_substr($metaDesc, 0, 160)) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
</head>
<body>
<header class="site-header">
    <div class="wrap"><h1><a href="<?= e(url('/')) ?>">Recetario de Cócteles</a></h1></div>
</header>

<main class="wrap detail">
    <p class="back"><a href="<?= e(url('/')) ?>">← Todas las recetas</a></p>
    <h1><?= e($recipe['name']) ?></h1>

    <?php if ($img !== null): ?>
        <div class="hero"><img src="<?= e($img) ?>" alt="<?= e($recipe['name']) ?>"></div>
    <?php endif; ?>

    <dl class="specs">
        <?php foreach ($specs as $label => $value): ?>
            <div><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd></div>
        <?php endforeach; ?>
    </dl>

    <h2>Ingredientes</h2>
    <ul class="ingredients">
        <?php foreach ($recipe['ingredients'] as $i): ?>
            <li><?= e($i['raw_text']) ?></li>
        <?php endforeach; ?>
    </ul>

    <?php if (!empty($recipe['description'])): ?>
        <h2>Notas</h2>
        <p class="note"><?= e($recipe['description']) ?></p>
    <?php endif; ?>

    <?php if (!empty($recipe['tags'])): ?>
        <h2>Etiquetas</h2>
        <div class="tag-links">
            <?php foreach ($recipe['tags'] as $t): ?>
                <a href="<?= e(url('/?tag=' . urlencode($t['slug']))) ?>"><?= e($t['name']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer class="site-footer">
    <div class="wrap"><a href="<?= e(url('/')) ?>">Recetario del Taller de Coctelería</a></div>
</footer>
</body>
</html>
