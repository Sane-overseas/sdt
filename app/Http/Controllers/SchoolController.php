<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;
use App\Models\Block;
use App\Models\School;
use App\Models\Completion;
use App\Models\AsignedSchool;
use App\Imports\SchoolImport;
use App\Services\StateService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    // Show the add school form
    public function create()
    {
        $district['districts'] = StateService::districtsQuery()->orderBy('district')->get();

        return view('admin.add', $district);
    }

    // Fetch districts dynamically
    public function fetchDistricts()
    {
        return response()->json(StateService::districtsQuery()->orderBy('district')->get());
    }



    public function storeDistrict(Request $request)
{
    try {
        $stateId = StateService::scopeStateId();
        if (!$stateId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a state first.',
            ], 422);
        }

        $request->validate([
            'district' => [
                'required',
                'string',
                Rule::unique('districts', 'district')->where(fn ($query) => $query->where('state_id', $stateId)),
            ],
        ]);

        $district = District::create([
            'district' => $request->district,
            'state_id' => $stateId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'District added successfully!',
            'district' => $district
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}





    // Fetch blocks based on selected district
    public function fetchBlocks($district_id)
    {
        StateService::assertDistrictInScope((int) $district_id);

        return response()->json(Block::where('district_id', $district_id)->get());
    }



    public function storeBlock(Request $request)
    {
        try {
            $request->validate([
                'district_id' => 'required|exists:districts,id',
                'block' => 'required|string|unique:blocks,block,NULL,id,district_id,' . $request->district_id
            ]);

            $block = Block::create([
                'district_id' => $request->district_id,
                'block' => $request->block
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Block added successfully!',
                'block' => $block
            ]);
        } catch (\Exception $e) {
            // Log::error('Block store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }



    // Add a new school
    public function store(Request $request)
    {
        StateService::assertDistrictInScope((int) $request->district_id);

        $school = new School();
        $school->district_id = $request->district_id;
        $school->block = $request->block;
        $school->school_name = $request->school_name;
        $school->school_code = $request->school_code;
        $school->total_students = $request->total_students;
        $school->save();

        return redirect()->back()->with('success', 'School added successfully!');
    }




    public function import(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'file' => 'required|file|mimes:xlsx,csv,xls'
        ]);

        StateService::assertDistrictInScope((int) $request->district_id);

        try {
            // Import the schools
            Excel::import(new SchoolImport($request->district_id), $request->file('file'));
            return redirect()->back()->with('success', 'Schools imported successfully into the selected district!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->back()->with('error', 'Validation errors: ' . implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error importing schools: ' . $e->getMessage());
        }
    }
    public function showImportForm()
    {
        $districts = StateService::districtsQuery()->orderBy('district')->get();

        return view('schools.import', compact('districts'));
    }

    public function downloadTemplate()
    {
        // Create template data with headers and sample rows
        $templateData = [
            ['ABC Primary School', '12345', 'Block A', '150'],
            ['XYZ High School', '67890', 'Block B', '300'],
            ['Sample Elementary School', '11111', 'Block C', '200'],
            ['Demo Secondary School', '22222', 'Block D', '450'],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
        ];

        return Excel::download(new class($templateData) implements FromArray, WithHeadings, WithStyles {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'School Name',
                    'School Code',
                    'Block',
                    'Total Students'
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    // Style the header row
                    1 => [
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => 'solid',
                            'startColor' => ['rgb' => '1976D2']
                        ],
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center'
                        ]
                    ],
                    // Set column widths
                    'A' => ['width' => 25],
                    'B' => ['width' => 15],
                    'C' => ['width' => 15],
                    'D' => ['width' => 15],
                ];
            }
        }, 'school_import_template.xlsx');
    }










    //     public function deleteDistrict($id)
    // {
    //     $district = District::find($id);
    //     if (!$district) {
    //         return response()->json(['message' => 'District not found'], 404);
    //     }

    //     // Delete all related blocks
    //     Block::where('district_id', $id)->delete();

    //     // Now delete the district
    //     $district->delete();

    //     return response()->json(['message' => 'District and all related blocks deleted successfully']);
    // }

    public function deleteBlock($id)
    {
        $block = Block::findOrFail($id);
        $block->delete();

        return response()->json(['message' => 'Block deleted successfully!']);
    }

    public function manageSchools()
    {
        $districts = StateService::districtsQuery()->orderBy('district')->get();
        $schools = StateService::schoolsQuery()
            ->with(['images', 'videos', 'completions', 'assignedSchools.user'])
            ->get()
            ->map(fn ($school) => $this->enrichSchoolMeta($school));

        return view('admin.manageschool', compact('districts', 'schools'));
    }

    public function exportSchools(Request $request)
    {
        $schools = School::with(['district', 'images', 'videos', 'completions', 'assignedSchools.user'])
            ->get()
            ->map(fn ($school) => $this->enrichSchoolMeta($school));

        $filteredSchools = $this->filterSchoolsCollection($schools, $request);

        $rows = $filteredSchools->values()->map(function ($school, $index) {
            return [
                $index + 1,
                optional($school->district)->district ?? '',
                $school->block ?? '',
                $school->school_code ?? '',
                $school->school_name ?? '',
                $school->total_students ?? 0,
                $this->getTrainerNameForSchool($school) ?? '',
            ];
        })->toArray();

        return Excel::download(new class($rows) implements FromArray, WithHeadings, WithStyles {
            private $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'S. No',
                    'District Name',
                    'Block Name',
                    'School Code',
                    'School Name',
                    'Total Girls',
                    'Trainer Name',
                ];
            }

            public function styles(Worksheet $sheet)
            {
                return [
                    1 => [
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => 'solid',
                            'startColor' => ['rgb' => '4CAF50']
                        ],
                        'alignment' => [
                            'horizontal' => 'center',
                            'vertical' => 'center'
                        ]
                    ],
                    'A' => ['width' => 8],
                    'B' => ['width' => 18],
                    'C' => ['width' => 18],
                    'D' => ['width' => 18],
                    'E' => ['width' => 35],
                    'F' => ['width' => 15],
                    'G' => ['width' => 20],
                ];
            }
        }, 'schools_export.xlsx');
    }

    // Filter schools by district
    public function filterByDistrict($district_id)
    {
        StateService::assertDistrictInScope((int) $district_id);
        $schools = School::where('district_id', $district_id)->get();

        return response()->json($schools);
    }

    // Update school function
    public function update($id)

    {
        $data = School::where("id", "=", $id)->get();
        return view("admin.updateschool", ['school' => $data]);
    }

    public function updateschool(Request $request)
    {
        $school = School::find($request->id);

        if (!$school) {
            return redirect("admin/manageschool")->with("error", "School not found!");
        }

        $school->block = $request->block;
        $school->school_name = $request->school_name;
        $school->school_code = $request->school_code;
        $school->total_students = $request->total_students;

        $school->save();

        return redirect("admin/manageschool")->with("success", "School updated successfully!");
    }
    //  end Update school function



    // Delete school
    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|exists:schools,id']);
        School::findOrFail($request->id)->delete();
        return response()->json(['success' => true, 'message' => 'School deleted successfully']);
    }
    // end Delete school
    private function enrichSchoolMeta(School $school): School
    {
        $statusValues = $this->getSchoolStatusValues($school);
        $school->image_status_value = $statusValues['image'];
        $school->video_status_value = $statusValues['video'];
        $school->uc_status_value = $statusValues['uc'];
        $school->trainer_name = $this->getTrainerNameForSchool($school);

        return $school;
    }

    private function getSchoolStatusValues(School $school): array
    {
        $latestImage = $school->images()->orderBy('created_at', 'desc')->first();
        $latestVideo = $school->videos()->orderBy('created_at', 'desc')->first();
        $latestCompletion = $school->completions()->orderBy('created_at', 'desc')->first();

        return [
            'image' => $latestImage->status ?? null,
            'video' => $latestVideo->status ?? null,
            'uc' => $latestCompletion->status ?? null,
        ];
    }

    private function getTrainerNameForSchool(School $school): ?string
    {
        $assignedSchool = AsignedSchool::where(function ($query) use ($school) {
            $query->where('school_name', $school->school_name)
                ->orWhere('school_name', (string)$school->id);
        })->with('user')->orderBy('created_at', 'desc')->first();

        if ($assignedSchool && $assignedSchool->user) {
            return $assignedSchool->user->instructor_name ?? null;
        }

        return null;
    }

    private function filterSchoolsCollection(Collection $schools, Request $request): Collection
    {
        $search = strtolower(trim($request->input('search', '')));
        $districtId = $request->input('district_id');
        $trainer = strtolower(trim($request->input('trainer', '')));
        $block = strtolower(trim($request->input('block', '')));
        $imageStatus = $request->filled('image_status') ? $request->input('image_status') : null;
        $videoStatus = $request->filled('video_status') ? $request->input('video_status') : null;
        $ucStatus = $request->filled('uc_status') ? $request->input('uc_status') : null;

        return $schools->filter(function ($school) use ($search, $districtId, $trainer, $block, $imageStatus, $videoStatus, $ucStatus) {
            $schoolName = strtolower($school->school_name ?? '');
            $schoolCode = strtolower($school->school_code ?? '');
            $trainerName = strtolower($school->trainer_name ?? '');
            $blockName = strtolower($school->block ?? '');

            if ($search && !str_contains($schoolName, $search) && !str_contains($schoolCode, $search)) {
                return false;
            }

            if ($districtId && (string) $school->district_id !== (string) $districtId) {
                return false;
            }

            if ($trainer && !str_contains($trainerName, $trainer)) {
                return false;
            }

            if ($block && !str_contains($blockName, $block)) {
                return false;
            }

            if ($imageStatus !== null && $this->normalizeStatusValue($school->image_status_value) !== $imageStatus) {
                return false;
            }

            if ($videoStatus !== null && $this->normalizeStatusValue($school->video_status_value) !== $videoStatus) {
                return false;
            }

            if ($ucStatus !== null && $this->normalizeStatusValue($school->uc_status_value) !== $ucStatus) {
                return false;
            }

            return true;
        });
    }

    private function normalizeStatusValue($value): string
    {
        return $value === null ? 'null' : (string) $value;
    }
}
