<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignedSchool extends Model
{
    use HasFactory;

    protected $fillable = ['asigned_schools'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
