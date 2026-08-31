<?php
declare(strict_types=1);

require __DIR__ . '/_common.php';
require_once __DIR__ . '/../src/RecipeAdmin.php';

use App\Auth;
use App\RecipeAdmin;

use function App\url;

Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}
Auth::requireCsrf();

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    RecipeAdmin::softDelete($id);
    flash_set('ok', 'Receta enviada a la papelera.');
}
header('Location: ' . url('admin/dashboard.php'));
exit;
