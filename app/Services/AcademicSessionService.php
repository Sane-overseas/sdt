<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AcademicSessionService
{
    private static bool $bypassScope = false;

    public static function bypassScope(bool $bypass = true): void
    {
        self::$bypassScope = $bypass;
    }

    public static function isBypassingScope(): bool
    {
        return self::$bypassScope;
    }

    public static function current(): ?AcademicSession
    {
        if (Auth::check() && (int) Auth::user()->role === 1) {
            $viewId = Session::get('view_academic_session_id');
            if ($viewId) {
                return AcademicSession::find($viewId);
            }
        }

        return AcademicSession::where('is_active', true)->first()
            ?? AcademicSession::orderByDesc('id')->first();
    }

    public static function currentId(): ?int
    {
        return self::current()?->id;
    }

    public static function activeId(): ?int
    {
        return self::active()?->id;
    }

    /** Session used for reading data (admin may view archive). */
    public static function scopeSessionId(): ?int
    {
        if (Auth::check() && (int) Auth::user()->role === 1) {
            return self::currentId();
        }

        return self::activeId();
    }

    /** Session used when creating assignments/uploads — always active. */
    public static function assignmentSessionId(): ?int
    {
        return self::activeId();
    }

    public static function resetSchoolMasterFlags(): void
    {
        School::query()->update([
            'asigned_school' => 0,
            'status' => 0,
            'image_status' => 0,
            'video_status' => 0,
            'completion_status' => 0,
            'distribution_status' => 0,
            'paid_status' => 0,
        ]);
    }

    public static function active(): ?AcademicSession
    {
        return AcademicSession::where('is_active', true)->first();
    }

    public static function all()
    {
        return AcademicSession::orderByDesc('id')->get();
    }

    public static function setViewingSessionId(?int $sessionId): void
    {
        if ($sessionId) {
            Session::put('view_academic_session_id', $sessionId);
        } else {
            Session::forget('view_academic_session_id');
        }
    }

    public static function isArchiveView(): bool
    {
        $current = self::current();
        $active = self::active();

        if (!$current) {
            return false;
        }

        if ($active && (int) $current->id !== (int) $active->id) {
            return true;
        }

        return $current->status === 'closed';
    }

    public static function isWritableContext(): bool
    {
        $current = self::current();
        $active = self::active();

        if (!$current || !$active) {
            return false;
        }

        return (int) $current->id === (int) $active->id && $current->status === 'active';
    }

    public static function createNew(string $name, ?string $startDate = null, ?string $endDate = null): AcademicSession
    {
        AcademicSession::where('is_active', true)->update([
            'is_active' => false,
            'status' => 'closed',
        ]);

        $session = AcademicSession::create([
            'name' => $name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => true,
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        self::setViewingSessionId(null);
        self::resetSchoolMasterFlags();

        return $session;
    }

    public static function activate(int $sessionId): AcademicSession
    {
        AcademicSession::query()->update([
            'is_active' => false,
            'status' => 'closed',
        ]);

        $session = AcademicSession::findOrFail($sessionId);
        $session->update([
            'is_active' => true,
            'status' => 'active',
        ]);

        self::setViewingSessionId(null);
        SchoolAssignmentService::syncAllSchoolAssignedFlags();

        return $session;
    }

    public static function close(int $sessionId): AcademicSession
    {
        $session = AcademicSession::findOrFail($sessionId);
        $wasActive = $session->is_active;

        $session->update([
            'is_active' => false,
            'status' => 'closed',
        ]);

        if ($wasActive) {
            self::setViewingSessionId(null);
        }

        return $session;
    }

    /**
     * Month labels for the current (or given) academic session: ["2026-04" => "Apr 2026", ...]
     * Falls back to the current calendar year when session dates are missing.
     */
    public static function monthLabels(?AcademicSession $session = null): array
    {
        $session = $session ?? self::current();

        if ($session?->start_date) {
            $start = Carbon::parse($session->start_date)->startOfMonth();
        } else {
            $start = Carbon::now()->startOfYear();
        }

        if ($session?->end_date) {
            $end = Carbon::parse($session->end_date)->startOfMonth();
        } else {
            $end = $start->copy()->addMonths(11);
        }

        if ($end->lt($start)) {
            $end = $start->copy()->addMonths(11);
        }

        $months = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $months[$cursor->format('Y-m')] = $cursor->format('M Y');
            $cursor->addMonth();
        }

        return $months;
    }

    /** Parse assignment end_date values stored as d-m-Y / d/m/Y / Y-m-d. */
    public static function parseEndDate(?string $value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);
        foreach (['d-m-Y', 'd/m/Y', 'd-m-y', 'd/m/y', 'Y-m-d'] as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $value);
                if ($parsed !== false) {
                    return $parsed;
                }
            } catch (\Throwable $e) {
                // try next format
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
