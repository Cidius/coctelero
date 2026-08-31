<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';

use App\Auth;

use function App\e;
use function App\url;

if (Auth::check()) {
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Auth::requireCsrf();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Completá usuario y contraseña.';
    } else {
        $error = Auth::attempt($username, $password, client_ip());
        if ($error === null) {
            header('Location: ' . url('admin/dashboard.php'));
            exit;
        }
    }
}

admin_header('Ingresar', chrome: false);
?>
<div class="login-box">
    <h1>Recetario · Admin</h1>
    <?php if ($error !== null): ?>
        <p class="alert error"><?= e($error) ?></p>
    <?php endif; ?>
    <form method="post" action="<?= e(url('admin/login.php')) ?>">
        <?= Auth::csrfField() ?>
        <label>Usuario
            <input type="text" name="username" autocomplete="username" autofocus required>
        </label>
        <label>Contraseña
            <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <button type="submit">Ingresar</button>
    </form>
</div>
<?php
admin_footer();
