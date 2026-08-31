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

/** URL absoluta a partir de una ruta relativa. */
function url(string $path = ''): string
{
    $base = rtrim((string) (config()['base_url'] ?? ''), '/');
    return $base . '/' . ltrim($path, '/');
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
