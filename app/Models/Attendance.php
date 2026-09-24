<?php

namespace App\Models;

use App\Models\Concerns\BelongsToAcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, BelongsToAcademicSession;

    protected $fillable = [
        'user_id',
        'uploaded_user',
        'cordinator',
        'district',
        'block',
        'school_name',
        'school_address',
        'intime',
        'outtime',
        'route_date',
        'created_date',
        'status',
        'attendance_note',
        'attendance_file',
        'attendance_files',
        'school_id',
        'session_id',
    ];

    protected $casts = [
        'attendance_files' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Get an array of all uploaded attendance files (multi-page images or PDF).
     *
     * @return array<int, string>
     */
    public function getAllFiles(): array
    {
        if (!empty($this->attendance_files) && is_array($this->attendance_files)) {
            return array_values(array_filter($this->attendance_files));
        }

        if (!empty($this->attendance_file)) {
            return [$this->attendance_file];
        }

        return [];
    }
}
