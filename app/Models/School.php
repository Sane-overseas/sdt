<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    //  protected $fillable = ['schools'];
    protected $fillable = [
        'district_id',
        'school_name',
        'school_code',
        'block',
        'total_students',
        'training_hours',
        'status',
        'image_status',
        'video_status',
        'completion_status',
        'distribution_status',
        'asigned_school',
        'paid_status',
    ];

    protected $casts = [
        'training_hours' => 'float',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'school_id');
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'school_id');
    }

    public function completions()
    {
        return $this->hasMany(Completion::class, 'school_id');
    }

    public function assignedSchools()
    {
        return $this->hasMany(AsignedSchool::class, 'school_name', 'school_name');
    }
}


