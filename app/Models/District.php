<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    // protected $fillable = ['districts'];
    public $timestamps = false; // Disable timestamps

    protected $fillable = ['district']; // Correct column name

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
