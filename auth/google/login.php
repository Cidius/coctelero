<?php
declare(strict_types=1);

/** Redirige a Google para iniciar sesion. */

require __DIR__ . '/../../src/helpers.php';
require __DIR__ . '/../../src/UserAuth.php';
require __DIR__ . '/../../src/GoogleAuth.php';

use App\GoogleAuth;
use App\UserAuth;

use function App\boot_errors;

boot_errors();
UserAuth::startSession();

if (UserAuth::check()) {
    header('Location: ' . \App\url(UserAuth::sanitizeReturnTo((string) ($_GET['return_to'] ?? '/'))));
    exit;
}

$_SESSION['login_return_to'] = UserAuth::sanitizeReturnTo((string) ($_GET['return_to'] ?? '/'));

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

try {
    header('Location: ' . GoogleAuth::authorizeUrl($state));
    exit;
} catch (\Throwable $e) {
    http_response_code(500);
    $env = \App\config()['env'] ?? 'prod';
    exit('No se pudo iniciar el login con Google.' . ($env === 'dev' ? ' ' . $e->getMessage() : ''));
}
