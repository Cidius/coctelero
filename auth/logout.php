<?php
declare(strict_types=1);

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/UserAuth.php';

use App\UserAuth;

use function App\url;

UserAuth::logout();

header('Location: ' . url(UserAuth::sanitizeReturnTo((string) ($_GET['return_to'] ?? '/'))));
