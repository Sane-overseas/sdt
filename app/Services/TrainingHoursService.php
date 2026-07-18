<?php

namespace App\Services;

use App\Models\School;
use Carbon\Carbon;

class TrainingHoursService
{
    /** Daily hours from intime–outtime (H:i). */
    public static function dailyHours(string $intime, string $outtime): float
    {
        $start = Carbon::createFromFormat('H:i', date('H:i', strtotime($intime)));
        $end = Carbon::createFromFormat('H:i', date('H:i', strtotime($outtime)));

        if ($end->lessThanOrEqualTo($start)) {
            return 0;
        }

        return round($end->diffInMinutes($start) / 60, 2);
    }

    /** Planned training hours = working days × daily hours. */
    public static function plannedHours(int $workingDays, string $intime, string $outtime): float
    {
        return round($workingDays * self::dailyHours($intime, $outtime), 2);
    }

    /** Permanent school training hours (all sessions). */
    public static function getForSchool(int $schoolId): ?float
    {
        $hours = School::where('id', $schoolId)->value('training_hours');

        return $hours !== null ? (float) $hours : null;
    }

    /**
     * Update permanent school hours.
     * Does not change required_hours on existing assignments (snapshot rule).
     */
    public static function setForSchool(int $schoolId, float $hours): void
    {
        School::where('id', $schoolId)->update([
            'training_hours' => $hours,
        ]);
    }
}
