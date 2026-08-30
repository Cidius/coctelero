<?php
declare(strict_types=1);

/**
 * Home + listado + filtros.
 *
 * PLACEHOLDER (Fase 0). El front publico real se construye en la Fase 2.
 * Por ahora este archivo solo verifica que la conexion y los datos esten OK.
 */

require __DIR__ . '/../src/Database.php';

use App\Database;

$config = Database::loadConfig();
if (($config['env'] ?? 'prod') === 'dev') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

$pdo = Database::get();
$total = (int) $pdo->query('SELECT COUNT(*) FROM recipes WHERE deleted_at IS NULL')->fetchColumn();
$recipes = $pdo->query(
    'SELECT name, slug, method FROM recipes WHERE deleted_at IS NULL ORDER BY name'
)->fetchAll();

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recetario de Cócteles</title>
    <style>
        body { font: 16px/1.5 system-ui, sans-serif; margin: 2rem auto; max-width: 40rem; padding: 0 1rem; }
        h1 { font-size: 1.4rem; }
        .nota { background: #fff8e1; border: 1px solid #ffe082; padding: .75rem 1rem; border-radius: .5rem; }
        ul { padding-left: 1.2rem; }
        code { background: #f0f0f0; padding: .1rem .3rem; border-radius: .2rem; }
    </style>
</head>
<body>
    <h1>Recetario de Cócteles</h1>
    <p class="nota">
        <strong>Fase 0 OK.</strong> Conexión a la base y datos cargados correctamente.
        El front público (listado, filtros, buscador) se construye en la Fase 2.
    </p>
    <p><strong><?= $total ?></strong> recetas activas en la base.</p>
    <ul>
        <?php foreach ($recipes as $r): ?>
            <li><?= htmlspecialchars($r['name']) ?> <code><?= htmlspecialchars($r['method']) ?></code></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
