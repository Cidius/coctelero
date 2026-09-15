<?php
declare(strict_types=1);

/** Lista de recetas favoritas del usuario logueado. */

require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/UserAuth.php';
require __DIR__ . '/src/Favorites.php';

use App\Favorites;
use App\UserAuth;

use function App\asset;
use function App\boot_errors;
use function App\e;
use function App\pwa_head;
use function App\render_recipe_card;
use function App\seo_head;
use function App\url;

boot_errors();
UserAuth::requireLogin($_SERVER['REQUEST_URI'] ?? '/favoritos.php');

$user = UserAuth::user();
$recipes = Favorites::forUser($user['id']);

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Mis favoritos · Recetario de Cócteles</title>
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <?php seo_head('Mis favoritos', 'Tus cócteles guardados.', url('favoritos.php')); ?>
    <?php pwa_head(); ?>
</head>
<body>
<header class="site-header">
    <div class="wrap">
        <h1><a class="brand" href="<?= e(url('/')) ?>">
            <img class="brand-mark" src="<?= e(asset('assets/logo/mark.png')) ?>" alt="">
            Recetario de Cócteles
        </a></h1>
        <p><?= count($recipes) ?> favorito<?= count($recipes) === 1 ? '' : 's' ?></p>
        <?= UserAuth::headerHtml() ?>
    </div>
</header>

<main class="wrap">
    <p class="back"><a href="<?= e(url('/')) ?>">← Todas las recetas</a></p>
    <div class="grid" id="grid">
        <?php if ($recipes === []): ?>
            <div class="empty"><strong>Todavía no tenés favoritos</strong>
                Tocá el ♡ en cualquier receta para guardarla acá.</div>
        <?php else: ?>
            <?php foreach ($recipes as $r) echo render_recipe_card($r); ?>
        <?php endif; ?>
    </div>
</main>

<footer class="site-footer">
    <div class="wrap"><a href="<?= e(url('/')) ?>">El machete necesario para cualquier bartender <span class="by">by Cidius</span></a></div>
</footer>
<script src="<?= e(asset('assets/js/menu.js')) ?>" defer></script>
</body>
</html>
