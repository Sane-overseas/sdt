<?php

use App\Support\MediaPath;

if (! function_exists('media_url')) {
    /**
     * Short public URL for media. Files may live in nested school folders.
     * Examples:
     * - Legacy: media_url('images', 'file.jpg') → /m/i/file.jpg
     * - Nested: media_url('images', 'HP/.../images(2026-27)/file.jpg') → /m/i/a1b2c3d4e5f6
     */
    function media_url(?string $folder, ?string $filename): ?string
    {
        if ($folder === null || $folder === '' || $filename === null || $filename === '') {
            return null;
        }

        $path = MediaPath::diskPath($folder, $filename);
        if ($path === null) {
            return null;
        }

        $code = MediaPath::codeForFolder($folder);
        if ($code === null) {
            return asset('storage/'.$path);
        }

        // Flat legacy: keep readable short filename URLs
        if (MediaPath::isLegacyFlatPath($path)) {
            return route('media.show', [
                'type' => $code,
                'file' => basename($path),
            ]);
        }

        // Nested school/session path: short opaque token, disk folders unchanged
        return route('media.show', [
            'type' => $code,
            'file' => MediaPath::remember($path),
        ]);
    }
}
