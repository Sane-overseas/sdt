<?php

namespace App\Services;

use App\Models\Cordinator;
use App\Models\District;
use App\Models\School;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class StateService
{
    public const VIEW_STATE_COOKIE = 'sopl_view_state_id';

    public static function all()
    {
        return State::where('is_active', true)->orderBy('name')->get();
    }

    /** State used for reading/filtering data in admin panels. */
    public static function current(): ?State
    {
        if (Auth::check() && (int) Auth::user()->role === 1) {
            $viewId = Session::get('view_state_id') ?: self::cookieStateId();
            if ($viewId) {
                $state = State::where('is_active', true)->find($viewId);
                if ($state) {
                    // Keep session in sync after login (cookie survives logout).
                    if ((int) Session::get('view_state_id') !== (int) $state->id) {
                        Session::put('view_state_id', (int) $state->id);
                    }

                    return $state;
                }

                Session::forget('view_state_id');
                self::forgetCookie();
            }
        } elseif (Auth::check() && in_array((int) Auth::user()->role, [0, 2], true) && Auth::user()->state_id) {
            return State::find(Auth::user()->state_id);
        }

        return State::where('is_active', true)->orderBy('id')->first();
    }

    public static function currentId(): ?int
    {
        return self::current()?->id;
    }

    public static function scopeStateId(): ?int
    {
        return self::currentId();
    }

    public static function setViewingStateId(?int $stateId): void
    {
        if ($stateId) {
            Session::put('view_state_id', $stateId);
            Cookie::queue(cookie(self::VIEW_STATE_COOKIE, (string) $stateId, 60 * 24 * 365));
        } else {
            Session::forget('view_state_id');
            self::forgetCookie();
        }
    }

    /** Restore admin's last viewed state into the session (e.g. after login). */
    public static function restoreViewingStateForAdmin(): void
    {
        if (!Auth::check() || (int) Auth::user()->role !== 1) {
            return;
        }

        if (Session::get('view_state_id')) {
            return;
        }

        $cookieId = self::cookieStateId();
        if ($cookieId && State::where('is_active', true)->where('id', $cookieId)->exists()) {
            Session::put('view_state_id', $cookieId);
        }
    }

    private static function cookieStateId(): ?int
    {
        try {
            $value = request()->cookie(self::VIEW_STATE_COOKIE);
        } catch (\Throwable $e) {
            return null;
        }

        return $value !== null && $value !== '' ? (int) $value : null;
    }

    private static function forgetCookie(): void
    {
        Cookie::queue(Cookie::forget(self::VIEW_STATE_COOKIE));
    }

    public static function create(string $name, string $code): State
    {
        return State::create([
            'name' => $name,
            'code' => strtoupper($code),
            'is_active' => true,
        ]);
    }

    public static function districtsQuery(): Builder
    {
        $query = District::query();
        $stateId = self::scopeStateId();

        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        return $query;
    }

    public static function districtIds(): array
    {
        return self::districtsQuery()->pluck('id')->all();
    }

    public static function schoolsQuery(): Builder
    {
        $districtIds = self::districtIds();
        $query = School::query();

        if (self::scopeStateId()) {
            $query->whereIn('district_id', $districtIds ?: [0]);
        }

        return $query;
    }

    public static function cordinatorsQuery(): Builder
    {
        $query = Cordinator::query();
        $stateId = self::scopeStateId();

        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        return $query;
    }

    public static function trainersQuery()
    {
        $query = User::where('role', 0);
        $stateId = self::scopeStateId();

        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        return $query;
    }

    public static function coordinatorUsersQuery()
    {
        $query = User::where('role', 2);
        $stateId = self::scopeStateId();

        if ($stateId) {
            $query->where('state_id', $stateId);
        }

        return $query;
    }

    public static function assertDistrictInScope(int $districtId): District
    {
        $district = District::find($districtId);
        if (!$district) {
            abort(404, 'District not found.');
        }

        $stateId = self::scopeStateId();
        if ($stateId && (int) $district->state_id !== (int) $stateId) {
            abort(422, 'District does not belong to the selected state.');
        }

        return $district;
    }

    public static function assertCordinatorInScope(int $cordinatorId): void
    {
        $cordinator = Cordinator::find($cordinatorId);
        if (!$cordinator) {
            abort(404, 'Coordinator not found.');
        }

        $stateId = self::scopeStateId();
        if ($stateId && (int) $cordinator->state_id !== (int) $stateId) {
            abort(422, 'Coordinator does not belong to the selected state.');
        }
    }

    public static function assertSchoolInScope(int $schoolId): School
    {
        $school = School::find($schoolId);
        if (!$school) {
            abort(404, 'School not found.');
        }

        self::assertDistrictInScope((int) $school->district_id);

        return $school;
    }
}
