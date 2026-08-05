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

    /** Total required training hours for the school (e.g. 60). */
    public static function getForSchool(int $schoolId): ?float
    {
        $hours = School::where('id', $schoolId)->value('training_hours');

        return $hours !== null ? (float) $hours : null;
    }

    /** Max hours allowed per training day (e.g. 2). */
    public static function getDailyForSchool(int $schoolId): ?float
    {
        $hours = School::where('id', $schoolId)->value('daily_training_hours');

        return $hours !== null ? (float) $hours : null;
    }

    /**
     * Minimum working days = ceil(total / per-day).
     * Example: 60 hrs total, 2 hrs/day → 30 days minimum.
     * Trainer may take more days.
     */
    public static function minimumWorkingDays(?float $totalHours, ?float $dailyMaxHours): ?int
    {
        if ($totalHours === null || $dailyMaxHours === null || $dailyMaxHours <= 0) {
            return null;
        }

        return (int) ceil($totalHours / $dailyMaxHours);
    }

    /**
     * Update permanent school total hours.
     * Does not change required_hours on existing assignments (snapshot rule).
     */
    public static function setForSchool(int $schoolId, float $hours): void
    {
        School::where('id', $schoolId)->update([
            'training_hours' => $hours,
        ]);
    }

    /** True when daily intime–outtime exceeds max hrs/day. */
    public static function exceedsDailyMax(float $dailyHours, ?float $dailyMax): bool
    {
        if ($dailyMax === null) {
            return false;
        }

        return ($dailyHours - $dailyMax) > 0.001;
    }
}
