<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cordinator extends Model
{
    use HasFactory;

     protected $fillable = [
        'cordinator_name',
        'cordinator_code',
        'state_id',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
