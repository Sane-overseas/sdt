<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerRegistration extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_REVISION = 'revision';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'instructor_name',
        'father_name',
        'email',
        'instructor_code',
        'instructor_number',
        'aadhar_number',
        'address',
        'state_id',
        'district',
        'block',
        'martial_art_type',
        'blood_group',
        'reference_by',
        'reference_cordinator_id',
        'comment',
        'aadhar_doc',
        'qualification_doc',
        'martial_art_doc',
        'photo',
        'status',
        'rejection_note',
        'admin_remarks',
        'edit_token',
        'user_id',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRevision(): bool
    {
        return $this->status === self::STATUS_REVISION;
    }

    public function canBeEditedByTrainer(): bool
    {
        return $this->isRevision() && !empty($this->edit_token);
    }

    public function documentUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return asset('storage/'.$path);
    }
}
