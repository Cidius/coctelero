<?php
declare(strict_types=1);

/**
 * GET /api/recipes.php
 *
 * Parametros (todos opcionales, combinables):
 *   q         texto libre (nombre, descripcion, ingredientes)
 *   tag       slug de tag; repetible o separado por coma. AND entre tags.
 *   method    integrado | refrescado_directo | batido | machacado | frozen | otro
 *   volume    short | medium | long
 *   moment    aperitivo | digestivo | all_day
 *   family    slug de familia (sour, julep, ...)
 *   page      pagina (default 1)
 *   per_page  resultados por pagina (default 24, max 60)
 *
 * Respuesta: { data: [ {name, slug, image_url, glassware, ice, method,
 *              method_label, volume, moment, family, family_slug, garnish,
 *              tags:[{name,slug}]} ], meta: {...} }
 */

require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/Recipe.php';

use App\Recipe;

use function App\boot_errors;
use function App\method_label;
use function App\query_tags;
use function App\recipe_image_url;

boot_errors();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');

try {
    $result = Recipe::search([
        'q'        => (string) ($_GET['q'] ?? ''),
        'tags'     => query_tags($_GET),
        'method'   => (string) ($_GET['method'] ?? ''),
        'volume'   => (string) ($_GET['volume'] ?? ''),
        'moment'   => (string) ($_GET['moment'] ?? ''),
        'family'   => (string) ($_GET['family'] ?? ''),
        'page'     => (int) ($_GET['page'] ?? 1),
        'per_page' => (int) ($_GET['per_page'] ?? 24),
    ]);

    $volLabels = Recipe::VOLUMES;
    $momLabels = Recipe::MOMENTS;

    $data = array_map(static function (array $r) use ($volLabels, $momLabels): array {
        return [
            'name'         => $r['name'],
            'slug'         => $r['slug'],
            'image_url'    => recipe_image_url($r['image_path'] ?? null),
            'glassware'    => $r['glassware'],
            'ice'          => $r['ice'],
            'method'       => $r['method'],
            'method_label' => method_label($r['method'], $r['method_other'] ?? null),
            'volume'       => $r['volume'],
            'volume_label' => $r['volume'] !== null ? ($volLabels[$r['volume']] ?? null) : null,
            'moment'       => $r['moment'],
            'moment_label' => $r['moment'] !== null ? ($momLabels[$r['moment']] ?? null) : null,
            'family'       => $r['family'],
            'family_slug'  => $r['family_slug'],
            'garnish'      => $r['garnish'],
            'tags'         => $r['tags'] ?? [],
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
