<?php

namespace App\Services;

use App\Models\AsignedSchool;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;

class ClaimNotificationService
{
    /**
     * Get recent pending claim notifications for admin within the scoped state and session.
     *
     * @param int $limit
     * @return Collection
     */
    public static function getPendingClaimNotifications(int $limit = 15): Collection
    {
        $districtIds = StateService::districtIds();
        $sessionId = AcademicSessionService::scopeSessionId();

        $claimedAssignments = AsignedSchool::withoutGlobalScopes()
            ->with(['user', 'school'])
            ->where('claim_status', 1)
            ->where('paid_status', 0)
            ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->when(!empty($districtIds), fn($q) => $q->whereIn('district', $districtIds))
            ->orderByDesc('claimed_at')
            ->orderByDesc('id')
            ->take($limit)
            ->get();

        return $claimedAssignments->map(function ($assignment) {
            return [
                'assignment_id' => $assignment->id,
                'trainer_id' => $assignment->user_id,
                'trainer_name' => $assignment->user->instructor_name ?? 'Trainer #'.$assignment->user_id,
                'trainer_code' => $assignment->user->instructor_code ?? '—',
                'school_id' => $assignment->school_name,
                'school_name' => $assignment->school->school_name ?? ('School #'.$assignment->school_name),
                'district' => $assignment->district,
                'block' => $assignment->block,
                'claimed_at' => $assignment->claimed_at,
                'time_ago' => $assignment->claimed_at ? $assignment->claimed_at->diffForHumans() : 'Recently',
            ];
        });
    }

    /**
     * Get total count of pending claimed schools in the current scoped state & session.
     *
     * @return int
     */
    public static function getPendingClaimsCount(): int
    {
        $districtIds = StateService::districtIds();
        $sessionId = AcademicSessionService::scopeSessionId();

        return AsignedSchool::withoutGlobalScopes()
            ->where('claim_status', 1)
            ->where('paid_status', 0)
            ->when($sessionId, fn($q) => $q->where('session_id', $sessionId))
            ->when(!empty($districtIds), fn($q) => $q->whereIn('district', $districtIds))
            ->count();
    }
}
