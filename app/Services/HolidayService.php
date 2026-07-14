<?php

namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HolidayService
{
    /**
     * Sunday + 2nd Saturday are Off by default.
     * Calendar toggle can force a day Working (entry_type=working) or mark custom Off days.
     */
    public static function isHoliday(Carbon $date, ?int $districtId = null, ?int $stateId = null): bool
    {
        if (self::hasForceWorking($date->format('Y-m-d'), $districtId, $stateId)) {
            return false;
        }

        if (self::isGlobalHoliday($date)) {
            return true;
        }

        return self::offQuery($districtId, $stateId)
            ->whereDate('holiday_date', $date->format('Y-m-d'))
            ->exists();
    }

    public static function isGlobalHoliday(Carbon $date): bool
    {
        return $date->isSunday() || self::isSecondSaturday($date);
    }

    public static function isSecondSaturday(Carbon $date): bool
    {
        return $date->isSaturday() && (int) ceil($date->day / 7) === 2;
    }

    public static function holidayLabel(Carbon $date, ?int $districtId = null, ?int $stateId = null): string
    {
        if (self::hasForceWorking($date->format('Y-m-d'), $districtId, $stateId)) {
            return 'Working';
        }

        if ($date->isSunday()) {
            return 'Sunday';
        }

        if (self::isSecondSaturday($date)) {
            return '2nd Saturday';
        }

        $holiday = self::offQuery($districtId, $stateId)
            ->whereDate('holiday_date', $date->format('Y-m-d'))
            ->orderByRaw('CASE WHEN district_id IS NULL THEN 1 ELSE 0 END')
            ->first();

        return $holiday?->title ?: 'Off';
    }

    public static function calculateEndDate(Carbon $startDate, int $workingDays, ?int $districtId = null, ?int $stateId = null): Carbon
    {
        $current = $startDate->copy();
        $count = 0;

        while ($count < $workingDays) {
            if (!self::isHoliday($current, $districtId, $stateId)) {
                $count++;
            }

            if ($count < $workingDays) {
                $current->addDay();
            }
        }

        return $current;
    }

    public static function countWorkingDays(Carbon $start, Carbon $end, ?int $districtId = null, ?int $stateId = null): int
    {
        $count = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (!self::isHoliday($current, $districtId, $stateId)) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    public static function holidaysInRange(Carbon $start, Carbon $end, ?int $districtId = null, ?int $stateId = null): array
    {
        return self::offQuery($districtId, $stateId)
            ->whereBetween('holiday_date', [
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
            ])
            ->orderBy('holiday_date')
            ->get()
            ->all();
    }

    /** Custom Off days + force-Working rows (for calendar). */
    public static function queryForScope(?int $districtId = null, ?int $stateId = null): Builder
    {
        $query = Holiday::query();

        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        if ($districtId) {
            $query->where(function (Builder $q) use ($districtId) {
                $q->whereNull('district_id')->orWhere('district_id', $districtId);
            });
        }

        return $query;
    }

    public static function offQuery(?int $districtId = null, ?int $stateId = null): Builder
    {
        return self::queryForScope($districtId, $stateId)
            ->where(function (Builder $q) {
                $q->where('entry_type', Holiday::TYPE_OFF)
                    ->orWhereNull('entry_type');
            });
    }

    public static function workingQuery(?int $districtId = null, ?int $stateId = null): Builder
    {
        return self::queryForScope($districtId, $stateId)
            ->where('entry_type', Holiday::TYPE_WORKING);
    }

    public static function exactScopeQuery(int $stateId, ?int $districtId = null): Builder
    {
        return Holiday::query()
            ->where('state_id', $stateId)
            ->when(
                $districtId,
                fn (Builder $q) => $q->where('district_id', $districtId),
                fn (Builder $q) => $q->whereNull('district_id')
            );
    }

    public static function hasForceWorking(string $date, ?int $districtId = null, ?int $stateId = null): bool
    {
        return self::workingQuery($districtId, $stateId)
            ->whereDate('holiday_date', $date)
            ->exists();
    }

    /**
     * Toggle Off ↔ Working for one date at exact scope.
     *
     * Auto Sunday / 2nd Sat:
     *   Off (default) → click → mark Working
     *   Working → click → back to Off (default)
     *
     * Other days:
     *   Working → click → mark Off
     *   Off → click → Working (remove Off)
     *
     * @return array{action: string, status: string, holiday: ?Holiday}
     */
    public static function toggleDate(string $date, int $stateId, ?int $districtId = null, ?string $title = null): array
    {
        $carbon = Carbon::parse($date);

        $existingOff = self::exactScopeQuery($stateId, $districtId)
            ->whereDate('holiday_date', $date)
            ->where(function (Builder $q) {
                $q->where('entry_type', Holiday::TYPE_OFF)->orWhereNull('entry_type');
            })
            ->first();

        $existingWorking = self::exactScopeQuery($stateId, $districtId)
            ->whereDate('holiday_date', $date)
            ->where('entry_type', Holiday::TYPE_WORKING)
            ->first();

        if (self::isGlobalHoliday($carbon)) {
            // Default Off → force Working
            if (!$existingWorking) {
                if ($existingOff) {
                    $existingOff->delete();
                }

                $holiday = Holiday::create([
                    'holiday_date' => $date,
                    'title' => $title ?: 'Working Day',
                    'entry_type' => Holiday::TYPE_WORKING,
                    'state_id' => $stateId,
                    'district_id' => $districtId,
                    'created_by' => Auth::id(),
                ]);

                return ['action' => 'opened', 'status' => 'working', 'holiday' => $holiday];
            }

            // Force Working → remove → back to default Off
            $existingWorking->delete();

            return ['action' => 'closed', 'status' => 'off', 'holiday' => null];
        }

        // Normal day: toggle custom Off
        if ($existingOff) {
            $existingOff->delete();
            if ($existingWorking) {
                $existingWorking->delete();
            }

            return ['action' => 'opened', 'status' => 'working', 'holiday' => null];
        }

        if ($existingWorking) {
            $existingWorking->delete();
        }

        $holiday = Holiday::create([
            'holiday_date' => $date,
            'title' => $title ?: self::defaultTitleForDate($carbon),
            'entry_type' => Holiday::TYPE_OFF,
            'state_id' => $stateId,
            'district_id' => $districtId,
            'created_by' => Auth::id(),
        ]);

        return ['action' => 'closed', 'status' => 'off', 'holiday' => $holiday];
    }

    public static function defaultTitleForDate(Carbon $date): string
    {
        if ($date->isSunday()) {
            return 'Sunday Off';
        }

        if (self::isSecondSaturday($date)) {
            return '2nd Saturday Off';
        }

        if ($date->isSaturday()) {
            return 'Saturday Off';
        }

        return 'Off';
    }
}
