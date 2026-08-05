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
        'father_name',
        'instructor_code',
        'email',
        'password',
        'instructor_number',
        'aadhar_number',
        'address',
        'cordinator_id',
        'state_id',
        'district',
        'block',
        'martial_art_type',
        'blood_group',
        'comment',
        'aadhar_doc',
        'qualification_doc',
        'martial_art_doc',
        'photo',
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
        return $this->hasMany(AsignedSchool::class)
            ->where(function ($q) {
                $q->where('approval_status', 'approved')
                    ->orWhereNull('approval_status');
            });
    }

    public function allAsignedSchools()
    {
        return $this->hasMany(AsignedSchool::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
