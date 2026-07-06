<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            $user->total_amount = $user->amount + $user->extra_amount;
        });
    }

    protected $fillable = [
        'instructor_name',
        'instructor_code',
        'email',
        'password',
        'instructor_number',
        'cordinator_id',
        'district',
        'block',
        'school_name',
        'amount',
        'extra_amount',
        'total_amount'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function completions()
    {
        return $this->hasMany(Completion::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function asigned_schools()
    {
        return $this->hasMany(AsignedSchool::class);
    }
}
