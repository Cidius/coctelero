<?php
declare(strict_types=1);

/**
 * Bootstrap + layout compartido de todas las paginas de /admin.
 */

require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Messages.php';

use App\Auth;
use App\Messages;

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
        <a class="brand" href="<?= e(url('admin/dashboard.php')) ?>">
            <img class="brand-mark" src="<?= e(asset('assets/logo/mark.png')) ?>" alt="">
            El Coctelero · Admin
        </a>
        <?php $unread = Messages::unreadCount(); ?>
        <div class="site-nav">
            <button type="button" id="nav-toggle" class="nav-toggle" aria-expanded="false"
                    aria-controls="nav-panel" aria-label="Abrir menú">☰<?php if ($unread > 0): ?><span class="nav-dot"></span><?php endif; ?></button>
            <div id="nav-backdrop" class="nav-backdrop" hidden></div>
            <nav id="nav-panel" class="nav-panel" aria-label="Menú">
                <button type="button" id="nav-close" class="nav-close" aria-label="Cerrar menú">✕</button>
                <?php if ($user): ?>
                    <div class="nav-user">
                        <span class="user-avatar user-avatar-fallback"><?= e(mb_strtoupper(mb_substr($user['username'], 0, 1))) ?></span>
                        <div class="nav-user-info"><strong><?= e($user['username']) ?></strong><span class="muted small">Admin</span></div>
                    </div>
                <?php endif; ?>
                <div class="nav-links">
                    <a href="<?= e(url('admin/dashboard.php')) ?>">Recetas</a>
                    <a href="<?= e(url('admin/tags.php')) ?>">Tags</a>
                    <a href="<?= e(url('admin/papelera.php')) ?>">Papelera</a>
                    <a href="<?= e(url('admin/mensajes.php')) ?>">Mensajes<?= $unread > 0 ? ' <span class="badge">' . $unread . '</span>' : '' ?></a>
                    <a href="<?= e(url('/')) ?>">Ver sitio</a>
                </div>
                <?php if ($user): ?>
                    <div class="nav-footer"><a href="<?= e(url('admin/logout.php')) ?>">Salir</a></div>
                <?php endif; ?>
            </nav>
        </div>
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
<script src="<?= e(asset('assets/js/menu.js')) ?>" defer></script>
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
