<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Completion extends Model
{
    use HasFactory, BelongsToAcademicSession;

    protected $fillable = ['completions'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
