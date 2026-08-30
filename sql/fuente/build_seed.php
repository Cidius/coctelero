<?php
declare(strict_types=1);

/**
 * Generador del seed de las 52 recetas.
 *
 *   php sql/fuente/build_seed.php
 *
 * Lee  sql/fuente/recetario.txt  (texto extraido del Word del taller)
 * y escribe  sql/seed_52_recetas.sql .
 *
 * El .sql generado se commitea. Este script queda como registro de COMO
 * se mapeo cada campo, para poder regenerarlo si cambia la fuente.
 *
 * Mapeo:
 *   "N. Nombre"      -> recipes.name / slug (autogenerado)
 *   Ingredientes:    -> recipe_ingredients (split por coma / " y ", con
 *                       amount+unit parseados cuando se puede; raw_text
 *                       siempre conserva el texto completo del fragmento)
 *   Cristaleria:     -> recipes.glassware
 *   Hielo:           -> recipes.ice           ("-" / "—" => NULL)
 *   Metodo:          -> recipes.method (ENUM normalizado)
 *                       + recipes.method_detail (tecnica textual completa)
 *   Decoracion:      -> recipes.garnish  (todo lo que sigue a "Nota:" pasa
 *                       a recipes.description)
 *   Tags de destilado/caracteristica: derivados por palabras clave de los
 *   ingredientes. Es una primera pasada; el admin los corrige despues.
 */

$SRC = __DIR__ . '/recetario.txt';
$OUT = dirname(__DIR__) . '/seed_52_recetas.sql';

if (!is_file($SRC)) {
    fwrite(STDERR, "No se encuentra $SRC\n");
    exit(1);
}

mb_internal_encoding('UTF-8');

$lines = preg_split('/\R/u', (string) file_get_contents($SRC));

/* ---------- parseo de bloques ---------- */
$recipes = [];
$cur = null;
$labels = [
    'Ingredientes' => 'ingredients',
    'Cristaleria'  => 'glassware',
    'Cristalería'  => 'glassware',
    'Hielo'        => 'ice',
    'Metodo'       => 'method',
    'Método'       => 'method',
    'Decoracion'   => 'garnish',
    'Decoración'   => 'garnish',
];

foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    if (preg_match('/^(\d{1,2})\.\s+(\S.*)$/u', $line, $m)) {
        if ($cur !== null) {
            $recipes[] = $cur;
        }
        $cur = ['num' => (int) $m[1], 'name' => trim($m[2])];
        continue;
    }
    if ($cur === null) {
        continue; // encabezado del archivo
    }
    if (preg_match('/^([A-Za-zÁÉÍÓÚáéíóú]+):\s*(.*)$/u', $line, $m) && isset($labels[$m[1]])) {
        $cur[$labels[$m[1]]] = trim($m[2]);
    } else {
        // continuacion de la linea anterior (rara vez pasa en esta fuente)
        $keys = array_keys($cur);
        $last = end($keys);
        if (is_string($cur[$last] ?? null)) {
            $cur[$last] .= ' ' . $line;
        }
    }
}
if ($cur !== null) {
    $recipes[] = $cur;
}

fwrite(STDERR, count($recipes) . " recetas parseadas\n");

/* ---------- helpers ---------- */

function slugify(string $s): string
{
    $s = mb_strtolower(trim($s));
    $from = ['á','é','í','ó','ú','ü','ñ','à','è','ì','ò','ù','â','ê','î','ô','û','ç','½','¼','¾','&'];
    $to   = ['a','e','i','o','u','u','n','a','e','i','o','u','a','e','i','o','u','c','' ,'' ,'' ,'y'];
    $s = str_replace($from, $to, $s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-');
}

function norm(string $s): string
{
    $s = mb_strtolower($s);
    $from = ['á','é','í','ó','ú','ü','ñ','à','è','ì','ò','ù','â','ê','î','ô','û','ç'];
    $to   = ['a','e','i','o','u','u','n','a','e','i','o','u','a','e','i','o','u','c'];
    return str_replace($from, $to, $s);
}

function clean_field(?string $s): ?string
{
    if ($s === null) {
        return null;
    }
    $s = trim(rtrim(trim($s), '.'));
    $low = mb_strtolower($s);
    if ($s === '' || in_array($low, ['-', '—', '–', 'no lleva', 'no aplica'], true)) {
        return null;
    }
    return $s;
}

function map_method(string $detail): array
{
    $d = trim(rtrim(trim($detail), '.'));
    $lc = norm($d);
    if (str_contains($lc, 'machacado') || str_contains($lc, 'machacada')) {
        $enum = 'machacado';
    } elseif (str_contains($lc, 'frozen') || str_contains($lc, 'licuadora')) {
        $enum = 'frozen';
    } elseif (str_contains($lc, 'integrado')) {
        $enum = 'integrado';
    } elseif (str_contains($lc, 'refrescado') || str_contains($lc, 'directo')) {
        $enum = 'refrescado_directo';
    } elseif (str_contains($lc, 'batido') || str_contains($lc, 'batida')) {
        $enum = 'batido';
    } else {
        $enum = 'otro';
    }
    return [$enum, $d === '' ? null : $d];
}

function norm_unit(?string $u): ?string
{
    if ($u === null || $u === '') {
        return null;
    }
    $u = mb_strtolower(rtrim($u, '.'));
    $map = [
        'ml' => 'ml', 'cl' => 'cl', 'cc' => 'cc', 'oz' => 'oz',
        'cda' => 'cda', 'cucharada' => 'cda', 'cucharadas' => 'cda',
        'cdta' => 'cdta', 'cucharadita' => 'cdta', 'cucharaditas' => 'cdta',
        'bsp' => 'bsp',
        'dash' => 'dash', 'dashes' => 'dash',
        'gota' => 'gota', 'gotas' => 'gota',
        'hoja' => 'hoja', 'hojas' => 'hoja',
        'rodaja' => 'rodaja', 'rodajas' => 'rodaja',
        'lamina' => 'lamina', 'laminas' => 'lamina',
        'unidad' => 'unidad', 'unidades' => 'unidad',
        'parte' => 'parte', 'partes' => 'parte',
    ];
    return $map[norm($u)] ?? null;
}

/**
 * @return list<array{raw:string,amount:?float,unit:?string}>
 */
function parse_ingredients(string $text): array
{
    $s = trim(rtrim(trim($text), '.'));

    // Conectores finales -> coma, para poder splitear parejo.
    $s = preg_replace('/\s*\.\s*Se completa con\s+/iu', ', completar con ', $s) ?? $s;
    $s = preg_replace('/\s+y\s+(?:se\s+)?completa(?:r)? con\s+/iu', ', completar con ', $s) ?? $s;
    $s = preg_replace('/\s*\.\s*Opcional:\s*/iu', ', opcional: ', $s) ?? $s;
    // Fracciones tipo "1 y 1/2" / "1 y ½"
    $s = preg_replace('/(\d)\s*y\s*½/u', '$1.5', $s) ?? $s;
    $s = preg_replace('/(\d)\s*y\s*¼/u', '$1.25', $s) ?? $s;
    $s = preg_replace('/(\d)\s*y\s*¾/u', '$1.75', $s) ?? $s;
    $s = str_replace(['½', '¼', '¾'], ['0.5', '0.25', '0.75'], $s);

    $parts = preg_split('/\s*,\s*/u', $s) ?: [];
    // El ultimo fragmento suele traer " y " uniendo dos ingredientes.
    $last = array_pop($parts);
    if ($last !== null) {
        if (preg_match('/^(.*\S)\s+y\s+(\S.*)$/u', $last, $mm)) {
            $parts[] = $mm[1];
            $parts[] = $mm[2];
        } else {
            $parts[] = $last;
        }
    }

    $out = [];
    foreach ($parts as $p) {
        $raw = trim($p);
        if ($raw === '') {
            continue;
        }
        $raw = preg_replace('/^completar con\s+/iu', 'Completar con ', $raw) ?? $raw;
        $raw = preg_replace('/^opcional:\s*/iu', 'Opcional: ', $raw) ?? $raw;

        $amount = null;
        $unit = null;
        if (preg_match('/^(\d+(?:[.,]\d+)?)(?:\s*\/\s*\d+)?\s*\.?\s*([A-Za-zÁÉÍÓÚáéíóú]+)?\b/u', $raw, $mm)) {
            $amount = (float) str_replace(',', '.', $mm[1]);
            $unit = norm_unit($mm[2] ?? null);
        }
        $out[] = ['raw' => $raw, 'amount' => $amount, 'unit' => $unit];
    }
    return $out;
}

/** Tags por palabra clave sobre el texto de ingredientes + nombre. */
function derive_tags(string $name, string $ingredients): array
{
    $hay = norm($name . ' || ' . $ingredients);
    $rules = [
        'gin'            => ['gin'],
        'ron'            => ['ron blanco', 'ron anejo', ' ron ', ' ron,', ' ron.', 'de ron'],
        'cachaca'        => ['cachaca'],
        'vodka'          => ['vodka'],
        'campari'        => ['campari'],
        'aperol'         => ['aperol'],
        'fernet'         => ['fernet'],
        'cynar'          => ['cynar'],
        'pineral'        => ['pineral'],
        'hesperidina'    => ['hesperidina'],
        'hierro-quina'   => ['hierro quina'],
        'vermut'         => ['vermu', 'vermouth', 'vermut'],
        'whisky'         => ['whisky', 'whiskey', 'bourbon', 'scotch', 'single malt'],
        'tequila'        => ['tequila'],
        'mezcal'         => ['mezcal'],
        'pisco'          => ['pisco'],
        'brandy'         => ['brandy'],
        'apricot-brandy' => ['apricot'],
        'cherry-brandy'  => ['cherry brandy', 'licor marrasquino', 'marrasquino', 'marraschino'],
        'espumante'      => ['espumante', 'champana', 'champagne', 'prosecco', 'espumoso'],
        'triple-sec'     => ['triple sec', 'grand marnier', 'lemoncello', 'limoncello'],
        'amargo-obrero'  => ['amargo obrero'],
        'chartreuse'     => ['chartreuse'],
        'strega'         => ['strega'],
        'cassis'         => ['cassis'],
        'malibu'         => ['malibu'],
        'pimms'          => ["pimm"],
        'licor-cafe'     => ['licor de cafe'],
        'licor-crema'    => ['baileys', 'amarula', "sheridan", 'licor cremoso', 'crema de'],
        'angostura'      => ['angostura'],
        'menta'          => ['hojas de menta', 'de menta'],
        'albahaca'       => ['albahaca'],
        'jengibre'       => ['jengibre'],
        'pomelo'         => ['pomelo'],
        'cafe'           => ['cafe'],
        'frutilla'       => ['frutilla'],
        'con-huevo'      => ['clara de huevo', 'clara'],
        'citrico'        => ['jugo de limon', 'jugo de lima', 'citricos'],
    ];
    $tags = [];
    foreach ($rules as $tag => $needles) {
        foreach ($needles as $n) {
            if (str_contains($hay, $n)) {
                $tags[$tag] = true;
                break;
            }
        }
    }
    // Sin destilado detectado + limonada => sin alcohol
    $spirits = ['gin','ron','cachaca','vodka','campari','aperol','fernet','cynar','pineral',
        'hesperidina','hierro-quina','vermut','whisky','tequila','mezcal','pisco','brandy',
        'apricot-brandy','cherry-brandy','espumante','triple-sec','amargo-obrero','chartreuse',
        'strega','cassis','malibu','pimms','licor-cafe','licor-crema'];
    if (!array_intersect(array_keys($tags), $spirits) && str_contains($hay, 'limonada')) {
        $tags['sin-alcohol'] = true;
    }
    return array_keys($tags);
}

/* ---------- construir filas ---------- */

$seenSlug = [];
$allTags = [];   // slug => name
$rows = [];

foreach ($recipes as $r) {
    $name = $r['name'];
    $slug = slugify($name);
    if (isset($seenSlug[$slug])) {
        $slug .= '-' . (++$seenSlug[$slug]);
    } else {
        $seenSlug[$slug] = 1;
    }

    [$methodEnum, $methodDetail] = map_method($r['method'] ?? '');

    $garnishRaw = $r['garnish'] ?? '';
    $description = null;
    if (preg_match('/^(.*?)(?:\.\s*)?Nota:\s*(.+)$/su', $garnishRaw, $mm)) {
        $garnishRaw = trim($mm[1]);
        $description = 'Nota: ' . trim($mm[2]);
    }

    $ingText = $r['ingredients'] ?? '';

    $rows[] = [
        'id'            => $r['num'],
        'name'          => $name,
        'slug'          => $slug,
        'glassware'     => clean_field($r['glassware'] ?? null),
        'ice'           => clean_field($r['ice'] ?? null),
        'method'        => $methodEnum,
        'method_detail' => $methodDetail,
        'garnish'       => clean_field($garnishRaw),
        'description'   => $description,
        'ingredients'   => parse_ingredients($ingText),
        'tags'          => derive_tags($name, $ingText),
    ];

    foreach ($rows[count($rows) - 1]['tags'] as $t) {
        $allTags[$t] = mb_convert_case(str_replace('-', ' ', $t), MB_CASE_TITLE, 'UTF-8');
    }
}

/* ---------- emitir SQL ---------- */

function q(?string $v): string
{
    return $v === null ? 'NULL' : "'" . str_replace("'", "''", $v) . "'";
}
function n(?float $v): string
{
    if ($v === null) {
        return 'NULL';
    }
    return rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.') ?: '0';
}

ksort($allTags);
$tagId = [];
$i = 0;
foreach ($allTags as $slug => $name) {
    $tagId[$slug] = ++$i;
}

$out = [];
$out[] = '-- =====================================================================';
$out[] = '--  Seed: 52 recetas del Taller de Cocteleria';
$out[] = '--  GENERADO por sql/fuente/build_seed.php - no editar a mano.';
$out[] = '--  Regenerar:  php sql/fuente/build_seed.php';
$out[] = '--  Requiere haber corrido antes sql/schema.sql';
$out[] = '-- =====================================================================';
$out[] = '';
$out[] = 'SET NAMES utf8mb4;';
$out[] = 'START TRANSACTION;';
$out[] = '';
$out[] = 'DELETE FROM recipe_tags;';
$out[] = 'DELETE FROM recipe_ingredients;';
$out[] = 'DELETE FROM recipe_topics;';
$out[] = 'DELETE FROM recipes;';
$out[] = 'DELETE FROM tags;';
$out[] = '';

$out[] = '-- ---------- tags ----------';
$out[] = 'INSERT INTO tags (id, name, slug) VALUES';
$vals = [];
foreach ($allTags as $slug => $name) {
    $vals[] = sprintf('  (%d, %s, %s)', $tagId[$slug], q($name), q($slug));
}
$out[] = implode(",\n", $vals) . ';';
$out[] = '';

$out[] = '-- ---------- recipes ----------';
$out[] = 'INSERT INTO recipes'
    . ' (id, name, slug, glassware, ice, method, method_other, method_detail, garnish, description)'
    . ' VALUES';
$vals = [];
foreach ($rows as $r) {
    $vals[] = sprintf(
        '  (%d, %s, %s, %s, %s, %s, NULL, %s, %s, %s)',
        $r['id'],
        q($r['name']),
        q($r['slug']),
        q($r['glassware']),
        q($r['ice']),
        q($r['method']),
        q($r['method_detail']),
        q($r['garnish']),
        q($r['description'])
    );
}
$out[] = implode(",\n", $vals) . ';';
$out[] = '';

$out[] = '-- ---------- recipe_ingredients ----------';
$vals = [];
foreach ($rows as $r) {
    $pos = 0;
    foreach ($r['ingredients'] as $ing) {
        $vals[] = sprintf(
            '  (%d, %s, %s, %s, %d)',
            $r['id'],
            q($ing['raw']),
            n($ing['amount']),
            q($ing['unit']),
            ++$pos
        );
    }
}
$out[] = 'INSERT INTO recipe_ingredients (recipe_id, raw_text, amount, unit, position) VALUES';
$out[] = implode(",\n", $vals) . ';';
$out[] = '';

$out[] = '-- ---------- recipe_tags ----------';
$vals = [];
foreach ($rows as $r) {
    foreach ($r['tags'] as $t) {
        $vals[] = sprintf('  (%d, %d)', $r['id'], $tagId[$t]);
    }
}
$out[] = 'INSERT INTO recipe_tags (recipe_id, tag_id) VALUES';
$out[] = implode(",\n", $vals) . ';';
$out[] = '';

$out[] = 'COMMIT;';
$out[] = '';
$out[] = '-- Topics: se dejan SIN cargar en esta etapa (decision del plan v2).';
$out[] = '-- Propuesta para habilitar mas adelante:';
$out[] = "--   INSERT INTO topics (name, slug) VALUES";
$out[] = "--     ('Clasicos','clasicos'),";
$out[] = "--     ('Refrescantes / Verano','refrescantes-verano'),";
$out[] = "--     ('Batidos con huevo','batidos-con-huevo'),";
$out[] = "--     ('Frozen','frozen'),";
$out[] = "--     ('Machacados','machacados'),";
$out[] = "--     ('Con espuma / Champagne','con-espuma-champagne'),";
$out[] = "--     ('Amargos / Aperitivos','amargos-aperitivos');";
$out[] = '';

file_put_contents($OUT, implode("\n", $out));

fwrite(STDERR, sprintf(
    "OK -> %s\n  %d recetas, %d tags, %d filas de ingredientes\n",
    $OUT,
    count($rows),
    count($allTags),
    array_sum(array_map(fn($r) => count($r['ingredients']), $rows))
));
