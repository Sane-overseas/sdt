<?php

namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;

class HolidayService
{
    public static function isHoliday(Carbon $date): bool
    {
        if (self::isGlobalHoliday($date)) {
            return true;
        }

        return Holiday::whereDate('holiday_date', $date->format('Y-m-d'))->exists();
    }

    public static function isGlobalHoliday(Carbon $date): bool
    {
        return $date->isSunday() || self::isSecondSaturday($date);
    }

    public static function isSecondSaturday(Carbon $date): bool
    {
        return $date->isSaturday() && (int) ceil($date->day / 7) === 2;
    }

    public static function holidayLabel(Carbon $date): string
    {
        if ($date->isSunday()) {
            return 'Sunday';
        }

        if (self::isSecondSaturday($date)) {
            return '2nd Saturday';
        }

        $holiday = Holiday::whereDate('holiday_date', $date->format('Y-m-d'))->first();

        return $holiday?->title ?? 'Holiday';
    }

    public static function calculateEndDate(Carbon $startDate, int $workingDays): Carbon
    {
        $current = $startDate->copy();
        $count = 0;

        while ($count < $workingDays) {
            if (!self::isHoliday($current)) {
                $count++;
            }

            if ($count < $workingDays) {
                $current->addDay();
            }
        }

        return $current;
    }

    public static function countWorkingDays(Carbon $start, Carbon $end): int
    {
        $count = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (!self::isHoliday($current)) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    public static function holidaysInRange(Carbon $start, Carbon $end): array
    {
        return Holiday::whereBetween('holiday_date', [
            $start->format('Y-m-d'),
            $end->format('Y-m-d'),
        ])->orderBy('holiday_date')->get()->all();
    }
}
