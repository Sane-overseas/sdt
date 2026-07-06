<?php

// namespace App\Imports;

// use App\Models\School;
// use App\Models\District; // Ensure District model is used
// use Maatwebsite\Excel\Concerns\ToModel;


// class SchoolImport implements ToModel
// {
//     public function model(array $row)
//     {
//         // Validate that district_id exists
//         $district = District::find($row[0]);

//         if (!$district) {
//             throw new \Exception("Invalid district_id: " . $row[0]);
//         }

//         return new School([
//             'district_id'  => $row[0], // Ensure it's valid
//             'school_name'  => $row[1] ?? 'Unknown School',
//             'school_code'  => $row[2] ?? 'UNKNOWN',
//             'block'        => $row[3] ?? 'N/A',
//             'created_at'   => now(),
//             'updated_at'   => now(),
//         ]);
//     }



// }


namespace App\Imports;

use App\Models\School;
use App\Models\District;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;

class SchoolImport implements ToModel, WithHeadingRow, WithValidation, WithStartRow
{
    private $district_id;

    public function __construct($district_id)
    {
        $this->district_id = $district_id;
        Log::info("Importing for district ID: " . $this->district_id);
    }

    public function startRow(): int
    {
        return 2; // Skip the header row
    }

    public function rules(): array
    {
        return [
            'school_name' => 'required|string|max:255',
            'school_code' => 'required|max:255',
            'block' => 'required|string|max:255',
            'total_students' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'school_name.required' => 'School Name is required',
            'school_code.required' => 'School Code is required',
            'school_code.max' => 'School Code cannot exceed 255 characters',
            'block.required' => 'Block is required',
            'total_students.required' => 'Total Students is required',
            'total_students.integer' => 'Total Students must be a number',
            'total_students.min' => 'Total Students must be 0 or greater',
        ];
    }

    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['school_name']) && empty($row['school_code'])) {
            return null;
        }

        // Convert total_students to integer, handle empty values
        $totalStudents = $row['total_students'] ?? 0;
        if (is_string($totalStudents) && trim($totalStudents) === '') {
            $totalStudents = 0;
        } else {
            $totalStudents = (int)$totalStudents;
        }

        Log::info("Importing row:", [
            'district_id' => $this->district_id,
            'school_name' => $row['school_name'] ?? 'Unknown School',
            'school_code' => (string)($row['school_code'] ?? 'UNKNOWN'),
            'block' => $row['block'] ?? 'N/A',
            'total_students' => $totalStudents,
        ]);

        return new School([
            'district_id'  => $this->district_id,
            'school_name'  => $row['school_name'] ?? 'Unknown School',
            'school_code'  => (string)($row['school_code'] ?? 'UNKNOWN'),
            'block'        => $row['block'] ?? 'N/A',
            'total_students' => $totalStudents,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}
