<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/UserAuth.php';

use App\Auth;
use App\UserAuth;

use function App\url;

Auth::logout();
UserAuth::clearAdminHint(); // si entraste via Google, revoca tambien ese atajo
header('Location: ' . url('admin/login.php'));
exit;
