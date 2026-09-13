<?php
declare(strict_types=1);

/** Vuelta de Google: valida, loguea y redirige a donde el usuario estaba. */

require __DIR__ . '/../../src/helpers.php';
require __DIR__ . '/../../src/UserAuth.php';
require __DIR__ . '/../../src/GoogleAuth.php';

use App\GoogleAuth;
use App\UserAuth;

use function App\boot_errors;
use function App\config;
use function App\e;
use function App\url;

boot_errors();
UserAuth::startSession();

$state = (string) ($_GET['state'] ?? '');
$code  = (string) ($_GET['code'] ?? '');
$savedState = (string) ($_SESSION['oauth_state'] ?? '');
unset($_SESSION['oauth_state']);

if (isset($_GET['error'])) {
    header('Location: ' . url('/'));
    exit;
}

if ($code === '' || $state === '' || $savedState === '' || !hash_equals($savedState, $state)) {
    http_response_code(400);
    header('Content-Type: text/html; charset=utf-8');
    echo '<p>No se pudo iniciar sesión (estado inválido). Volvé a <a href="' . e(url('/')) . '">la home</a> e intentá de nuevo.</p>';
    exit;
}

try {
    $profile = GoogleAuth::exchangeCodeForProfile($code);
    UserAuth::loginWithGoogle($profile);
} catch (\Throwable $e) {
    http_response_code(502);
    header('Content-Type: text/html; charset=utf-8');
    $env = config()['env'] ?? 'prod';
    echo '<p>No se pudo validar con Google.' . ($env === 'dev' ? ' ' . htmlspecialchars($e->getMessage()) : '')
        . ' <a href="' . e(url('/')) . '">Volver</a>.</p>';
    exit;
}

$returnTo = (string) ($_SESSION['login_return_to'] ?? '/');
unset($_SESSION['login_return_to']);

header('Location: ' . url(UserAuth::sanitizeReturnTo($returnTo)));
exit;
