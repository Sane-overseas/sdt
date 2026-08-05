<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\School;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TrainerUploadPathService
{
    /**
     * Build: {state}/{district}/{block}/{school}/session({session})
     * Example: HP/Kangra/Dharamshala/GSSS_Dharamshala/session(2026-27)
     *
     * Trainer registration files stay under trainer_data/{code}_* — not this service.
     */
    public function directory(int $schoolId, string $type = '', ?int $sessionId = null): string
    {
        $school = School::with('district.state')->findOrFail($schoolId);
        $session = $sessionId
            ? AcademicSession::find($sessionId)
            : AcademicSessionService::active();

        $sessionLabel = $this->segment($session?->name ?? 'session');
        $state = $this->segment(
            $school->district?->state?->code
                ?: ($school->district?->state?->name ?? 'NA')
        );
        $district = $this->segment($school->district?->district ?? 'NA');
        $block = $this->segment($school->block ?? 'NA');
        $schoolName = $this->segment($school->school_name ?? 'NA');

        return implode('/', [
            $state,
            $district,
            $block,
            $schoolName,
            'session('.$sessionLabel.')',
        ]);
    }

    /**
     * Store upload and return relative path for DB (full path under public disk).
     */
    public function store(UploadedFile $file, int $schoolId, string $type, string $filename, ?int $sessionId = null): string
    {
        $dir = $this->directory($schoolId, $type, $sessionId);
        $safeName = $this->safeFilename(basename($filename));
        $file->storeAs($dir, $safeName, 'public');

        return $dir.'/'.$safeName;
    }

    private function safeFilename(string $filename): string
    {
        $filename = $this->segment($filename);
        if ($filename === '' || $filename === 'NA') {
            return 'file';
        }

        return $filename;
    }

    /**
     * Resolve DB value to a public-disk relative path (supports legacy flat filenames).
     */
    public static function resolve(?string $folder, ?string $stored): ?string
    {
        if ($stored === null || $stored === '') {
            return null;
        }

        $stored = str_replace('\\', '/', $stored);
        if (str_contains($stored, '/')) {
            return ltrim($stored, '/');
        }

        if ($folder === null || $folder === '') {
            return $stored;
        }

        return trim($folder, '/').'/'.ltrim($stored, '/');
    }

    public static function delete(?string $folder, ?string $stored): void
    {
        $path = self::resolve($folder, $stored);
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function segment(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[\\\\\\/\\:\\*\\?\\"\\<\\>\\|]+/', '_', $value) ?? $value;
        $value = preg_replace('/\\s+/', '_', $value) ?? $value;
        $value = trim($value, '._');

        return $value !== '' ? $value : 'NA';
    }
}
