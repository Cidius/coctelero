<?php
declare(strict_types=1);

/**
 * Home: listado de recetas con buscador y filtros combinables.
 *
 * Funciona sin JavaScript (render server-side segun la query string).
 * app.js lo mejora: filtra sin recargar via /api/recipes.php.
 */

require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/Recipe.php';

use App\Recipe;

use function App\boot_errors;
use function App\e;
use function App\method_label;
use function App\query_tags;
use function App\recipe_image_url;
use function App\url;

boot_errors();

$activeTags   = query_tags($_GET);
$activeMethod = (string) ($_GET['method'] ?? '');
$q            = trim((string) ($_GET['q'] ?? ''));

$result  = Recipe::search([
    'q'      => $q,
    'tags'   => $activeTags,
    'method' => $activeMethod,
    'page'   => (int) ($_GET['page'] ?? 1),
]);
$allTags  = Recipe::tagsWithCounts();
$methods  = Recipe::methodsWithCounts();
$hasFilter = $q !== '' || $activeTags !== [] || ($activeMethod !== '' && isset(Recipe::METHODS[$activeMethod]));

/** Render de una card (compartido conceptualmente con app.js). */
function render_card(array $r): string
{
    $img = recipe_image_url($r['image_path'] ?? null);
    $thumb = $img !== null
        ? '<img src="' . e($img) . '" alt="" loading="lazy">'
        : '🍸';
    $meta = trim(method_label($r['method'], $r['method_other'] ?? null)
        . ($r['glassware'] ? ' · ' . $r['glassware'] : ''));
    $tags = '';
    foreach (array_slice($r['tags'] ?? [], 0, 4) as $t) {
        $tags .= '<span>' . e($t['name']) . '</span>';
    }
    return '<a class="card" href="' . e(url('receta.php?slug=' . urlencode($r['slug']))) . '">'
        . '<div class="thumb">' . $thumb . '</div>'
        . '<div class="body">'
        . '<h3>' . e($r['name']) . '</h3>'
        . '<p class="meta">' . e($meta) . '</p>'
        . ($tags !== '' ? '<div class="card-tags">' . $tags . '</div>' : '')
        . '</div></a>';
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recetario de Cócteles</title>
    <meta name="description" content="Buscador de recetas de cócteles: filtrá por destilado, método e ingredientes.">
    <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>">
</head>
<body>
<header class="site-header">
    <div class="wrap">
        <h1><a href="<?= e(url('/')) ?>">Recetario de Cócteles</a></h1>
        <p><?= (int) $result['meta']['total'] ?> recetas · buscá por nombre, destilado o ingrediente</p>
    </div>
</header>

<main class="wrap" id="app"
      data-endpoint="<?= e(url('api/recipes.php')) ?>"
      data-detail="<?= e(url('receta.php')) ?>">

    <form class="search" method="get" action="<?= e(url('/')) ?>" role="search">
        <input type="search" name="q" value="<?= e($q) ?>"
               placeholder="Buscar… (ej. menta, ron, negroni)" autocomplete="off">
        <noscript><button type="submit">Buscar</button></noscript>
    </form>

    <div class="filters">
        <?php if ($methods): ?>
        <div class="filter-group" data-filter="method">
            <h2>Método</h2>
            <div class="chips">
                <?php foreach ($methods as $m): ?>
                    <button type="button" class="chip" data-value="<?= e($m['value']) ?>"
                            aria-pressed="<?= $activeMethod === $m['value'] ? 'true' : 'false' ?>">
                        <?= e($m['label']) ?> <span class="count"><?= (int) $m['count'] ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="filter-group" data-filter="tag">
            <h2>Etiquetas</h2>
            <div class="chips">
                <?php foreach ($allTags as $t): ?>
                    <button type="button" class="chip" data-value="<?= e($t['slug']) ?>"
                            aria-pressed="<?= in_array($t['slug'], $activeTags, true) ? 'true' : 'false' ?>">
                        <?= e($t['name']) ?> <span class="count"><?= (int) $t['count'] ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="toolbar">
        <span id="result-count"><?= (int) $result['meta']['total'] ?> resultado<?= $result['meta']['total'] === 1 ? '' : 's' ?></span>
        <button type="button" id="reset" <?= $hasFilter ? '' : 'hidden' ?>>Limpiar filtros</button>
    </div>

    <div class="grid" id="grid">
        <?php if ($result['data'] === []): ?>
            <div class="empty"><strong>Sin resultados</strong>Probá con otra búsqueda o quitá filtros.</div>
        <?php else: ?>
            <?php foreach ($result['data'] as $r) echo render_card($r); ?>
        <?php endif; ?>
    </div>
</main>

<footer class="site-footer">
    <div class="wrap">Recetario del Taller de Coctelería</div>
</footer>

<script src="<?= e(url('assets/js/app.js')) ?>" defer></script>
</body>
</html>
