<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory, BelongsToAcademicSession;

    protected $fillable = [
        'user_id',
        'testimonial_video',
        'cordinator',
        'district',
        'bloack',
        'school_name',
        'school_address',
        'intime',
        'outtime',
        'route_date',
        'created_date',
        'status',
        'testimonial_note',
        'uploaded_user',
        'school_id',
        'session_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
