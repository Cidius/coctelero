<?php
declare(strict_types=1);

/**
 * POST /api/favorite.php  (slug, _csrf)
 * Alterna el favorito/like del usuario logueado sobre una receta.
 * Respuesta: { favorited: bool, count: int }
 */

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/UserAuth.php';
require __DIR__ . '/../src/Favorites.php';
require __DIR__ . '/../src/Recipe.php';

use App\Favorites;
use App\Recipe;
use App\UserAuth;

use function App\boot_errors;

boot_errors();
header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'method_not_allowed']);
    exit;
}

if (!UserAuth::check()) {
    http_response_code(401);
    echo json_encode(['error' => 'login_required']);
    exit;
}

UserAuth::requireCsrf();

$slug = strtolower(trim((string) ($_POST['slug'] ?? '')));
$recipe = preg_match('/^[a-z0-9-]{1,180}$/', $slug) ? Recipe::basicBySlug($slug) : null;

if ($recipe === null) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}

$user = UserAuth::user();
[$favorited, $count] = Favorites::toggle($user['id'], (int) $recipe['id']);

echo json_encode(['favorited' => $favorited, 'count' => $count], JSON_UNESCAPED_UNICODE);
