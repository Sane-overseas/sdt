<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    public const TYPE_OFF = 'off';
    public const TYPE_WORKING = 'working';

    protected $fillable = [
        'holiday_date',
        'title',
        'entry_type',
        'state_id',
        'district_id',
        'created_by',
    ];

    protected $casts = [
        'holiday_date' => 'date:Y-m-d',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function isForceWorking(): bool
    {
        return $this->entry_type === self::TYPE_WORKING;
    }
}
