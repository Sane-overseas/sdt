<?php

namespace App\Services;

use App\Models\AsignedSchool;
use App\Models\Completion;
use App\Models\Image;
use App\Models\School;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;

class SessionUploadService
{
    public static function findAssignment(int $assignmentId, ?int $trainerUserId = null): ?AsignedSchool
    {
        $query = AsignedSchool::where('id', $assignmentId);

        if ($trainerUserId !== null) {
            $query->where('user_id', $trainerUserId);
        }

        return $query->first();
    }

    /** Uploads & route plans only allowed on active session assignments. */
    public static function assertActiveSessionAssignment(int $assignmentId, ?int $trainerUserId = null): AsignedSchool
    {
        $activeId = AcademicSessionService::activeId();
        if (!$activeId) {
            abort(422, 'No active session. Please contact admin.');
        }

        $assignment = AsignedSchool::withoutGlobalScopes()
            ->where('id', $assignmentId)
            ->when($trainerUserId !== null, fn ($q) => $q->where('user_id', $trainerUserId))
            ->first();

        if (!$assignment) {
            abort(403, 'School assignment not found.');
        }

        if ((int) $assignment->session_id !== (int) $activeId) {
            abort(403, 'This school belongs to a previous session. Uploads are only allowed in the active session.');
        }

        if (Auth::check() && in_array((int) Auth::user()->role, [0, 2], true)) {
            $auth = Auth::user();
            $role = (int) $auth->role;

            if ($role === 0) {
                if ((int) $auth->id !== (int) $assignment->user_id) {
                    abort(403, 'Unauthorized.');
                }
            } elseif ($role === 2) {
                if ((int) ($auth->data_upload_status ?? 0) !== 1) {
                    abort(403, 'You do not have data upload permission. Ask admin to enable it.');
                }

                $trainer = User::find($assignment->user_id);
                if (!$trainer || (int) $trainer->cordinator_id !== (int) $auth->cordinator_id) {
                    abort(403, 'You can only upload for your own trainers.');
                }
            }
        }

        return $assignment;
    }

    public static function assertRoutePlanSet(AsignedSchool $assignment): void
    {
        if (empty($assignment->route_date)) {
            abort(422, 'Route plan must be set before uploading data.');
        }
    }

    /** Planned hours must cover required total before uploads are allowed. */
    public static function assertPlannedHoursCoverRequired(AsignedSchool $assignment): void
    {
        if ($assignment->required_hours === null) {
            return;
        }

        $required = (float) $assignment->required_hours;
        $planned = $assignment->planned_hours !== null ? (float) $assignment->planned_hours : null;

        if ($planned === null || ($planned + 0.001) < $required) {
            abort(422, 'Planned hours must cover required total hours before uploading. Please update your route plan.');
        }
    }

    public static function canUpload(AsignedSchool|array $assignment): bool
    {
        $approval = is_array($assignment)
            ? ($assignment['approval_status'] ?? 'approved')
            : ($assignment->approval_status ?? 'approved');
        if ($approval !== 'approved') {
            return false;
        }

        $routeDate = is_array($assignment) ? ($assignment['route_date'] ?? null) : $assignment->route_date;
        if (empty($routeDate)) {
            return false;
        }

        $required = is_array($assignment)
            ? ($assignment['required_hours'] ?? null)
            : $assignment->required_hours;
        if ($required === null) {
            return true;
        }

        $planned = is_array($assignment)
            ? ($assignment['planned_hours'] ?? null)
            : $assignment->planned_hours;

        return $planned !== null && ((float) $planned + 0.001) >= (float) $required;
    }

    public static function assertCanUpload(int $assignmentId, ?int $trainerUserId = null): AsignedSchool
    {
        $assignment = self::assertActiveSessionAssignment($assignmentId, $trainerUserId);
        if (!$assignment->isApproved()) {
            abort(403, 'This school request is not approved yet. Wait for admin approval.');
        }
        self::assertRoutePlanSet($assignment);
        self::assertPlannedHoursCoverRequired($assignment);

        return $assignment;
    }

    public static function assertCanSetRoutePlan(int $assignmentId): AsignedSchool
    {
        $assignment = self::assertActiveSessionAssignment($assignmentId);
        if (!$assignment->isApproved()) {
            abort(403, 'Route plan can be set only after admin approves the school request.');
        }

        return $assignment;
    }

    /** Derive assignment + school status from session-scoped upload records. */
    public static function syncAssignmentStatuses(?int $sessionId = null): void
    {
        $sessionId = $sessionId ?? AcademicSessionService::scopeSessionId();

        $query = AsignedSchool::withoutGlobalScopes();
        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }

        $activeId = AcademicSessionService::activeId();

        foreach ($query->get() as $assignment) {
            $school = School::find($assignment->school_name);
            if (!$school) {
                continue;
            }

            $uploadQuery = fn ($model) => $model::withoutGlobalScopes()
                ->where('school_id', $school->id)
                ->where('session_id', $assignment->session_id)
                ->where('user_id', $assignment->user_id);

            $video = $uploadQuery(Video::class)->first();
            $image = $uploadQuery(Image::class)->first();
            $completion = $uploadQuery(Completion::class)->first();

            $complete = $video && (int) $video->status === 1
                && $image && (int) $image->status === 1
                && $completion && (int) $completion->status === 1;

            AsignedSchool::withoutGlobalScopes()
                ->where('id', $assignment->id)
                ->update(['status' => $complete ? 1 : 0]);

            if ($activeId && (int) $assignment->session_id === (int) $activeId) {
                $school->video_status = $video ? (int) $video->status : 0;
                $school->image_status = $image ? (int) $image->status : 0;
                $school->completion_status = $completion ? (int) $completion->status : 0;
                $school->status = $complete ? 1 : 0;
                $school->save();
            }
        }
    }
}
