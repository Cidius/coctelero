<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';

use App\Auth;

use function App\url;

Auth::logout();
header('Location: ' . url('admin/login.php'));
exit;
