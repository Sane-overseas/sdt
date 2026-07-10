<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    use HasFactory, BelongsToAcademicSession;

     protected $fillable = ['advance_payment'];
}
