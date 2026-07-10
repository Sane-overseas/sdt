<?php

namespace App\Services;

use App\Models\AsignedSchool;
use App\Models\District;
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;

class SchoolAssignmentService
{
    public static function assignedSchoolIdsForActiveSession(): Collection
    {
        $sessionId = AcademicSessionService::activeId();
        if (!$sessionId) {
            return collect();
        }

        return AsignedSchool::withoutGlobalScopes()
            ->where('session_id', $sessionId)
            ->pluck('school_name');
    }

    public static function isAssignedInActiveSession(int $schoolId): bool
    {
        return self::assignedSchoolIdsForActiveSession()->contains((string) $schoolId)
            || self::assignedSchoolIdsForActiveSession()->contains($schoolId);
    }

    public static function syncSchoolAssignedFlag(int $schoolId): void
    {
        $school = School::find($schoolId);
        if (!$school) {
            return;
        }

        $school->asigned_school = self::isAssignedInActiveSession($schoolId) ? 1 : 0;
        $school->save();
    }

    public static function syncAllSchoolAssignedFlags(): void
    {
        School::query()->update(['asigned_school' => 0]);

        $sessionId = AcademicSessionService::activeId();
        if (!$sessionId) {
            return;
        }

        $assignedIds = AsignedSchool::withoutGlobalScopes()
            ->where('session_id', $sessionId)
            ->pluck('school_name');

        if ($assignedIds->isNotEmpty()) {
            School::whereIn('id', $assignedIds)->update(['asigned_school' => 1]);
        }
    }

    public static function assignSchools(array $schoolIds, int $trainerId, $district, $block, int $assignedBy): array
    {
        $sessionId = AcademicSessionService::assignmentSessionId();
        if (!$sessionId) {
            return ['error' => 'No active session found. Please create or activate a session first.'];
        }

        $assigned = 0;
        $skipped = 0;

        foreach ($schoolIds as $schoolId) {
            $school = School::find($schoolId);
            if (!$school) {
                $skipped++;
                continue;
            }

            $trainer = User::find($trainerId);
            $schoolDistrict = District::find($school->district_id);
            if ($trainer && $trainer->state_id && $schoolDistrict && (int) $trainer->state_id !== (int) $schoolDistrict->state_id) {
                return ['error' => 'School and trainer must belong to the same state.'];
            }

            $exists = AsignedSchool::withoutGlobalScopes()
                ->where('session_id', $sessionId)
                ->where('school_name', $schoolId)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            AsignedSchool::create([
                'user_id' => $trainerId,
                'district' => $district,
                'block' => $block,
                'school_name' => $schoolId,
                'session_id' => $sessionId,
                'asigned_by' => $assignedBy,
                'start_route_plan' => null,
                'end_route_plan' => null,
            ]);

            self::syncSchoolAssignedFlag((int) $schoolId);
            $assigned++;
        }

        return ['assigned' => $assigned, 'skipped' => $skipped];
    }
}
