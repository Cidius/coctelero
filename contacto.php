<?php
declare(strict_types=1);

/**
 * Contacto con el coctelero: feedback general o sobre una receta puntual
 * (?recipe=slug). Requiere estar logueado.
 */

require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/UserAuth.php';
require __DIR__ . '/src/Messages.php';
require __DIR__ . '/src/Recipe.php';

use App\Messages;
use App\Recipe;
use App\UserAuth;

use function App\asset;
use function App\boot_errors;
use function App\e;
use function App\pwa_head;
use function App\seo_head;
use function App\url;

boot_errors();
UserAuth::requireLogin($_SERVER['REQUEST_URI'] ?? '/contacto.php');
$user = UserAuth::user();

$slug = strtolower(trim((string) ($_GET['recipe'] ?? '')));
$recipe = ($slug !== '' && preg_match('/^[a-z0-9-]{1,180}$/', $slug)) ? Recipe::basicBySlug($slug) : null;

$sent = false;
$error = null;
$body = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    UserAuth::requireCsrf();

    $body = trim((string) ($_POST['body'] ?? ''));
    $postedSlug = strtolower(trim((string) ($_POST['recipe'] ?? '')));
    $postedRecipe = ($postedSlug !== '' && preg_match('/^[a-z0-9-]{1,180}$/', $postedSlug))
        ? Recipe::basicBySlug($postedSlug)
        : null;

    if ($body === '') {
        $error = 'Escribí tu mensaje.';
    } elseif (mb_strlen($body) > 4000) {
        $error = 'El mensaje es demasiado largo (máx. 4000 caracteres).';
    } else {
        Messages::create($user['id'], $user['name'], $user['email'], $body, $postedRecipe['id'] ?? null);
        Messages::notifyAdmin($user['name'], $user['email'], $body, $postedRecipe['name'] ?? null);
        $sent = true;
        $recipe = $postedRecipe;
        $body = '';
    }
}

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Contacto · Recetario de Cócteles</title>
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <?php seo_head('Contacto', 'Escribile al coctelero.', url('contacto.php')); ?>
    <?php pwa_head(); ?>
</head>
<body>
<header class="site-header">
    <div class="wrap">
        <h1><a href="<?= e(url('/')) ?>">Recetario de Cócteles</a></h1>
        <?= UserAuth::headerHtml() ?>
    </div>
</header>

<main class="wrap detail">
    <p class="back"><a href="<?= e(url('/')) ?>">← Volver</a></p>
    <h1>Contacto</h1>

    <?php if ($recipe !== null): ?>
        <p class="muted">Sobre: <a href="<?= e(url('receta.php?slug=' . urlencode($recipe['slug']))) ?>"><?= e($recipe['name']) ?></a></p>
    <?php endif; ?>

    <?php if ($sent): ?>
        <p class="alert ok">¡Gracias! Tu mensaje le llegó al coctelero.</p>
        <p><a href="<?= e(url('/')) ?>">← Volver al recetario</a></p>
    <?php else: ?>
        <?php if ($error !== null): ?>
            <p class="alert error"><?= e($error) ?></p>
        <?php endif; ?>
        <form method="post" action="<?= e(url('contacto.php')) ?>" class="contact-form">
            <?= UserAuth::csrfField() ?>
            <?php if ($recipe !== null): ?>
                <input type="hidden" name="recipe" value="<?= e($recipe['slug']) ?>">
            <?php endif; ?>
            <label class="form-field">
                <span>Mensaje para el coctelero</span>
                <textarea name="body" rows="6" required maxlength="4000"
                          placeholder="Feedback, una receta que probaste, una sugerencia…"><?= e($body) ?></textarea>
            </label>
            <p class="muted small">Se envía como <?= e($user['name']) ?> (<?= e($user['email']) ?>).</p>
            <button type="submit" class="btn primary">Enviar</button>
        </form>
    <?php endif; ?>
</main>

<footer class="site-footer">
    <div class="wrap"><a href="<?= e(url('/')) ?>">El machete necesario para cualquier bartender <span class="by">by Cidius</span></a></div>
</footer>
<script src="<?= e(asset('assets/js/menu.js')) ?>" defer></script>
</body>
</html>
