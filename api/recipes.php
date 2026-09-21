<?php
declare(strict_types=1);

/**
 * GET /api/recipes.php
 *
 * Parametros (todos opcionales, combinables):
 *   q         texto libre (nombre, descripcion, ingredientes)
 *   tag       slug de tag; repetible o separado por coma. AND entre tags.
 *   flavor    slug de perfil de sabor (dulce|amargo|acido|seco); repetible
 *             o separado por coma. OR entre perfiles (alcanza con uno).
 *   method    integrado | directo | batido | machacado | licuado | lanzado | capas | otro
 *   moment    aperitivo | digestivo | all_day
 *   family    slug de familia (sour, julep, ...)
 *   page      pagina (default 1)
 *   per_page  resultados por pagina: 10 (default), 20 o 50
 *
 * Respuesta: { data: [ {name, slug, image_url, glassware, ice, method,
 *              method_label, moment, moment_label, family, family_slug,
 *              garnish, tags:[{name,slug,is_spirit}],
 *              flavor_profiles:[{name,slug}] (en orden de predominancia)}
 *              ], meta: {...} }
 */

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/Recipe.php';

use App\Recipe;

use function App\boot_errors;
use function App\method_label;
use function App\query_slug_list;
use function App\query_tags;
use function App\recipe_image_url;
use function App\sanitize_per_page;

boot_errors();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');

try {
    $result = Recipe::search([
        'q'        => (string) ($_GET['q'] ?? ''),
        'tags'     => query_tags($_GET),
        'flavors'  => query_slug_list($_GET, 'flavor'),
        'method'   => (string) ($_GET['method'] ?? ''),
        'moment'   => (string) ($_GET['moment'] ?? ''),
        'family'   => (string) ($_GET['family'] ?? ''),
        'page'     => (int) ($_GET['page'] ?? 1),
        'per_page' => sanitize_per_page($_GET['per_page'] ?? null),
    ]);

    $momLabels = Recipe::MOMENTS;

    $data = array_map(static function (array $r) use ($momLabels): array {
        return [
            'name'         => $r['name'],
            'slug'         => $r['slug'],
            'image_url'    => recipe_image_url($r['image_path'] ?? null),
            'glassware'    => $r['glassware'],
            'ice'          => $r['ice'],
            'method'       => $r['method'],
            'method_label' => method_label($r['method'], $r['method_other'] ?? null),
            'moment'       => $r['moment'],
            'moment_label' => $r['moment'] !== null ? ($momLabels[$r['moment']] ?? null) : null,
            'family'       => $r['family'],
            'family_slug'  => $r['family_slug'],
            'garnish'      => $r['garnish'],
            'tags'         => $r['tags'] ?? [],
            'flavor_profiles' => $r['flavor_profiles'] ?? [],
        ];
    }, $result['data']);

    echo json_encode(
        ['data' => $data, 'meta' => $result['meta']],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
} catch (\Throwable $e) {
    http_response_code(500);
    $env = (App\config()['env'] ?? 'prod');
    echo json_encode([
        'error' => 'server_error',
        'detail' => $env === 'dev' ? $e->getMessage() : null,
    ], JSON_UNESCAPED_UNICODE);
}
