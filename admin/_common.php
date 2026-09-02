<?php
declare(strict_types=1);

/**
 * Bootstrap + layout compartido de todas las paginas de /admin.
 */

require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Auth.php';

use App\Auth;

use function App\asset;
use function App\boot_errors;
use function App\e;
use function App\url;

boot_errors();
Auth::startSession();

/** IP del cliente (Hostinger pasa la real por X-Forwarded-For). */
function client_ip(): string
{
    $fwd = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($fwd !== '') {
        $ip = trim(explode(',', $fwd)[0]);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function admin_header(string $title, bool $chrome = true): void
{
    $user = App\Auth::user();
    header('Content-Type: text/html; charset=utf-8');
    ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> · Admin</title>
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
</head>
<body class="admin">
<?php if ($chrome): ?>
<header class="admin-bar">
    <div class="wrap">
        <a class="brand" href="<?= e(url('admin/dashboard.php')) ?>">Recetario · Admin</a>
        <nav>
            <a href="<?= e(url('admin/dashboard.php')) ?>">Recetas</a>
            <a href="<?= e(url('admin/papelera.php')) ?>">Papelera</a>
            <a href="<?= e(url('/')) ?>" target="_blank" rel="noopener">Ver sitio ↗</a>
            <?php if ($user): ?>
                <span class="who"><?= e($user['username']) ?></span>
                <a href="<?= e(url('admin/logout.php')) ?>">Salir</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<?php endif; ?>
<main class="wrap admin-main">
    <?php
}

function admin_footer(): void
{
    ?>
</main>
</body>
</html>
    <?php
}

/** Muestra un flash guardado en sesion y lo limpia. */
function flash_take(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

function flash_set(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
