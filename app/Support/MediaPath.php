<?php

namespace App\Support;

class MediaPath
{
    public const FOLDERS = [
        't' => 'testimonials',
        'v' => 'videos',
        'i' => 'images',
        'c' => 'completion',
        'd' => 'distribution',
        'k' => 'certificates',
        'l' => 'logos',
        'r' => 'trainer_data',
    ];

    public static function folderForCode(string $code): ?string
    {
        return self::FOLDERS[$code] ?? null;
    }

    public static function codeForFolder(string $folder): ?string
    {
        $folder = trim($folder, '/');
        $map = array_flip(self::FOLDERS);

        return $map[$folder] ?? null;
    }

    /**
     * DB may store legacy filename OR full relative path under public disk.
     */
    public static function diskPath(string $folder, ?string $stored): ?string
    {
        if ($stored === null || $stored === '') {
            return null;
        }

        $stored = str_replace('\\', '/', $stored);
        if (str_contains($stored, '/')) {
            return ltrim($stored, '/');
        }

        return trim($folder, '/').'/'.ltrim($stored, '/');
    }

    /**
     * Nested school paths get a short token → /m/i/a1b2c3d4e5f6
     * Mapping lives in storage/app/media-index (folders on public disk stay nested).
     */
    public static function shortKey(string $relativePath): string
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

        return substr(hash('sha256', $relativePath), 0, 12);
    }

    public static function remember(string $relativePath): string
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $key = self::shortKey($relativePath);
        $dir = storage_path('app/media-index/'.substr($key, 0, 2));
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file = $dir.'/'.$key;
        if (! is_file($file)) {
            file_put_contents($file, $relativePath);
        }

        return $key;
    }

    public static function pathFromKey(string $key): ?string
    {
        if (! preg_match('/^[a-f0-9]{12}$/', $key)) {
            return null;
        }

        $file = storage_path('app/media-index/'.substr($key, 0, 2).'/'.$key);
        if (! is_file($file)) {
            return null;
        }

        $path = trim((string) file_get_contents($file));

        return $path !== '' ? $path : null;
    }

    /**
     * True when path is flat legacy: images/file.jpg
     */
    public static function isLegacyFlatPath(string $path): bool
    {
        $parts = explode('/', ltrim($path, '/'));

        return count($parts) === 2 && self::codeForFolder($parts[0]) !== null;
    }
}
