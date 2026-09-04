<?php
declare(strict_types=1);

namespace App;

/**
 * Helpers de vista. Se incluyen desde los scripts de /public.
 */

require_once __DIR__ . '/Database.php';

/** Escape para HTML. */
function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Config cacheada. */
function config(): array
{
    return Database::loadConfig();
}

/** Activa la muestra de errores en dev. */
function boot_errors(): void
{
    $env = config()['env'] ?? 'prod';
    if ($env === 'dev') {
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', '0');
    }
}

/**
 * URL del sitio a partir de una ruta relativa.
 *
 * Si config['base_url'] esta seteada, se usa como prefijo (util para forzar
 * https o un host fijo, y para las URLs absolutas del SEO). Si no, se detecta
 * el host del request: cambiar de dominio no requiere tocar nada.
 * En CLI sin host, devuelve una ruta relativa a la raiz ("/...").
 */
function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');

    $base = rtrim((string) (config()['base_url'] ?? ''), '/');
    if ($base !== '') {
        return $base . $path;
    }

    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($host === '') {
        return $path; // CLI / sin request
    }

    $https = ($_SERVER['HTTPS'] ?? '') === 'on'
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;

    return ($https ? 'https' : 'http') . '://' . $host . $path;
}

/**
 * URL de un asset con ?v=<fecha del archivo> para invalidar la cache del
 * navegador en cada deploy. $path es relativo a la raiz del sitio.
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = dirname(__DIR__) . '/' . $path;
    $v = is_file($file) ? filemtime($file) : false;
    return url($path) . ($v !== false ? '?v=' . $v : '');
}

/** URL publica de la imagen de una receta (o null si no tiene). */
function recipe_image_url(?string $imagePath): ?string
{
    if ($imagePath === null || $imagePath === '') {
        return null;
    }
    // Se guarda solo el nombre del archivo.
    $dir = trim((string) (config()['uploads_url'] ?? '/uploads/recipes'), '/');
    return url($dir . '/' . ltrim($imagePath, '/'));
}

/** Etiquetas <head> de la PWA (manifest, iconos, theme-color, service worker). */
function pwa_head(): void
{
    $tags = [
        '<link rel="manifest" href="' . e(url('manifest.webmanifest')) . '">',
        '<meta name="theme-color" content="#14110f">',
        '<link rel="icon" type="image/png" href="' . e(url('assets/icons/favicon-32.png')) . '">',
        '<link rel="apple-touch-icon" href="' . e(url('assets/icons/apple-touch-icon.png')) . '">',
        '<meta name="apple-mobile-web-app-capable" content="yes">',
        '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">',
        '<meta name="apple-mobile-web-app-title" content="Cócteles">',
        '<script>if("serviceWorker" in navigator){addEventListener("load",function(){'
            . 'navigator.serviceWorker.register("' . e(url('sw.js')) . '").catch(function(){})})}</script>',
    ];
    echo implode("\n    ", $tags) . "\n";
}

/** Etiqueta legible del metodo. */
function method_label(string $method, ?string $methodOther = null): string
{
    return match ($method) {
        'integrado'          => 'Integrado',
        'refrescado_directo' => 'Refrescado / Directo',
        'batido'             => 'Batido',
        'machacado'          => 'Machacado',
        'frozen'             => 'Frozen',
        'otro'               => $methodOther !== null && $methodOther !== '' ? $methodOther : 'Otro',
        default              => ucfirst($method),
    };
}

/**
 * Lee parametros de tag de la query string.
 * Acepta ?tag=ron&tag=menta  y  ?tag=ron,menta  y  ?tag[]=ron
 *
 * @return list<string>
 */
function query_tags(array $get): array
{
    $raw = $get['tag'] ?? [];
    $items = is_array($raw) ? $raw : explode(',', (string) $raw);
    $out = [];
    foreach ($items as $it) {
        $slug = strtolower(trim((string) $it));
        if ($slug !== '' && preg_match('/^[a-z0-9-]{1,80}$/', $slug)) {
            $out[$slug] = $slug;
        }
    }
    return array_values($out);
}

/** Convierte un texto a slug (minusculas, sin acentos, separado por guiones). */
function slugify(string $s): string
{
    $s = mb_strtolower(trim($s), 'UTF-8');
    $s = str_replace('&', ' ', $s);
    $from = ['á','é','í','ó','ú','ü','ñ','à','è','ì','ò','ù','â','ê','î','ô','û','ç','½','¼','¾'];
    $to   = ['a','e','i','o','u','u','n','a','e','i','o','u','a','e','i','o','u','c','', '', ''];
    $s = str_replace($from, $to, $s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-');
}

/** Normaliza una unidad de medida a una forma canonica (o null). */
function normalize_unit(?string $u): ?string
{
    if ($u === null || trim($u) === '') {
        return null;
    }
    $u = mb_strtolower(rtrim(trim($u), '.'), 'UTF-8');
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
    return $map[$u] ?? null;
}

/**
 * Parsea una linea de ingrediente. raw_text conserva el texto completo;
 * amount/unit se extraen si la linea arranca con un numero.
 *
 * @return array{raw:string, amount:?float, unit:?string}
 */
function parse_ingredient_line(string $line): array
{
    $raw = trim($line);
    $amount = null;
    $unit = null;
    $s = preg_replace('/(\d),(\d)/', '$1.$2', $raw) ?? $raw;
    if (preg_match('/^(\d+(?:\.\d+)?)(?:\s*\/\s*\d+)?\s*\.?\s*(\p{L}+)?/u', $s, $m)) {
        $amount = (float) $m[1];
        $unit = normalize_unit($m[2] ?? null);
    }
    return ['raw' => $raw, 'amount' => $amount, 'unit' => $unit];
}

/** Redirige y corta la ejecucion. */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Devuelve la URL normalizada si es http/https valida, o null.
 * Tolera que falte el esquema ("iba-world.com/..." -> "https://...").
 */
function safe_url(?string $url): ?string
{
    $url = trim((string) $url);
    if ($url === '') {
        return null;
    }
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
}

/** Etiqueta legible a partir del dominio de una URL. */
function link_label_from_url(string $url): string
{
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    $host = preg_replace('/^www\./', '', $host) ?? $host;
    $known = [
        'iba-world.com'     => 'IBA',
        'instagram.com'     => 'Instagram',
        'youtube.com'       => 'YouTube',
        'youtu.be'          => 'YouTube',
        'tiktok.com'        => 'TikTok',
        'facebook.com'      => 'Facebook',
        'x.com'             => 'X',
        'twitter.com'       => 'X',
        'diffordsguide.com' => "Difford's Guide",
        'es.wikipedia.org'  => 'Wikipedia',
        'en.wikipedia.org'  => 'Wikipedia',
    ];
    return $known[$host] ?? ($host !== '' ? $host : 'Enlace');
}

/**
 * Parsea el textarea de enlaces del admin. Un enlace por linea:
 *   Etiqueta | https://...      (si falta la etiqueta se deduce del dominio)
 *
 * @return list<array{label:string, url:string}>
 */
function parse_links_text(string $text): array
{
    $out = [];
    foreach (preg_split('/\r\n|\r|\n/', $text) ?: [] as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        if (str_contains($line, '|')) {
            [$label, $rawUrl] = explode('|', $line, 2);
        } else {
            [$label, $rawUrl] = ['', $line];
        }
        $url = safe_url($rawUrl);
        if ($url === null) {
            continue;
        }
        $label = trim($label);
        if ($label === '') {
            $label = link_label_from_url($url);
        }
        $out[] = ['label' => mb_substr($label, 0, 80), 'url' => mb_substr($url, 0, 500)];
    }
    return $out;
}
