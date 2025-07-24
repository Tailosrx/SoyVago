<?php
function ajustarBrillo($hex, $pasos) {
    // Convierte hex a RGB
    $r = hexdec(substr($hex, 1, 2));
    $g = hexdec(substr($hex, 3, 2));
    $b = hexdec(substr($hex, 5, 2));

    // Ajusta el brillo
    $r = max(0, min(255, $r + $pasos));
    $g = max(0, min(255, $g + $pasos));
    $b = max(0, min(255, $b + $pasos));

    // Vuelve a hex
    return '#'.str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
          .str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
          .str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}