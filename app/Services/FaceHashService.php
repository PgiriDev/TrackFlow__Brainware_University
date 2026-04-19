<?php

namespace App\Services;

class FaceHashService
{
    public static function hash(array $vector): string
    {
        // Normalize again (safety)
        $norm = sqrt(array_sum(array_map(fn($v) => $v * $v, $vector)));
        if ($norm == 0) {
            // Defensive: avoid division by zero
            throw new \InvalidArgumentException('Zero-length face vector');
        }
        $normalized = array_map(
            fn($v) => round($v / $norm, 6),
            $vector
        );

        $string = implode('|', $normalized);

        return hash('sha256', $string);
    }
}
