<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignedSchool extends Model
{
    use HasFactory, BelongsToAcademicSession;

    protected $fillable = [
        'user_id',
        'district',
        'block',
        'school_name',
        'session_id',
        'start_route_plan',
        'end_route_plan',
        'route_date',
        'end_date',
        'remark',
        'uc_submitted',
        'status',
        'asigned_by',
        'paid_status',
        'add_route_plan_date',
        'added_by_route_plan',
        'working_days',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
