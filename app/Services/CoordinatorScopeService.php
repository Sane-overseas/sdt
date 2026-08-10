<?php

namespace App\Services;

use App\Models\District;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CoordinatorScopeService
{
    public const LEVEL_DISTRICT = 'district';
    public const LEVEL_STATE = 'state';

    public static function isCoordinator(User $user): bool
    {
        return (int) $user->role === 2;
    }

    public static function isStateCoordinator(User $user): bool
    {
        return self::isCoordinator($user)
            && ($user->coordinator_level ?? self::LEVEL_DISTRICT) === self::LEVEL_STATE;
    }

    public static function isDistrictCoordinator(User $user): bool
    {
        return self::isCoordinator($user) && !self::isStateCoordinator($user);
    }

    public static function trainersInScopeQuery(User $coordinator): Builder
    {
        $query = User::query()->where('role', 0);

        if ($coordinator->state_id) {
            $query->where('state_id', $coordinator->state_id);
        }

        if (self::isDistrictCoordinator($coordinator)) {
            self::applyDistrictMatch($query, $coordinator->district);
        }

        return $query->orderBy('instructor_name');
    }

    public static function coordinatorsInScopeQuery(User $coordinator): Builder
    {
        $query = User::query()
            ->where('role', 2)
            ->where('id', '!=', $coordinator->id);

        if ($coordinator->state_id) {
            $query->where('state_id', $coordinator->state_id);
        }

        if (self::isDistrictCoordinator($coordinator)) {
            self::applyDistrictMatch($query, $coordinator->district);
        }

        return $query->orderBy('instructor_name');
    }

    public static function trainerInScope(User $coordinator, ?User $trainer): bool
    {
        if (!$trainer || (int) $trainer->role !== 0) {
            return false;
        }

        if ($coordinator->state_id && (int) $trainer->state_id !== (int) $coordinator->state_id) {
            return false;
        }

        if (self::isStateCoordinator($coordinator)) {
            return true;
        }

        return self::districtsMatch($coordinator->district, $trainer->district);
    }

    public static function assertTrainerInScope(User $coordinator, ?User $trainer): void
    {
        if (!self::trainerInScope($coordinator, $trainer)) {
            abort(403, 'You can only manage trainers in your scope.');
        }
    }

    public static function coordinatorInScope(User $viewer, ?User $target): bool
    {
        if (!$target || (int) $target->role !== 2) {
            return false;
        }

        if ($viewer->state_id && (int) $target->state_id !== (int) $viewer->state_id) {
            return false;
        }

        if (self::isStateCoordinator($viewer)) {
            return true;
        }

        return self::districtsMatch($viewer->district, $target->district);
    }

    public static function assertCoordinatorInScope(User $viewer, ?User $target): void
    {
        if (!self::coordinatorInScope($viewer, $target)) {
            abort(403, 'You can only view coordinators in your scope.');
        }
    }

    public static function canManageAssignmentTarget(User $actor, ?User $target): bool
    {
        if (!$target) {
            return false;
        }

        if ((int) $actor->id === (int) $target->id) {
            return true;
        }

        if ((int) $target->role === 0) {
            return self::trainerInScope($actor, $target);
        }

        if ((int) $target->role === 2) {
            return self::coordinatorInScope($actor, $target);
        }

        return false;
    }

    public static function applyDistrictMatch(Builder $query, ?string $districtValue): void
    {
        $districtName = trim((string) $districtValue);
        if ($districtName === '') {
            $query->whereRaw('1 = 0');

            return;
        }

        $districtNameLower = mb_strtolower($districtName);
        $district = District::where(function ($q) use ($districtName, $districtNameLower) {
            $q->whereRaw('LOWER(TRIM(district)) = ?', [$districtNameLower])
                ->orWhere('district', $districtName);
            if (ctype_digit($districtName)) {
                $q->orWhere('id', (int) $districtName);
            }
        })->first();

        $query->where(function ($q) use ($district, $districtName, $districtNameLower) {
            $q->whereRaw('LOWER(TRIM(district)) = ?', [$districtNameLower])
                ->orWhere('district', $districtName);
            if ($district) {
                $q->orWhere('district', (string) $district->id)
                    ->orWhereRaw('LOWER(TRIM(district)) = ?', [mb_strtolower(trim((string) $district->district))]);
            }
        });
    }

    public static function districtsMatch(?string $a, ?string $b): bool
    {
        $a = trim((string) $a);
        $b = trim((string) $b);
        if ($a === '' || $b === '') {
            return false;
        }

        if (mb_strtolower($a) === mb_strtolower($b)) {
            return true;
        }

        $resolve = function (string $value): ?District {
            $lower = mb_strtolower($value);

            return District::where(function ($q) use ($value, $lower) {
                $q->whereRaw('LOWER(TRIM(district)) = ?', [$lower])
                    ->orWhere('district', $value);
                if (ctype_digit($value)) {
                    $q->orWhere('id', (int) $value);
                }
            })->first();
        };

        $da = $resolve($a);
        $db = $resolve($b);

        return $da && $db && (int) $da->id === (int) $db->id;
    }
}
