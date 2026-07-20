<?php

use App\Support\MediaPath;

if (! function_exists('media_url')) {
    /**
     * Public short URL for a file stored under storage/app/public/{folder}.
     * Example: media_url('testimonials', 'clip.mp4') → /m/t/clip.mp4
     */
    function media_url(?string $folder, ?string $filename): ?string
    {
        if ($folder === null || $folder === '' || $filename === null || $filename === '') {
            return null;
        }

        $code = MediaPath::codeForFolder($folder);
        if ($code === null) {
            return asset('storage/'.trim($folder, '/').'/'.ltrim($filename, '/'));
        }

        return route('media.show', [
            'type' => $code,
            'file' => basename($filename),
        ]);
    }
}
