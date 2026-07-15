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
}
