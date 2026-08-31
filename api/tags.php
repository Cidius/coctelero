<?php
declare(strict_types=1);

/**
 * GET /api/tags.php?q=ron  -> [{name, slug}, ...]
 * Autocompletado de etiquetas para el formulario del admin.
 */

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/Auth.php';
require __DIR__ . '/../src/RecipeAdmin.php';

use App\Auth;
use App\RecipeAdmin;

use function App\boot_errors;

boot_errors();

header('Content-Type: application/json; charset=utf-8');

if (!Auth::check()) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

$q = (string) ($_GET['q'] ?? '');
echo json_encode(
    RecipeAdmin::tagSuggestions($q, 12),
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
