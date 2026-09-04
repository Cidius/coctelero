<?php
declare(strict_types=1);

/**
 * Detalle de una receta: /receta.php?slug=negroni
 */

require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/Recipe.php';

use App\Recipe;

use function App\asset;
use function App\boot_errors;
use function App\e;
use function App\method_label;
use function App\pwa_head;
use function App\recipe_image_url;
use function App\seo_head;
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
        <meta name="robots" content="noindex">
        <title>Receta no encontrada</title>
        <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
<?php pwa_head(); ?>
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

if (Recipe::registerView((int) $recipe['id'])) {
    $recipe['views'] = (int) ($recipe['views'] ?? 0) + 1;
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

$canonical = url('receta.php?slug=' . urlencode($recipe['slug']));

// Datos estructurados schema.org/Recipe (resultados enriquecidos de Google).
$ld = [
    '@context'         => 'https://schema.org',
    '@type'            => 'Recipe',
    'name'             => $recipe['name'],
    'url'              => $canonical,
    'recipeIngredient' => array_values(array_map(
        static fn($i) => $i['raw_text'],
        $recipe['ingredients']
    )),
];
if ($img !== null) {
    $ld['image'] = [$img];
}
if (!empty($recipe['description'])) {
    $ld['description'] = $recipe['description'];
}
if (!empty($recipe['family'])) {
    $ld['recipeCategory'] = $recipe['family'];
}
if (!empty($recipe['method_detail'])) {
    $ld['recipeInstructions'] = [['@type' => 'HowToStep', 'text' => $recipe['method_detail']]];
}
if (!empty($recipe['author_name'])) {
    $ld['author'] = ['@type' => 'Person', 'name' => $recipe['author_name']];
}
if (!empty($recipe['tags'])) {
    $ld['keywords'] = implode(', ', array_map(static fn($t) => $t['name'], $recipe['tags']));
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($recipe['name']) ?> · Recetario de Cócteles</title>
    <meta name="description" content="<?= e(mb_substr($metaDesc, 0, 160)) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <?php seo_head($recipe['name'] . ' · Recetario de Cócteles', $metaDesc, $canonical, $img, 'article'); ?>
    <?php pwa_head(); ?>
    <script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
</head>
<body>
<header class="site-header">
    <div class="wrap"><h1><a href="<?= e(url('/')) ?>">Recetario de Cócteles</a></h1></div>
</header>

<main class="wrap detail">
    <p class="back"><a href="<?= e(url('/')) ?>">← Todas las recetas</a></p>
    <h1><?= e($recipe['name']) ?></h1>

    <?php if (!empty($recipe['author_name'])): ?>
        <p class="author">Cóctel de
            <?php $au = \App\safe_url($recipe['author_url'] ?? null); ?>
            <?php if ($au !== null): ?>
                <a href="<?= e($au) ?>" target="_blank" rel="noopener noreferrer"><?= e($recipe['author_name']) ?></a>
            <?php else: ?>
                <strong><?= e($recipe['author_name']) ?></strong>
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <div class="detail-actions">
        <?php $views = (int) ($recipe['views'] ?? 0); ?>
        <span class="views">👁 <?= number_format($views, 0, ',', '.') ?> vista<?= $views === 1 ? '' : 's' ?></span>
        <button type="button" id="share-btn" class="share-btn"
                data-url="<?= e(url('receta.php?slug=' . urlencode($recipe['slug']))) ?>"
                data-text="<?= e($recipe['name'] . ' — Recetario de Cócteles') ?>">
            Compartir
        </button>
    </div>

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

    <?php if (!empty($recipe['links'])): ?>
        <h2>Más información</h2>
        <ul class="ext-links">
            <?php foreach ($recipe['links'] as $l): ?>
                <?php $lu = \App\safe_url($l['url']); ?>
                <?php if ($lu !== null): ?>
                    <li><a href="<?= e($lu) ?>" target="_blank" rel="noopener noreferrer">
                        <?= e($l['label']) ?> <span class="ext">↗</span>
                    </a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
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
    <div class="wrap"><a href="<?= e(url('/')) ?>">El machete necesario para cualquier bartender <span class="by">by Cidius</span></a></div>
</footer>
<script>
(function () {
    var btn = document.getElementById('share-btn');
    if (!btn) return;
    var url = btn.dataset.url || location.href;
    var data = { title: document.title, text: btn.dataset.text || document.title, url: url };
    btn.addEventListener('click', function () {
        if (navigator.share) {
            navigator.share(data).catch(function () {});
        } else if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(function () {
                var prev = btn.textContent;
                btn.textContent = 'Enlace copiado';
                btn.disabled = true;
                setTimeout(function () { btn.textContent = prev; btn.disabled = false; }, 1800);
            });
        } else {
            window.prompt('Copiá el enlace:', url);
        }
    });
})();
</script>
</body>
</html>
