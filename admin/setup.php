<?php
declare(strict_types=1);

/**
 * Alta del PRIMER usuario admin, por web.
 * Se auto-deshabilita en cuanto existe un usuario (devuelve 403).
 * Igual: borrá este archivo del servidor despues de usarlo.
 */

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/Database.php';

use App\Auth;
use App\Database;

use function App\e;
use function App\url;

$pdo = Database::get();
$count = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();

if ($count > 0) {
    http_response_code(403);
    admin_header('Setup', chrome: false);
    ?>
    <div class="login-box">
        <h1>Setup deshabilitado</h1>
        <p class="muted">Ya existe un usuario admin. Borrá <code>admin/setup.php</code> del servidor
            y entrá por el <a href="<?= e(url('admin/login.php')) ?>">login</a>.</p>
    </div>
    <?php
    admin_footer();
    exit;
}

$error = null;
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $u  = trim((string) ($_POST['username'] ?? ''));
    $p  = (string) ($_POST['password'] ?? '');
    $p2 = (string) ($_POST['password2'] ?? '');

    if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $u)) {
        $error = 'Usuario inválido (3 a 50: letras, números, . _ -).';
    } elseif (strlen($p) < 10) {
        $error = 'La contraseña necesita al menos 10 caracteres.';
    } elseif ($p !== $p2) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?)')
            ->execute([$u, password_hash($p, PASSWORD_DEFAULT)]);
        $done = true;
    }
}

admin_header('Setup', chrome: false);
?>
<div class="login-box">
    <h1>Crear usuario admin</h1>

    <?php if ($done): ?>
        <p class="alert ok">Usuario creado. Ahora <strong>borrá <code>admin/setup.php</code></strong> del servidor.</p>
        <p><a href="<?= e(url('admin/login.php')) ?>">Ir al login</a></p>
    <?php else: ?>
        <?php if ($error !== null): ?>
            <p class="alert error"><?= e($error) ?></p>
        <?php endif; ?>
        <form method="post" action="<?= e(url('admin/setup.php')) ?>">
            <?= Auth::csrfField() ?>
            <label>Usuario
                <input type="text" name="username" value="<?= e((string) ($_POST['username'] ?? '')) ?>"
                       autocomplete="username" autofocus required>
            </label>
            <label>Contraseña <small class="muted">(mín. 10)</small>
                <input type="password" name="password" autocomplete="new-password" required>
            </label>
            <label>Repetir contraseña
                <input type="password" name="password2" autocomplete="new-password" required>
            </label>
            <button type="submit">Crear</button>
        </form>
    <?php endif; ?>
</div>
<?php
admin_footer();
