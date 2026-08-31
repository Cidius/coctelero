<?php
declare(strict_types=1);

namespace App;

require_once __DIR__ . '/helpers.php';

/**
 * Validacion y normalizacion de imagenes subidas desde el admin.
 * - Valida el tipo real con getimagesize() (no la extension).
 * - Redimensiona a un maximo y reescribe como WebP con GD.
 * - Guarda solo el nombre del archivo; la carpeta sale de config['uploads_dir'].
 */
final class Uploader
{
    private const MAX_DIM = 1400;          // lado mayor, en px
    private const WEBP_QUALITY = 82;
    private const MAX_BYTES = 12 * 1024 * 1024;

    /**
     * Procesa $_FILES['imagen']. Devuelve el nombre de archivo generado,
     * o null si no se subio nada. Lanza RuntimeException si algo falla.
     */
    public static function handle(array $file, string $slug): ?string
    {
        $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($err === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($err !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('La subida falló (código ' . $err . ').');
        }
        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new \RuntimeException('La imagen supera el máximo de 12 MB.');
        }
        $tmp = $file['tmp_name'] ?? '';
        if (!is_uploaded_file($tmp)) {
            throw new \RuntimeException('Archivo temporal inválido.');
        }

        $info = @getimagesize($tmp);
        if ($info === false) {
            throw new \RuntimeException('El archivo no es una imagen válida.');
        }
        [$w, $h] = $info;
        $type = $info[2]; // IMAGETYPE_*

        $src = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($tmp),
            IMAGETYPE_PNG  => imagecreatefrompng($tmp),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($tmp) : false,
            IMAGETYPE_GIF  => imagecreatefromgif($tmp),
            default        => false,
        };
        if (!$src) {
            throw new \RuntimeException('Formato no soportado. Usá JPG, PNG o WebP.');
        }

        // Escala manteniendo proporcion.
        $scale = min(1.0, self::MAX_DIM / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));

        $dst = imagecreatetruecolor($nw, $nh);
        // Fondo blanco para aplanar transparencias (PNG/GIF) al pasar a WebP.
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $nw, $nh, $white);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($src);

        $dir = self::dir();
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException('No se pudo crear la carpeta de imágenes.');
        }

        $name = $slug . '-' . substr(bin2hex(random_bytes(4)), 0, 8) . '.webp';
        $path = $dir . '/' . $name;

        $ok = function_exists('imagewebp')
            ? imagewebp($dst, $path, self::WEBP_QUALITY)
            : imagejpeg($dst, substr($path, 0, -5) . '.jpg', self::WEBP_QUALITY);
        imagedestroy($dst);

        if (!$ok) {
            throw new \RuntimeException('No se pudo guardar la imagen procesada.');
        }
        if (!function_exists('imagewebp')) {
            $name = substr($name, 0, -5) . '.jpg';
        }
        return $name;
    }

    /** Borra un archivo de imagen si existe. */
    public static function delete(?string $filename): void
    {
        if ($filename === null || $filename === '') {
            return;
        }
        // Defensa: solo el basename, nada de rutas.
        $filename = basename($filename);
        $path = self::dir() . '/' . $filename;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private static function dir(): string
    {
        $dir = (string) (config()['uploads_dir'] ?? (dirname(__DIR__) . '/uploads/recipes'));
        return rtrim($dir, '/\\');
    }
}
