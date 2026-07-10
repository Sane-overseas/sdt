<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    use HasFactory, BelongsToAcademicSession;

    protected $fillable = ['distributions'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
