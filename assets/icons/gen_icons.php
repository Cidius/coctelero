<?php
declare(strict_types=1);

/**
 * Genera los iconos de la PWA con GD. Correr una vez:
 *   php assets/icons/gen_icons.php
 * Los PNG generados se commitean.
 */

$dir = __DIR__;
$BG = [0x14, 0x11, 0x0f]; // --bg
$FG = [0xe0, 0xa4, 0x58]; // --accent

/**
 * Dibuja una copa de martini centrada.
 * $pad = fraccion de margen (0.10 normal, 0.22 para "maskable").
 */
function make(int $size, float $pad, string $path, array $bg, array $fg): void
{
    $im = imagecreatetruecolor($size, $size);
    $cbg = imagecolorallocate($im, $bg[0], $bg[1], $bg[2]);
    $cfg = imagecolorallocate($im, $fg[0], $fg[1], $fg[2]);
    imagefilledrectangle($im, 0, 0, $size, $size, $cbg);

    $cx = $size / 2;
    $cy = $size / 2;
    $u  = ($size * (1 - 2 * $pad)) / 2;   // media base de la copa

    // Bowl (triangulo con vertice abajo)
    imagefilledpolygon($im, [
        (int) ($cx - $u), (int) ($cy - $u * 0.72),
        (int) ($cx + $u), (int) ($cy - $u * 0.72),
        (int) $cx,        (int) ($cy + $u * 0.34),
    ], $cfg);

    // Tallo
    $sw = max(2, (int) ($u * 0.07));
    imagefilledrectangle(
        $im,
        (int) ($cx - $sw), (int) ($cy + $u * 0.30),
        (int) ($cx + $sw), (int) ($cy + $u * 0.86),
        $cfg
    );

    // Base
    imagefilledrectangle(
        $im,
        (int) ($cx - $u * 0.58), (int) ($cy + $u * 0.86),
        (int) ($cx + $u * 0.58), (int) ($cy + $u * 1.00),
        $cfg
    );

    imagepng($im, $path, 9);
    imagedestroy($im);
    echo "  $path\n";
}

echo "Generando iconos PWA...\n";
make(512, 0.14, "$dir/icon-512.png", $BG, $FG);
make(192, 0.14, "$dir/icon-192.png", $BG, $FG);
make(512, 0.24, "$dir/icon-maskable-512.png", $BG, $FG);
make(180, 0.16, "$dir/apple-touch-icon.png", $BG, $FG);
make(32,  0.10, "$dir/favicon-32.png", $BG, $FG);
echo "OK\n";
