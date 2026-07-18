<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Block;
use App\Models\Cordinator;
use App\Models\District;
use App\Models\School;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        AcademicSession::firstOrCreate(
            ['name' => '2026-27'],
            [
                'start_date' => '2026-04-01',
                'end_date' => '2027-03-31',
                'is_active' => true,
                'status' => 'active',
            ]
        );

        $states = [
            [
                'name' => 'HIMACHAL PRADESH',
                'code' => 'HP',
                'districts' => [
                    [
                        'district' => 'Shimla',
                        'blocks' => ['Mashobra', 'Theog'],
                        'schools' => [
                            ['school_name' => 'GSSS Mashobra', 'school_code' => 'HP-SHI-001', 'block' => 'Mashobra', 'total_students' => 120],
                            ['school_name' => 'GHS Theog', 'school_code' => 'HP-SHI-002', 'block' => 'Theog', 'total_students' => 90],
                            ['school_name' => 'GPS Chotta Shimla', 'school_code' => 'HP-SHI-003', 'block' => 'Mashobra', 'total_students' => 60],
                        ],
                    ],
                    [
                        'district' => 'Kangra',
                        'blocks' => ['Dharamshala', 'Palampur'],
                        'schools' => [
                            ['school_name' => 'GSSS Dharamshala', 'school_code' => 'HP-KAN-001', 'block' => 'Dharamshala', 'total_students' => 150],
                            ['school_name' => 'GHS Palampur', 'school_code' => 'HP-KAN-002', 'block' => 'Palampur', 'total_students' => 110],
                        ],
                    ],
                ],
                'coordinators' => [
                    [
                        'cordinator_name' => 'HP Coordinator',
                        'cordinator_code' => 'HP-CORD-01',
                        'email' => 'coordinator.hp@sdt.local',
                        'instructor_code' => 'HPC01',
                        'instructor_number' => '9800000001',
                        'district' => 'Shimla',
                    ],
                ],
                'trainers' => [
                    [
                        'instructor_name' => 'HP Trainer One',
                        'email' => 'trainer1.hp@sdt.local',
                        'instructor_code' => 'HPT01',
                        'instructor_number' => '9800000011',
                        'district' => 'Shimla',
                        'cordinator_code' => 'HP-CORD-01',
                        'amount' => 15000,
                    ],
                    [
                        'instructor_name' => 'HP Trainer Two',
                        'email' => 'trainer2.hp@sdt.local',
                        'instructor_code' => 'HPT02',
                        'instructor_number' => '9800000012',
                        'district' => 'Kangra',
                        'cordinator_code' => 'HP-CORD-01',
                        'amount' => 15000,
                    ],
                ],
            ],
            [
                'name' => 'HARYANA',
                'code' => 'HR',
                'districts' => [
                    [
                        'district' => 'Gurugram',
                        'blocks' => ['Sohna', 'Pataudi'],
                        'schools' => [
                            ['school_name' => 'GSSS Sohna', 'school_code' => 'HR-GUR-001', 'block' => 'Sohna', 'total_students' => 200],
                            ['school_name' => 'GHS Pataudi', 'school_code' => 'HR-GUR-002', 'block' => 'Pataudi', 'total_students' => 140],
                            ['school_name' => 'GPS Sector 14', 'school_code' => 'HR-GUR-003', 'block' => 'Sohna', 'total_students' => 80],
                        ],
                    ],
                    [
                        'district' => 'Faridabad',
                        'blocks' => ['Ballabgarh', 'Tigaon'],
                        'schools' => [
                            ['school_name' => 'GSSS Ballabgarh', 'school_code' => 'HR-FAR-001', 'block' => 'Ballabgarh', 'total_students' => 180],
                            ['school_name' => 'GHS Tigaon', 'school_code' => 'HR-FAR-002', 'block' => 'Tigaon', 'total_students' => 100],
                        ],
                    ],
                ],
                'coordinators' => [
                    [
                        'cordinator_name' => 'HR Coordinator',
                        'cordinator_code' => 'HR-CORD-01',
                        'email' => 'coordinator.hr@sdt.local',
                        'instructor_code' => 'HRC01',
                        'instructor_number' => '9800000002',
                        'district' => 'Gurugram',
                    ],
                ],
                'trainers' => [
                    [
                        'instructor_name' => 'HR Trainer One',
                        'email' => 'trainer1.hr@sdt.local',
                        'instructor_code' => 'HRT01',
                        'instructor_number' => '9800000021',
                        'district' => 'Gurugram',
                        'cordinator_code' => 'HR-CORD-01',
                        'amount' => 15000,
                    ],
                    [
                        'instructor_name' => 'HR Trainer Two',
                        'email' => 'trainer2.hr@sdt.local',
                        'instructor_code' => 'HRT02',
                        'instructor_number' => '9800000022',
                        'district' => 'Faridabad',
                        'cordinator_code' => 'HR-CORD-01',
                        'amount' => 15000,
                    ],
                ],
            ],
        ];

        foreach ($states as $stateData) {
            $state = State::updateOrCreate(
                ['code' => $stateData['code']],
                [
                    'name' => $stateData['name'],
                    'is_active' => true,
                ]
            );

            $districtIds = [];

            foreach ($stateData['districts'] as $districtData) {
                $district = District::updateOrCreate(
                    [
                        'district' => $districtData['district'],
                        'state_id' => $state->id,
                    ],
                    ['state_id' => $state->id]
                );

                $districtIds[$districtData['district']] = $district->id;

                foreach ($districtData['blocks'] as $blockName) {
                    Block::updateOrCreate(
                        [
                            'district_id' => $district->id,
                            'block' => $blockName,
                        ],
                        []
                    );
                }

                foreach ($districtData['schools'] as $schoolData) {
                    School::updateOrCreate(
                        ['school_code' => $schoolData['school_code']],
                        [
                            'district_id' => $district->id,
                            'school_name' => $schoolData['school_name'],
                            'block' => $schoolData['block'],
                            'total_students' => $schoolData['total_students'],
                            'training_hours' => $schoolData['training_hours'] ?? 40,
                            'status' => 0,
                            'asigned_school' => 0,
                        ]
                    );
                }

                // Extra schools per block for UI/testing (grid checklist, many schools)
                if ($stateData['code'] === 'HP' && $districtData['district'] === 'Shimla') {
                    $this->seedBlockSchools($district->id, 'Mashobra', 'HP-SHI-M', 20);
                    $this->seedBlockSchools($district->id, 'Theog', 'HP-SHI-T', 15);
                }
                if ($stateData['code'] === 'HP' && $districtData['district'] === 'Kangra') {
                    $this->seedBlockSchools($district->id, 'Dharamshala', 'HP-KAN-D', 18);
                    $this->seedBlockSchools($district->id, 'Palampur', 'HP-KAN-P', 12);
                }
                if ($stateData['code'] === 'HR' && $districtData['district'] === 'Gurugram') {
                    $this->seedBlockSchools($district->id, 'Sohna', 'HR-GUR-S', 22);
                    $this->seedBlockSchools($district->id, 'Pataudi', 'HR-GUR-P', 14);
                }
                if ($stateData['code'] === 'HR' && $districtData['district'] === 'Faridabad') {
                    $this->seedBlockSchools($district->id, 'Ballabgarh', 'HR-FAR-B', 16);
                    $this->seedBlockSchools($district->id, 'Tigaon', 'HR-FAR-T', 12);
                }
            }

            $cordinatorIds = [];

            foreach ($stateData['coordinators'] as $coordData) {
                $cordinator = Cordinator::updateOrCreate(
                    [
                        'cordinator_code' => $coordData['cordinator_code'],
                        'state_id' => $state->id,
                    ],
                    [
                        'cordinator_name' => $coordData['cordinator_name'],
                        'state_id' => $state->id,
                    ]
                );

                $cordinatorIds[$coordData['cordinator_code']] = $cordinator->id;

                $coordUser = User::updateOrCreate(
                    ['email' => $coordData['email']],
                    [
                        'instructor_name' => $coordData['cordinator_name'],
                        'instructor_code' => $coordData['instructor_code'],
                        'password' => 'password',
                        'instructor_number' => $coordData['instructor_number'],
                        'cordinator_id' => $cordinator->id,
                        'state_id' => $state->id,
                        'district' => $coordData['district'],
                        'amount' => 0,
                        'extra_amount' => 0,
                    ]
                );
                $coordUser->forceFill([
                    'role' => 2,
                    'active_status' => 1,
                ])->save();
            }

            foreach ($stateData['trainers'] as $trainerData) {
                $trainer = User::updateOrCreate(
                    ['email' => $trainerData['email']],
                    [
                        'instructor_name' => $trainerData['instructor_name'],
                        'instructor_code' => $trainerData['instructor_code'],
                        'password' => 'password',
                        'instructor_number' => $trainerData['instructor_number'],
                        'cordinator_id' => $cordinatorIds[$trainerData['cordinator_code']],
                        'state_id' => $state->id,
                        'district' => $trainerData['district'],
                        'amount' => $trainerData['amount'],
                        'extra_amount' => 0,
                    ]
                );
                $trainer->forceFill([
                    'role' => 0,
                    'active_status' => 1,
                ])->save();
            }
        }
    }
    private function seedBlockSchools(int $districtId, string $block, string $codePrefix, int $count): void
    {
        $types = ['GPS', 'GMS', 'GSSS', 'GHS'];

        for ($i = 1; $i <= $count; $i++) {
            $type = $types[($i - 1) % count($types)];
            $code = $codePrefix.str_pad((string) $i, 3, '0', STR_PAD_LEFT);

            School::updateOrCreate(
                ['school_code' => $code],
                [
                    'district_id' => $districtId,
                    'school_name' => $type.' '.$block.' School '.$i,
                    'block' => $block,
                    'total_students' => 40 + ($i * 3),
                    'training_hours' => 35 + ($i % 10),
                    'status' => 0,
                    'asigned_school' => 0,
                ]
            );
        }
    }
}
