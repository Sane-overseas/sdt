<?php

namespace App\Services;

use App\Models\AsignedSchool;
use App\Models\District;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SchoolRequestService
{
    public const MAX_ACTIVE_SLOTS = 3;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    /** Incomplete pending/approved assignments count toward the 3-school limit. */
    public static function activeSlotCount(int $trainerId, ?int $sessionId = null): int
    {
        $sessionId = $sessionId ?? AcademicSessionService::activeId();
        if (!$sessionId) {
            return 0;
        }

        return AsignedSchool::withoutGlobalScopes()
            ->where('session_id', $sessionId)
            ->where('user_id', $trainerId)
            ->whereIn('approval_status', [self::STATUS_PENDING, self::STATUS_APPROVED])
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 0);
            })
            ->count();
    }

    public static function remainingSlots(int $trainerId, ?int $sessionId = null): int
    {
        return max(0, self::MAX_ACTIVE_SLOTS - self::activeSlotCount($trainerId, $sessionId));
    }

    /** Schools in trainer's block that are free for request. */
    public static function availableSchoolsForTrainer(User $trainer): Collection
    {
        $sessionId = AcademicSessionService::activeId();
        if (!$sessionId || empty($trainer->block)) {
            return collect();
        }

        $districtQuery = District::query()->where('district', $trainer->district);
        if ($trainer->state_id) {
            $districtQuery->where('state_id', $trainer->state_id);
        }
        $district = $districtQuery->first();
        if (!$district) {
            return collect();
        }

        $takenIds = AsignedSchool::withoutGlobalScopes()
            ->where('session_id', $sessionId)
            ->whereIn('approval_status', [self::STATUS_PENDING, self::STATUS_APPROVED])
            ->pluck('school_name')
            ->map(fn ($id) => (int) $id)
            ->all();

        return School::where('district_id', $district->id)
            ->where('block', $trainer->block)
            ->when(!empty($takenIds), fn ($q) => $q->whereNotIn('id', $takenIds))
            ->orderBy('school_name')
            ->get();
    }

    /**
     * @param  array<int>  $schoolIds
     * @return array{ok?:bool,error?:string,requested?:int}
     */
    public static function requestSchools(User $trainer, array $schoolIds): array
    {
        $sessionId = AcademicSessionService::assignmentSessionId();
        if (!$sessionId) {
            return ['error' => 'No active session. Please contact admin.'];
        }

        $schoolIds = array_values(array_unique(array_map('intval', $schoolIds)));
        if (empty($schoolIds)) {
            return ['error' => 'Please select at least one school.'];
        }

        $remaining = self::remainingSlots((int) $trainer->id, $sessionId);
        if ($remaining <= 0) {
            return ['error' => 'You already have '.self::MAX_ACTIVE_SLOTS.' active schools (pending or in training). Complete one to request another.'];
        }

        if (count($schoolIds) > $remaining) {
            return ['error' => 'You can select at most '.$remaining.' more school(s) right now (max '.self::MAX_ACTIVE_SLOTS.' active).'];
        }

        $availableIds = self::availableSchoolsForTrainer($trainer)->pluck('id')->map(fn ($id) => (int) $id)->all();
        foreach ($schoolIds as $schoolId) {
            if (!in_array($schoolId, $availableIds, true)) {
                return ['error' => 'One or more selected schools are not available in your block.'];
            }
        }

        $district = District::where('district', $trainer->district)
            ->when($trainer->state_id, fn ($q) => $q->where('state_id', $trainer->state_id))
            ->first();

        $requested = 0;
        foreach ($schoolIds as $schoolId) {
            $school = School::find($schoolId);
            if (!$school) {
                continue;
            }

            AsignedSchool::create([
                'user_id' => $trainer->id,
                'district' => $district?->id ?? $school->district_id,
                'block' => $trainer->block,
                'school_name' => $schoolId,
                'session_id' => $sessionId,
                'asigned_by' => $trainer->id,
                'approval_status' => self::STATUS_PENDING,
                'status' => 0,
                'required_hours' => TrainingHoursService::getForSchool($schoolId),
                'daily_training_hours' => TrainingHoursService::getDailyForSchool($schoolId),
                'planned_hours' => null,
                'start_route_plan' => null,
                'end_route_plan' => null,
            ]);

            SchoolAssignmentService::syncSchoolAssignedFlag($schoolId);
            $requested++;
        }

        return ['ok' => true, 'requested' => $requested];
    }

    public static function approve(int $assignmentId, int $adminId, ?string $note = null): array
    {
        $row = AsignedSchool::withoutGlobalScopes()->find($assignmentId);
        if (!$row) {
            return ['error' => 'Request not found.'];
        }
        if ($row->approval_status !== self::STATUS_PENDING) {
            return ['error' => 'Only pending requests can be approved.'];
        }

        $row->approval_status = self::STATUS_APPROVED;
        $row->approval_note = $note;
        $row->approved_at = now();
        $row->approved_by = $adminId;
        $row->save();

        SchoolAssignmentService::syncSchoolAssignedFlag((int) $row->school_name);

        return ['ok' => true, 'message' => 'School request approved.'];
    }

    public static function reject(int $assignmentId, int $adminId, ?string $note = null): array
    {
        $row = AsignedSchool::withoutGlobalScopes()->find($assignmentId);
        if (!$row) {
            return ['error' => 'Request not found.'];
        }
        if ($row->approval_status !== self::STATUS_PENDING) {
            return ['error' => 'Only pending requests can be rejected.'];
        }

        $schoolId = (int) $row->school_name;
        $row->approval_status = self::STATUS_REJECTED;
        $row->approval_note = $note;
        $row->approved_at = now();
        $row->approved_by = $adminId;
        $row->save();

        SchoolAssignmentService::syncSchoolAssignedFlag($schoolId);

        return ['ok' => true, 'message' => 'School request rejected.'];
    }

    public static function pendingRequests(?int $stateId = null)
    {
        $query = AsignedSchool::withoutGlobalScopes()
            ->with(['user'])
            ->where('approval_status', self::STATUS_PENDING)
            ->where('session_id', AcademicSessionService::scopeSessionId())
            ->orderByDesc('created_at');

        if ($stateId) {
            $query->whereHas('user', fn ($q) => $q->where('state_id', $stateId));
        }

        return $query->get();
    }
}
