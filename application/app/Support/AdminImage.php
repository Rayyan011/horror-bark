<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class AdminImage
{
    public static function first(?array $images): ?string
    {
        $image = collect($images ?? [])->first(fn ($path) => filled($path));

        if (! $image) {
            return null;
        }

        return self::url($image);
    }

    public static function url(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return url($path);
        }

        return Storage::disk('public')->url(ltrim($path, '/'));
    }
}
