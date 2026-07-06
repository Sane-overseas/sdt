<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    // protected $fillable = ['blocks'];
    public $timestamps = false;
    protected $fillable = ['district_id', 'block'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}

