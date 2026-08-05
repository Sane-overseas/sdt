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
        'required_hours',
        'daily_training_hours',
        'planned_hours',
        'approval_status',
        'approval_note',
        'approved_at',
        'approved_by',
    ];

    public const APPROVAL_PENDING = 'pending';
    public const APPROVAL_APPROVED = 'approved';
    public const APPROVAL_REJECTED = 'rejected';

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_name', 'id');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', self::APPROVAL_PENDING);
    }

    public function isApproved(): bool
    {
        return ($this->approval_status ?? self::APPROVAL_APPROVED) === self::APPROVAL_APPROVED;
    }
}
