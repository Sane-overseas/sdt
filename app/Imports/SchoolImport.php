<?php

namespace App\Imports;

use App\Models\School;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Log;

class SchoolImport implements ToModel, WithHeadingRow, WithValidation, WithStartRow, SkipsEmptyRows
{
    private $district_id;

    public function __construct($district_id)
    {
        $this->district_id = $district_id;
        Log::info('Importing for district ID: '.$this->district_id);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function rules(): array
    {
        return [
            'school_name' => 'required|string|max:255',
            'school_code' => 'required|max:255',
            'block' => 'required|string|max:255',
            'total_students' => 'required|numeric|min:0',
            'total_training_hours' => 'required|numeric|min:0.5|max:9999',
            'daily_training_hours' => 'required|numeric|min:0.1|max:12',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'school_name.required' => 'School Name is required',
            'school_code.required' => 'School Code is required',
            'block.required' => 'Block is required',
            'total_students.required' => 'Total Students is required',
            'total_students.numeric' => 'Total Students must be a number',
            'total_training_hours.required' => 'Total Training Hours is required',
            'total_training_hours.numeric' => 'Total Training Hours must be a number',
            'daily_training_hours.required' => 'Daily Training Hours is required',
            'daily_training_hours.numeric' => 'Daily Training Hours must be a number',
        ];
    }

    public function model(array $row)
    {
        if ($this->isEmpty($row['school_name'] ?? null) && $this->isEmpty($row['school_code'] ?? null)) {
            return null;
        }

        $schoolCode = trim((string) ($row['school_code'] ?? ''));
        if ($schoolCode === '') {
            return null;
        }

        $payload = [
            'district_id' => $this->district_id,
            'school_name' => trim((string) ($row['school_name'] ?? 'Unknown School')),
            'school_code' => $schoolCode,
            'block' => trim((string) ($row['block'] ?? 'N/A')),
            'total_students' => $this->toInt($row['total_students'] ?? 0),
            'training_hours' => $this->toFloat(
                $row['total_training_hours'] ?? $row['training_hours'] ?? 60
            ),
            'daily_training_hours' => $this->toFloat(
                $row['daily_training_hours'] ?? $row['training_hours_day'] ?? 2
            ),
        ];

        Log::info('Importing/updating school row', $payload);

        // Same district + school_code => update; otherwise create
        School::updateOrCreate(
            [
                'district_id' => $this->district_id,
                'school_code' => $schoolCode,
            ],
            $payload
        );

        // Already persisted — avoid double insert by ToModel
        return null;
    }

    private function isEmpty($value): bool
    {
        return $value === null || trim((string) $value) === '';
    }

    private function toInt($value): int
    {
        if (is_string($value) && trim($value) === '') {
            return 0;
        }

        return (int) $value;
    }

    private function toFloat($value): float
    {
        if (is_string($value) && trim($value) === '') {
            return 0.0;
        }

        return (float) $value;
    }
}
