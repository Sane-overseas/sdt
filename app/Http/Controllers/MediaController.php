<?php

namespace App\Http\Controllers;

use App\Support\MediaPath;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class MediaController extends BaseController
{
    public function show(Request $request, string $type, string $file)
    {
        $folder = MediaPath::folderForCode($type);
        if ($folder === null) {
            abort(404);
        }

        $file = basename($file);
        if ($file === '' || $file === '.' || $file === '..') {
            abort(404);
        }

        $disk = Storage::disk('public');
        $relative = $this->resolveRelativePath($folder, $file, $disk);

        if ($relative === null || ! $disk->exists($relative)) {
            abort(404);
        }

        // Prevent path traversal outside public disk root
        $absolute = realpath($disk->path($relative));
        $root = realpath($disk->path(''));
        if ($absolute === false || $root === false || ! str_starts_with($absolute, $root)) {
            abort(404);
        }

        $mime = $disk->mimeType($relative) ?: 'application/octet-stream';

        return response()->file($absolute, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function resolveRelativePath(string $folder, string $file, $disk): ?string
    {
        // 1) Short nested token: /m/i/a1b2c3d4e5f6
        $fromKey = MediaPath::pathFromKey($file);
        if ($fromKey !== null) {
            return $fromKey;
        }

        // 2) Legacy flat: images/file.jpg
        $legacy = $folder.'/'.$file;
        if ($disk->exists($legacy)) {
            return $legacy;
        }

        // 3) Fallback: find by basename under nested folders (old long links / lost index)
        return $this->findByBasename($disk->path(''), $file);
    }

    private function findByBasename(string $root, string $basename): ?string
    {
        if (! is_dir($root)) {
            return null;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $item) {
            if (! $item->isFile()) {
                continue;
            }
            if ($item->getFilename() !== $basename) {
                continue;
            }

            $full = $item->getPathname();
            $relative = ltrim(str_replace('\\', '/', substr($full, strlen($root))), '/');
            // Rebuild index for next time
            MediaPath::remember($relative);

            return $relative;
        }

        return null;
    }
}
