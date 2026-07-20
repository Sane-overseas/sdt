<?php

namespace App\Http\Controllers;

use App\Support\MediaPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{

    public function show(Request $request, string $type, string $file)
    {
        $folder = MediaPath::folderForCode($type);
        if ($folder === null) {
            abort(404);
        }

        // Prevent path traversal — filename only
        $file = basename($file);
        if ($file === '' || $file === '.' || $file === '..') {
            abort(404);
        }

        $relative = $folder.'/'.$file;
        $disk = Storage::disk('public');

        if (! $disk->exists($relative)) {
            abort(404);
        }

        $absolute = $disk->path($relative);
        $mime = $disk->mimeType($relative) ?: 'application/octet-stream';

        return response()->file($absolute, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
