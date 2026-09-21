<?php
declare(strict_types=1);

/**
 * Generador de sql/datos_momento_sabor_2026.sql.
 *
 *   php sql/fuente/build_momento_sabor.php
 *
 * Lee sql/fuente/recipe_flavors_2026.csv (planilla de clasificacion:
 * id, nombre, momento, perfil de sabor ya ordenado de mas a menos
 * predominante) y escribe sql/datos_momento_sabor_2026.sql.
 *
 * Matchea cada fila por slug (slugify(nombre), la misma funcion que usa
 * el sitio al crear una receta) -- el "id" de la planilla es solo el
 * numero de fila de la fuente, no el id real de la base.
 *
 * El .sql generado se commitea. Este script queda como registro de como
 * se genero, para poder regenerarlo si cambia la planilla.
 */

require __DIR__ . '/../../src/helpers.php';

use function App\slugify;

$csvPath = __DIR__ . '/recipe_flavors_2026.csv';
$outPath = __DIR__ . '/../datos_momento_sabor_2026.sql';

$rows = [];
$fh = fopen($csvPath, 'r');
$header = fgetcsv($fh, 0, ',', '"', '\\');
while (($row = fgetcsv($fh, 0, ',', '"', '\\')) !== false) {
    $rows[] = array_combine($header, $row);
}
fclose($fh);

$momentMap = [
    'Para todo momento' => 'all_day',
    'Aperitivo'          => 'aperitivo',
    'Digestivo'          => 'digestivo',
];
$flavorMap = [
    'Dulce'  => 'dulce',
    'Amargo' => 'amargo',
    'Ácido'  => 'acido',
    'Seco'   => 'seco',
];

function sql_esc(string $s): string
{
    return str_replace("'", "''", $s);
}

$out = [];
$out[] = '-- =====================================================================';
$out[] = '--  Datos: momento + perfil de sabor (con orden de predominancia) para';
$out[] = '--  las recetas existentes. GENERADO por sql/fuente/build_momento_sabor.php';
$out[] = '--  a partir de sql/fuente/recipe_flavors_2026.csv -- no editar a mano.';
$out[] = '--  Correr DESPUES de sql/migracion_14_predominancia_sabor.sql.';
$out[] = '-- =====================================================================';
$out[] = '';
$out[] = 'SET NAMES utf8mb4;';
$out[] = '';

$warnings = [];

foreach ($rows as $r) {
    $name = trim($r['recipe_name']);
    $slug = slugify($name);
    $momentTxt = trim($r['moment']);
    $momentSlug = $momentMap[$momentTxt] ?? null;
    if ($momentSlug === null) {
        $warnings[] = "Momento desconocido '$momentTxt' para '$name'";
        continue;
    }

    $out[] = "-- {$name} ({$slug})";
    $out[] = "UPDATE recipes SET moment = '{$momentSlug}' WHERE slug = '" . sql_esc($slug) . "';";
    $out[] = "DELETE rfp FROM recipe_flavor_profiles rfp JOIN recipes r ON r.id = rfp.recipe_id "
        . "WHERE r.slug = '" . sql_esc($slug) . "';";

    $pos = 1;
    foreach (array_map('trim', explode(',', $r['flavor'])) as $fn) {
        $fSlug = $flavorMap[$fn] ?? null;
        if ($fSlug === null) {
            $warnings[] = "Sabor desconocido '$fn' para '$name'";
            continue;
        }
        $out[] = "INSERT INTO recipe_flavor_profiles (recipe_id, flavor_profile_id, position) "
            . "SELECT r.id, fp.id, {$pos} FROM recipes r JOIN flavor_profiles fp ON fp.slug = '{$fSlug}' "
            . "WHERE r.slug = '" . sql_esc($slug) . "';";
        $pos++;
    }
    $out[] = '';
}

file_put_contents($outPath, implode("\n", $out) . "\n");

fwrite(STDERR, 'Filas procesadas: ' . count($rows) . "\n");
fwrite(STDERR, 'Warnings: ' . count($warnings) . "\n");
foreach ($warnings as $w) {
    fwrite(STDERR, "  - $w\n");
}
echo "Escrito: $outPath\n";
