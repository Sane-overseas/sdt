<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Right-side state logo URL for the current state.
     * Uses uploaded logo, else public/images/{code}-logo.png if present.
     */
    public function logoUrl(): ?string
    {
        if ($this->logo) {
            return asset('storage/'.$this->logo);
        }

        $filename = strtolower($this->code).'-logo.png';
        if (is_file(public_path('images/'.$filename))) {
            return asset('images/'.$filename);
        }

        return null;
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function cordinators(): HasMany
    {
        return $this->hasMany(Cordinator::class);
    }

    /**
     * Most common schools.training_hours for this state (from school master table).
     * Null when no schools have hours set yet.
     */
    public function typicalTrainingHours(): ?float
    {
        $row = \Illuminate\Support\Facades\DB::table('schools')
            ->join('districts', 'districts.id', '=', 'schools.district_id')
            ->where('districts.state_id', $this->id)
            ->whereNotNull('schools.training_hours')
            ->select('schools.training_hours', \Illuminate\Support\Facades\DB::raw('COUNT(*) as cnt'))
            ->groupBy('schools.training_hours')
            ->orderByDesc('cnt')
            ->orderByDesc('schools.training_hours')
            ->first();

        if (!$row) {
            return null;
        }

        $hours = (float) $row->training_hours;

        return floor($hours) == $hours ? (float) (int) $hours : $hours;
    }
}
