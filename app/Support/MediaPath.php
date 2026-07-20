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
}
