<?php

namespace App\Services;

use App\Models\CoordinatorRegistration;
use App\Models\Cordinator;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CoordinatorCodeService
{
    public const PREFIX_DISTRICT = 'SOPL_DC_';
    public const PREFIX_STATE = 'SOPL_SC_';

    /** Floor for first district code when none exist (SOPL_DC_023). */
    public const START_DISTRICT = 23;

    /** Floor for first state code when none exist (SOPL_SC_005). */
    public const START_STATE = 5;

    public static function prefixForLevel(string $level): string
    {
        return $level === CoordinatorScopeService::LEVEL_STATE
            ? self::PREFIX_STATE
            : self::PREFIX_DISTRICT;
    }

    public static function startForLevel(string $level): int
    {
        return $level === CoordinatorScopeService::LEVEL_STATE
            ? self::START_STATE
            : self::START_DISTRICT;
    }

    /**
     * Next code like SOPL_DC_023 / SOPL_SC_005 (3-digit padded), unique across users & coordinators.
     */
    public static function next(string $level = CoordinatorScopeService::LEVEL_DISTRICT): string
    {
        $level = $level === CoordinatorScopeService::LEVEL_STATE
            ? CoordinatorScopeService::LEVEL_STATE
            : CoordinatorScopeService::LEVEL_DISTRICT;

        $prefix = self::prefixForLevel($level);
        $start = self::startForLevel($level);
        $pattern = '/^'.preg_quote(rtrim($prefix, '_'), '/').'_(\d+)$/i';

        $max = $start - 1;

        $codes = collect()
            ->merge(User::where('instructor_code', 'like', $prefix.'%')->pluck('instructor_code'))
            ->merge(Cordinator::where('cordinator_code', 'like', $prefix.'%')->pluck('cordinator_code'))
            ->merge(CoordinatorRegistration::where('instructor_code', 'like', $prefix.'%')->pluck('instructor_code'));

        foreach ($codes as $code) {
            if (preg_match($pattern, (string) $code, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        $next = max($max + 1, $start);

        for ($i = 0; $i < 200; $i++) {
            $candidate = $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
            if (!self::exists($candidate)) {
                return $candidate;
            }
            $next++;
        }

        throw ValidationException::withMessages([
            'code' => ['Unable to generate coordinator code. Please try again.'],
        ]);
    }

    public static function exists(string $code): bool
    {
        return User::where('instructor_code', $code)->exists()
            || Cordinator::where('cordinator_code', $code)->exists()
            || CoordinatorRegistration::where('instructor_code', $code)->exists();
    }
}
