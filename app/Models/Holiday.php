<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'holiday_date',
        'title',
        'state_id',
        'created_by',
    ];

    protected $casts = [
        'holiday_date' => 'date:Y-m-d',
    ];
}
