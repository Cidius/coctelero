<?php
declare(strict_types=1);

/**
 * Genera sql/import_autor_2026.sql a partir del PDF
 * "Recetas Cocteles de autor e internacionales clasicos -- 2026".
 *
 *   php sql/import/build_import_autor.php
 *
 * Solo incluye recetas con nombre distintivo y slug que NO exista ya en la
 * base (las 52 del taller + lo cargado por el admin). El SQL generado es
 * seguro de re-correr: cada receta se inserta solo si su slug no esta, y
 * ingredientes/tags solo si la receta todavia no los tiene.
 *
 * Quedan fuera (cargar por el ABM): Coctel Nº1/2/3/7/9, "-", *1..*8,
 * *Sin alcohol, *4/*5/*6. Y las que ya existen: Mojito, Negroni, Pisco
 * Sour, Margarita, Old Fashioned, Rob Roy, Hanky Panky, Black Russian,
 * Pineral/Hesperidina/Cynar Julep, Julep del Giardino.
 */

mb_internal_encoding('UTF-8');

/* ---------- helpers ---------- */

function slugify(string $s): string
{
    $s = mb_strtolower(trim($s));
    $s = str_replace('&', ' ', $s);
    $from = ['á','é','í','ó','ú','ü','ñ','à','è','ì','ò','ù','â','ê','î','ô','û','ç','½','¼','¾','’',"'"];
    $to   = ['a','e','i','o','u','u','n','a','e','i','o','u','a','e','i','o','u','c','', '', '', '', ''];
    $s = str_replace($from, $to, $s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-');
}

function q(?string $v): string
{
    return $v === null ? 'NULL' : "'" . str_replace("'", "''", $v) . "'";
}
function num(?float $v): string
{
    if ($v === null) {
        return 'NULL';
    }
    $s = rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.');
    return $s === '' ? '0' : $s;
}

/** "1.5 oz de Campari" -> [raw, 1.5, 'oz'] ; "Puñado de menta" -> [raw, null, null]. */
function ing(string $raw): array
{
    $raw = trim($raw);
    $amount = null;
    $unit = null;
    if (preg_match('/^(\d+(?:\.\d+)?)\s*(oz|ml|cl|cda|cdta|dash|gotas?|rolitos?)?\b/iu', $raw, $m)) {
        $amount = (float) $m[1];
        $u = mb_strtolower($m[2] ?? '');
        $unit = match ($u) {
            'oz' => 'oz', 'ml' => 'ml', 'cl' => 'cl',
            'cda' => 'cda', 'cdta' => 'cdta', 'dash' => 'dash',
            'gota', 'gotas' => 'gota',
            'rolito', 'rolitos' => 'unidad',
            default => null,
        };
    }
    return [$raw, $amount, $unit];
}

/* ---------- datos (transcripcion del PDF) ---------- */

$M_DIR = ['integrado', 'Directo'];
$M_DIRM = ['integrado', 'Directo, menta activada'];
$M_BAT = ['batido', 'Batido y colado simple'];
$M_BATD = ['batido', 'Batido y doble colado'];
$M_REF = ['refrescado_directo', 'Refrescado'];
$M_CUB = ['refrescado_directo', 'Batido cubano'];

$recipes = [
    [
        'name' => 'Coctel Litoraleño', 'glass' => 'Vaso trago largo', 'ice' => null,
        'm' => $M_DIR, 'garnish' => 'Piel de limón', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'highball',
        'ing' => ['1 oz de Pombero del Litoral (vermut)', '1 oz de gin tónica', '2 oz de almíbar de cardamomo', '1 oz de jugo de limón', 'Completar con soda'],
        'tags' => ['vermut', 'gin', 'citrico'],
    ],
    [
        'name' => 'Limonada especiada', 'glass' => 'Copa Hurricane', 'ice' => null,
        'm' => $M_DIR, 'garnish' => 'Piel de limón', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'mocktail',
        'ing' => ['2 oz de almíbar de cardamomo', '1 oz de jugo de limón', 'Completar con soda'],
        'tags' => ['sin-alcohol', 'citrico'],
    ],
    [
        'name' => 'Naranjada especiada', 'glass' => 'Vaso bombé', 'ice' => null,
        'm' => $M_DIR, 'garnish' => 'Media rodaja de naranja', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'mocktail',
        'ing' => ['1 oz de almíbar especiado (canela, anís, clavo de olor)', '2 oz de jugo de naranja', 'Completar con soda'],
        'tags' => ['sin-alcohol', 'citrico'],
    ],
    [
        'name' => 'Coctel Fisgona', 'glass' => 'Copón', 'ice' => null,
        'm' => $M_DIR, 'garnish' => 'Media rodaja de pomelo rosado', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'highball',
        'ing' => ['1 oz de Vodka (Smirnoff Raspberry)', '1 oz de Hierro Quina', '1 oz de almíbar de canela', '1 oz de jugo de pomelo rosado', 'Completar con soda'],
        'tags' => ['vodka', 'hierro-quina', 'pomelo'],
    ],
    [
        'name' => 'Limonada exótica', 'glass' => 'Vaso trago largo', 'ice' => null,
        'm' => $M_DIR, 'garnish' => 'Piel de limón', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'mocktail',
        'ing' => ['1 oz de almíbar de coriandro', '1 oz de jugo de limón', 'Completar con soda'],
        'tags' => ['sin-alcohol', 'citrico'],
    ],
    [
        'name' => 'Special Latte', 'glass' => 'Copa Hurricane', 'ice' => null,
        'm' => $M_BAT, 'garnish' => null, 'vol' => 'medium', 'mom' => 'digestivo', 'fam' => null,
        'desc' => 'Coctel Nº4 del recetario de autor.',
        'ing' => ['1 oz de Ron (Havana Club)', '1 oz de Licor Baileys', '1 oz de almíbar de café', '1 oz de café espresso', '1 oz de leche'],
        'tags' => ['ron', 'licor-crema', 'cafe'],
    ],
    [
        'name' => 'Mix Cítrico', 'glass' => 'Vaso trago largo', 'ice' => null,
        'm' => $M_BAT, 'garnish' => null, 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'mocktail',
        'ing' => ['1 oz de almíbar de coriandro', '1 oz de jugo de limón', '1 oz de jugo de pomelo rosado', '2 oz de jugo de naranja'],
        'tags' => ['sin-alcohol', 'citrico', 'pomelo'],
    ],
    [
        'name' => 'Daiquiri Clásico', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_BATD, 'garnish' => 'Piel de limón', 'vol' => 'short', 'mom' => null, 'fam' => 'sour',
        'ing' => ['2 oz de Ron blanco (Bacardi)', '1 oz de jugo de limón', '1 oz de almíbar simple'],
        'tags' => ['ron', 'citrico'],
    ],
    [
        'name' => 'Cynartini', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_BATD, 'garnish' => 'Piel de pomelo rosado', 'vol' => 'short', 'mom' => 'aperitivo', 'fam' => 'sour',
        'ing' => ['2 oz de Cynar', '1 oz de jugo de pomelo rosado', '1 oz de almíbar simple'],
        'tags' => ['cynar', 'pomelo'],
    ],
    [
        'name' => 'Ponche de Primavera Ruso', 'glass' => 'Vaso trago largo', 'ice' => null,
        'm' => $M_BATD, 'garnish' => null, 'vol' => 'long', 'mom' => null, 'fam' => 'sour',
        'desc' => 'También conocido como Russian Spring Punch.',
        'ing' => ['1 oz de Vodka', '1 oz de Licor de Cassis', '1 oz de jugo de pomelo rosado', '1 oz de almíbar simple'],
        'tags' => ['vodka', 'cassis', 'pomelo'],
    ],
    [
        'name' => 'De Milán a Padua', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_BATD, 'garnish' => null, 'vol' => 'short', 'mom' => 'aperitivo', 'fam' => 'sour',
        'ing' => ['1.5 oz de Campari', '1.5 oz de Aperol', '2 oz de jugo de limón', '2 oz de jugo de naranja'],
        'tags' => ['campari', 'aperol', 'citrico'],
    ],
    [
        'name' => 'Espresso Ferroviario', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_BATD, 'garnish' => '4 granos de café', 'vol' => 'short', 'mom' => 'digestivo', 'fam' => null,
        'ing' => ['1.5 oz de Whisky', '1 oz de Licor Amargo Obrero', '1 oz de café espresso', '0.5 oz de almíbar especiado (anís, canela, clavo de olor)'],
        'tags' => ['whisky', 'amargo-obrero', 'cafe'],
    ],
    [
        'name' => 'Espresso Martini', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_BATD, 'garnish' => '4 granos de café', 'vol' => 'short', 'mom' => 'digestivo', 'fam' => null,
        'ing' => ['1.5 oz de Vodka', '1 oz de Licor de café', '1 oz de café espresso', '0.5 oz de almíbar simple'],
        'tags' => ['vodka', 'cafe', 'licor-cafe'],
    ],
    [
        'name' => 'Última Palabra', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_BATD, 'garnish' => 'Piel de pomelo', 'vol' => 'short', 'mom' => null, 'fam' => null,
        'desc' => 'Versión local del Last Word.',
        'ing' => ['0.75 oz de Dry Gin', '0.75 oz de Licor de cereza', '0.75 oz de Licor de hierbas', '0.75 oz de jugo de limón'],
        'tags' => ['gin', 'chartreuse', 'citrico'],
    ],
    [
        'name' => 'El Turrón', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_BAT, 'garnish' => 'Polvo de cacao y canela', 'vol' => 'short', 'mom' => 'digestivo', 'fam' => null,
        'ing' => ['2 oz de Ron', '1 oz de Malibú', '1 oz de Amarula'],
        'tags' => ['ron', 'malibu', 'licor-crema'],
    ],
    [
        'name' => 'Amaretto Sour', 'glass' => 'Vaso Old Fashioned', 'ice' => null,
        'm' => $M_BAT, 'garnish' => 'Piel de limón', 'vol' => 'short', 'mom' => null, 'fam' => 'sour',
        'ing' => ['2 oz de Amaretto', '1 oz de almíbar simple', '1 oz de jugo de limón', '1 clara de huevo'],
        'tags' => ['amaretto', 'con-huevo', 'citrico'],
    ],
    [
        'name' => 'Limoncello Chiara', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_DIR, 'garnish' => 'Piel de naranja', 'vol' => 'short', 'mom' => 'all_day', 'fam' => null,
        'ing' => ['1 oz de Limoncello', '2 oz de jugo de naranja', '0.5 oz de jugo de limón', 'Completar con soda'],
        'tags' => ['triple-sec', 'citrico'],
    ],
    [
        'name' => 'Negroni ahumado', 'glass' => 'Vaso Old Fashioned', 'ice' => 'En cubos',
        'm' => $M_CUB, 'garnish' => 'Canela en rama', 'vol' => 'short', 'mom' => 'digestivo', 'fam' => 'trio',
        'desc' => 'Ídem Negroni, ahumado con clavo de olor, anís estrellado, canela y cacao.',
        'ing' => ['1 oz de Campari', '1 oz de Vermut rojo', '1 oz de Gin'],
        'tags' => ['campari', 'vermut', 'gin'],
    ],
    [
        'name' => 'Molino Orange', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_REF, 'garnish' => 'Piel de limón', 'vol' => 'short', 'mom' => 'digestivo', 'fam' => null,
        'ing' => ['1 oz de Brandy (o coñac)', '1 oz de Licor de Damasco (base brandy)', '1 oz de Triple Sec'],
        'tags' => ['brandy', 'triple-sec'],
    ],
    [
        'name' => 'Dry Martini Clásic', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_REF, 'garnish' => 'Piel de limón', 'vol' => 'short', 'mom' => 'aperitivo', 'fam' => 'duo',
        'desc' => 'Gin macerado en cardamomo.',
        'ing' => ['1.5 oz de Gin macerado en cardamomo', '1 oz de Vermut seco'],
        'tags' => ['gin', 'vermut'],
    ],
    [
        'name' => 'Vesper Litoraleño', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_REF, 'garnish' => 'Piel de limón', 'vol' => 'short', 'mom' => 'aperitivo', 'fam' => 'duo',
        'desc' => 'Vodka macerado en pimienta de Jamaica.',
        'ing' => ['1.5 oz de Vodka macerado en pimienta de Jamaica', '1 oz de Vermut blanco'],
        'tags' => ['vodka', 'vermut'],
    ],
    [
        'name' => 'La Madrina', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_REF, 'garnish' => null, 'vol' => 'short', 'mom' => 'digestivo', 'fam' => 'duo',
        'desc' => 'Godmother con vodka.',
        'ing' => ['1.5 oz de Vodka', '1 oz de Amaretto'],
        'tags' => ['vodka', 'amaretto'],
    ],
    [
        'name' => 'Amargo Obrero Julep', 'glass' => 'Vaso trago largo', 'ice' => 'Molido',
        'm' => $M_DIRM, 'garnish' => 'Menta', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'julep',
        'ing' => ['2 oz de Amargo Obrero', '1 oz de almíbar simple', '1 oz de jugo de limón', 'Puñado de menta', 'Completar con soda'],
        'tags' => ['amargo-obrero', 'menta', 'citrico'],
    ],
    [
        'name' => 'Pombero Julep', 'glass' => 'Vaso trago largo', 'ice' => 'Molido',
        'm' => $M_DIRM, 'garnish' => 'Menta', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'julep',
        'ing' => ['2 oz de Vermut rojo (Pombero)', '1 oz de almíbar simple', '1 oz de jugo de pomelo', 'Puñado de menta', 'Completar con soda'],
        'tags' => ['vermut', 'menta', 'pomelo'],
    ],
    [
        'name' => "Pinn's Julep", 'glass' => 'Vaso trago largo', 'ice' => 'Molido',
        'm' => $M_DIRM, 'garnish' => 'Menta', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'julep',
        'ing' => ["2 oz de Pinn's", '1 oz de almíbar simple', '1 oz de jugo de pomelo', 'Puñado de menta', 'Completar con soda'],
        'tags' => ['pimms', 'menta', 'pomelo'],
    ],
    [
        'name' => 'Cynar Julep Clásico', 'glass' => 'Vaso trago largo', 'ice' => 'Molido',
        'm' => $M_DIRM, 'garnish' => 'Menta', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'julep',
        'ing' => ['2 oz de Cynar', '1 oz de almíbar simple', '1 oz de jugo de limón', 'Completar con jugo de pomelo'],
        'tags' => ['cynar', 'menta', 'pomelo', 'citrico'],
    ],
    [
        'name' => 'Cynar Julep (rediseño)', 'glass' => 'Vaso trago largo', 'ice' => 'Molido',
        'm' => $M_DIRM, 'garnish' => 'Menta', 'vol' => 'long', 'mom' => 'all_day', 'fam' => 'julep',
        'ing' => ['2 oz de Cynar', '1 oz de almíbar simple', '1 oz de jugo de limón', 'Completar con soda'],
        'tags' => ['cynar', 'menta', 'citrico'],
    ],
    [
        'name' => 'Reversión de la Margarita', 'glass' => 'Copa Cóctel', 'ice' => null,
        'm' => $M_BAT, 'garnish' => 'Piel de limón', 'vol' => 'short', 'mom' => null, 'fam' => 'sour',
        'ing' => ['2 oz de Tequila', '1.5 oz de almíbar de naranja', '0.5 oz de jugo de limón', '0.75 oz de jugo de naranja', '2 gotas de tabasco'],
        'tags' => ['tequila', 'citrico'],
    ],
    [
        'name' => 'Rodilla de Abeja Clásica', 'glass' => 'Vaso trago largo', 'ice' => null,
        'm' => $M_BATD, 'garnish' => 'Rodaja de limón', 'vol' => 'long', 'mom' => null, 'fam' => 'sour',
        'desc' => "Bee's Knees clásico.",
        'ing' => ['2 oz de Gin', '1 oz de almíbar de miel', '1 oz de jugo de limón'],
        'tags' => ['gin', 'citrico'],
    ],
];

/* ---------- emitir SQL ---------- */

$allTags = [];
foreach ($recipes as $r) {
    foreach ($r['tags'] as $t) {
        $allTags[$t] = mb_convert_case(str_replace('-', ' ', $t), MB_CASE_TITLE, 'UTF-8');
    }
}
ksort($allTags);

$out = [];
$out[] = '-- =====================================================================';
$out[] = '--  Import: cocteles de autor e internacionales clasicos 2026';
$out[] = '--  GENERADO por sql/import/build_import_autor.php';
$out[] = '--  Seguro de re-correr. Requiere migraciones 01-05 aplicadas.';
$out[] = '-- =====================================================================';
$out[] = '';
$out[] = 'SET NAMES utf8mb4;';
$out[] = 'START TRANSACTION;';
$out[] = '';
$out[] = '-- tags que puedan faltar';
$out[] = 'INSERT IGNORE INTO tags (name, slug) VALUES';
$vals = [];
foreach ($allTags as $slug => $name) {
    $vals[] = sprintf('  (%s, %s)', q($name), q($slug));
}
$out[] = implode(",\n", $vals) . ';';
$out[] = '';

foreach ($recipes as $r) {
    $slug = slugify($r['name']);
    [$method, $detail] = $r['m'];
    $famSel = $r['fam'] === null ? 'NULL' : '(SELECT id FROM families WHERE slug = ' . q($r['fam']) . ')';

    $out[] = "-- ---------- {$r['name']} ----------";
    $out[] = 'INSERT INTO recipes'
        . ' (name, slug, glassware, ice, method, method_other, method_detail, volume, moment, family_id, garnish, description)';
    $out[] = 'SELECT ' . implode(', ', [
        q($r['name']), q($slug), q($r['glass']), q($r['ice']),
        q($method), 'NULL', q($detail), q($r['vol']), q($r['mom'] ?? null),
        $famSel, q($r['garnish']), q($r['desc'] ?? null),
    ]);
    $out[] = "FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM recipes WHERE slug = " . q($slug) . ");";

    // ingredientes
    $rows = [];
    $pos = 0;
    foreach ($r['ing'] as $line) {
        [$raw, $amount, $unit] = ing($line);
        $pos++;
        $rows[] = sprintf('    SELECT %s AS raw, %s AS amount, %s AS unit, %d AS pos',
            q($raw), num($amount), q($unit), $pos);
    }
    $out[] = 'INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position)';
    $out[] = 'SELECT r.id, x.raw, x.amount, x.unit, x.pos FROM recipes r JOIN (';
    $out[] = implode("\n    UNION ALL\n", $rows);
    $out[] = ') x WHERE r.slug = ' . q($slug)
        . ' AND NOT EXISTS (SELECT 1 FROM recipe_ingredients ri WHERE ri.recipe_id = r.id);';

    // tags
    $tagList = implode(', ', array_map('q', $r['tags']));
    $out[] = 'INSERT IGNORE INTO recipe_tags (recipe_id, tag_id)';
    $out[] = "SELECT r.id, t.id FROM recipes r JOIN tags t ON t.slug IN ($tagList) WHERE r.slug = " . q($slug) . ';';
    $out[] = '';
}

$out[] = 'COMMIT;';
$out[] = '';

$OUT = dirname(__DIR__) . '/import_autor_2026.sql';
file_put_contents($OUT, implode("\n", $out));

fwrite(STDERR, sprintf("OK -> %s\n  %d recetas, %d tags\n", $OUT, count($recipes), count($allTags)));
