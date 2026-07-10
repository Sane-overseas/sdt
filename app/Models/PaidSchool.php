<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidSchool extends Model
{
    use HasFactory, BelongsToAcademicSession;

     protected $fillable = ['paid_schools'];
}
