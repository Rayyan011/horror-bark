<?php

namespace App\Support;

class HorrorBarkThemeAssets
{
    public static function homeHero(): string
    {
        return static::storageUrl(
            'about/gallery/about-harbor-hero-01.png',
            'images/banner.webp',
        );
    }

    public static function storageUrl(string $path, ?string $fallbackAsset = null): string
    {
        if (file_exists(storage_path('app/public/'.ltrim($path, '/')))) {
            return asset('storage/'.ltrim($path, '/'));
        }

        return asset($fallbackAsset ?: ltrim($path, '/'));
    }
}
