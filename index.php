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
require __DIR__ . '/src/UserAuth.php';

use App\Recipe;
use App\UserAuth;

use function App\asset;
use function App\boot_errors;
use function App\e;
use function App\pwa_head;
use function App\query_tags;
use function App\render_recipe_card;
use function App\sanitize_per_page;
use function App\seo_head;
use function App\url;

boot_errors();

$activeTags    = query_tags($_GET);
$activeMethod  = (string) ($_GET['method'] ?? '');
$activeMoment  = (string) ($_GET['moment'] ?? '');
$activeFamily  = (string) ($_GET['family'] ?? '');
$q             = trim((string) ($_GET['q'] ?? ''));
$activePage    = max(1, (int) ($_GET['page'] ?? 1));
$activePerPage = sanitize_per_page($_GET['per_page'] ?? null);

$result  = Recipe::search([
    'q'        => $q,
    'tags'     => $activeTags,
    'method'   => $activeMethod,
    'moment'   => $activeMoment,
    'family'   => $activeFamily,
    'page'     => $activePage,
    'per_page' => $activePerPage,
]);
$totalActive = Recipe::countActive();
$allTags    = Recipe::tagsWithCounts();
$methods    = Recipe::methodsWithCounts();
$moments    = Recipe::momentsWithCounts();
$families   = Recipe::familiesWithCounts();
$hasFilter  = $q !== '' || $activeTags !== []
    || ($activeMethod !== '' && isset(Recipe::METHODS[$activeMethod]))
    || ($activeMoment !== '' && isset(Recipe::MOMENTS[$activeMoment]))
    || $activeFamily !== '';

// render_recipe_card() vive en src/helpers.php (compartida con favoritos.php).

// Filtros activos como query params reutilizables para armar los links de
// paginacion/tamaño de pagina sin perder la busqueda/filtros vigentes.
$baseQuery = [];
if ($q !== '') $baseQuery['q'] = $q;
if ($activeTags !== []) $baseQuery['tag'] = $activeTags;
if ($activeMethod !== '') $baseQuery['method'] = $activeMethod;
if ($activeMoment !== '') $baseQuery['moment'] = $activeMoment;
if ($activeFamily !== '') $baseQuery['family'] = $activeFamily;
if ($activePerPage !== Recipe::PER_PAGE_OPTIONS[0]) $baseQuery['per_page'] = $activePerPage;

$pageUrl = static function (int $page) use ($baseQuery) {
    $qs = $baseQuery;
    if ($page > 1) $qs['page'] = $page;
    return url('/') . ($qs !== [] ? '?' . http_build_query($qs) : '');
};

/**
 * Numeros de pagina a mostrar (primeras 2, ultimas 2 y la actual), con '...'
 * en los huecos. Para no pasar de una linea en mobile con muchas paginas
 * (ej. 9 paginas -> "1 2 .. 5 .. 8 9" en vez de listarlas todas).
 *
 * @return list<int|string>
 */
$pageTokens = static function (int $current, int $total): array {
    $show = array_unique(array_filter(
        [1, 2, $total - 1, $total, $current],
        static fn($p) => $p >= 1 && $p <= $total
    ));
    sort($show);
    $tokens = [];
    $prev = null;
    foreach ($show as $p) {
        if ($prev !== null && $p - $prev > 1) $tokens[] = '...';
        $tokens[] = $p;
        $prev = $p;
    }
    return $tokens;
};

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="yt1D2UP_uXVGbr33PQGmAKK_PmDZ50svdC0iQsV6ih4">
    <title>El Coctelero Online</title>
    <meta name="description" content="Buscador de recetas de cócteles: filtrá por destilado, familia, método e ingredientes.">
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <?php seo_head(
        'El Coctelero Online',
        'Buscador de recetas de cócteles: filtrá por destilado, familia, método e ingredientes.',
        url('/')
    ); ?>
    <?php pwa_head(); ?>
</head>
<body>
<header class="site-header">
    <div class="wrap">
        <h1><a class="brand" href="<?= e(url('/')) ?>">
            <img class="brand-mark" src="<?= e(asset('assets/logo/mark.png')) ?>" alt="">
            El Coctelero Online
        </a></h1>
        <?= UserAuth::headerHtml() ?>
    </div>
</header>

<main class="wrap" id="app"
      data-endpoint="<?= e(url('api/recipes.php')) ?>"
      data-detail="<?= e(url('receta.php')) ?>">

    <p class="search-subtitle"><?= $totalActive ?> recetas · buscá tu cóctel preferido</p>

    <form class="search" method="get" action="<?= e(url('/')) ?>" role="search">
        <input type="search" name="q" value="<?= e($q) ?>"
               placeholder="Buscar… (ej. menta, ron, negroni)" autocomplete="off">
        <noscript><button type="submit">Buscar</button></noscript>
    </form>

    <button type="button" id="filters-toggle" class="filters-toggle"
            aria-controls="filters" aria-expanded="true" hidden>
        Filtros <span id="filters-count" class="fcount" hidden></span>
    </button>

    <div class="filters" id="filters">
        <?php
        $momentsShown = array_filter($moments, static fn($x) => $x['count'] > 0);
        ?>

        <?php if ($momentsShown): ?>
        <div class="filter-group" data-filter="moment">
            <h2>Momento</h2>
            <div class="chips">
                <?php foreach ($momentsShown as $x): ?>
                    <button type="button" class="chip" data-value="<?= e($x['value']) ?>"
                            aria-pressed="<?= $activeMoment === $x['value'] ? 'true' : 'false' ?>">
                        <?= e($x['label']) ?> <span class="count"><?= (int) $x['count'] ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="filter-group" data-filter="family">
            <h2>Familia</h2>
            <div class="chips">
                <?php foreach ($families as $f): ?>
                    <button type="button" class="chip" data-value="<?= e($f['slug']) ?>"
                            aria-pressed="<?= $activeFamily === $f['slug'] ? 'true' : 'false' ?>">
                        <?= e($f['name']) ?> <span class="count"><?= (int) $f['count'] ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

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
        <form method="get" action="<?= e(url('/')) ?>" class="per-page-form" id="per-page-form">
            <?php foreach ($baseQuery as $k => $v): ?>
                <?php if ($k === 'per_page') continue; ?>
                <?php if (is_array($v)): ?>
                    <?php foreach ($v as $vv): ?>
                        <input type="hidden" name="tag[]" value="<?= e($vv) ?>">
                    <?php endforeach; ?>
                <?php else: ?>
                    <input type="hidden" name="<?= e($k) ?>" value="<?= e((string) $v) ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            <label class="per-page-label">
                Mostrar
                <select name="per_page" id="per-page-select">
                    <?php foreach (Recipe::PER_PAGE_OPTIONS as $opt): ?>
                        <option value="<?= $opt ?>" <?= $opt === $activePerPage ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
                por página
            </label>
            <noscript><button type="submit" class="btn">Aplicar</button></noscript>
        </form>
    </div>

    <div class="grid" id="grid">
        <?php if ($result['data'] === []): ?>
            <div class="empty"><strong>Sin resultados</strong>Probá con otra búsqueda o quitá filtros.</div>
        <?php else: ?>
            <?php foreach ($result['data'] as $r) echo render_recipe_card($r); ?>
        <?php endif; ?>
    </div>

    <nav class="pagination" id="pagination" aria-label="Paginación">
        <?php $totalPages = (int) $result['meta']['pages']; ?>
        <?php if ($totalPages > 1): ?>
            <?php if ($activePage > 1): ?>
                <a class="page-btn" href="<?= e($pageUrl($activePage - 1)) ?>" rel="prev" aria-label="Anterior">‹</a>
            <?php else: ?>
                <span class="page-btn disabled" aria-hidden="true">‹</span>
            <?php endif; ?>
            <div class="page-numbers">
                <?php foreach ($pageTokens($activePage, $totalPages) as $tok): ?>
                    <?php if ($tok === '...'): ?>
                        <span class="page-ellipsis">…</span>
                    <?php elseif ($tok === $activePage): ?>
                        <span class="page-num current" aria-current="page"><?= $tok ?></span>
                    <?php else: ?>
                        <a class="page-num" href="<?= e($pageUrl($tok)) ?>"><?= $tok ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php if ($activePage < $totalPages): ?>
                <a class="page-btn" href="<?= e($pageUrl($activePage + 1)) ?>" rel="next" aria-label="Siguiente">›</a>
            <?php else: ?>
                <span class="page-btn disabled" aria-hidden="true">›</span>
            <?php endif; ?>
        <?php endif; ?>
    </nav>
</main>

<footer class="site-footer">
    <div class="wrap">El machete necesario para cualquier bartender <span class="by">by Cidius</span>
        · <a href="<?= e(url('privacidad.php')) ?>">Privacidad</a></div>
</footer>

<script src="<?= e(asset('assets/js/menu.js')) ?>" defer></script>
<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</body>
</html>
