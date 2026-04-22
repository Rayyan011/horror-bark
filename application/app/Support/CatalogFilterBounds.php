<?php

namespace App\Support;

final class CatalogFilterBounds
{
    public static function price(?float $rawMin, ?float $rawMax): array
    {
        return self::build(
            $rawMin,
            $rawMax,
            0,
            fn (int $value): int => match (true) {
                $value <= 50 => 10,
                $value <= 100 => 25,
                $value <= 250 => 50,
                $value <= 500 => 100,
                $value <= 1_000 => 150,
                $value <= 2_500 => 250,
                $value <= 5_000 => 500,
                $value <= 10_000 => 1_000,
                default => 2_000,
            },
            fn (int $value): int => match (true) {
                $value <= 100 => 5,
                $value <= 250 => 10,
                $value <= 500 => 25,
                $value <= 2_500 => 50,
                $value <= 5_000 => 100,
                $value <= 10_000 => 250,
                default => 500,
            },
        );
    }

    public static function quantity(?float $rawMin, ?float $rawMax, int $floor = 1): array
    {
        return self::build(
            $rawMin,
            $rawMax,
            $floor,
            fn (int $value): int => match (true) {
                $value <= 5 => 1,
                $value <= 10 => 2,
                $value <= 25 => 5,
                $value <= 50 => 10,
                $value <= 100 => 20,
                $value <= 250 => 50,
                default => 100,
            },
            fn (int $value): int => match (true) {
                $value <= 25 => 1,
                $value <= 50 => 2,
                $value <= 100 => 5,
                $value <= 250 => 10,
                default => 25,
            },
        );
    }

    public static function normalizeSingle(array $bounds, mixed $value, bool $integer = true): int|float
    {
        $fallback = $bounds['min'];

        if ($value === null || $value === '') {
            return $integer ? (int) $fallback : (float) $fallback;
        }

        $normalized = max($bounds['min'], min($bounds['max'], $integer ? (int) round((float) $value) : (float) $value));

        return $integer ? (int) $normalized : (float) $normalized;
    }

    public static function normalizeRange(array $bounds, mixed $minValue, mixed $maxValue): array
    {
        $min = self::normalizeSingle($bounds, $minValue === null || $minValue === '' ? $bounds['min'] : $minValue);
        $max = self::normalizeSingle($bounds, $maxValue === null || $maxValue === '' ? $bounds['max'] : $maxValue);

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        return [
            'min' => (int) $min,
            'max' => (int) $max,
        ];
    }

    public static function isDefaultRange(array $bounds, int|float $min, int|float $max): bool
    {
        return (float) $min <= (float) $bounds['min'] && (float) $max >= (float) $bounds['max'];
    }

    private static function build(
        ?float $rawMin,
        ?float $rawMax,
        int $floor,
        callable $paddingResolver,
        callable $stepResolver,
    ): array {
        if ($rawMin === null || $rawMax === null) {
            return [
                'min' => $floor,
                'max' => $floor + max(10, $floor === 0 ? 100 : 10),
                'step' => $floor === 0 ? 10 : 1,
            ];
        }

        $minValue = max($floor, (int) floor($rawMin));
        $maxValue = max($minValue, (int) ceil($rawMax));

        $lowerPadding = $paddingResolver(max($floor, $minValue));
        $upperPadding = $paddingResolver($maxValue);
        $step = max(1, $stepResolver($maxValue));

        $min = max($floor, (int) floor(($minValue - $lowerPadding) / $step) * $step);
        $max = (int) ceil(($maxValue + $upperPadding) / $step) * $step;

        if ($max <= $min) {
            $max = $min + $step;
        }

        return [
            'min' => $min,
            'max' => $max,
            'step' => $step,
        ];
    }
}
