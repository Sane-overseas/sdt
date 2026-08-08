<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\District;
use App\Models\State;
use Illuminate\Database\Seeder;

class HaryanaDistrictBlockSeeder extends Seeder
{
    /**
     * Seed Haryana districts/blocks extracted from elementary / pmshri / secondary PDFs.
     */
    public function run(): void
    {
        $path = database_path('data/haryana_districts_blocks.json');
        if (! is_file($path)) {
            $this->command?->error("Missing {$path}");

            return;
        }

        $pairs = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $state = State::firstOrCreate(
            ['code' => 'HR'],
            ['name' => 'Haryana', 'is_active' => true]
        );

        $districts = 0;
        $blocks = 0;

        foreach ($pairs as $districtName => $blockNames) {
            $district = District::firstOrCreate(
                [
                    'state_id' => $state->id,
                    'district' => mb_convert_case($districtName, MB_CASE_TITLE, 'UTF-8'),
                ]
            );
            if ($district->wasRecentlyCreated) {
                $districts++;
            }

            foreach ($blockNames as $blockName) {
                $block = Block::firstOrCreate(
                    [
                        'district_id' => $district->id,
                        'block' => mb_convert_case($blockName, MB_CASE_TITLE, 'UTF-8'),
                    ]
                );
                if ($block->wasRecentlyCreated) {
                    $blocks++;
                }
            }
        }

        $this->command?->info("Haryana: +{$districts} districts, +{$blocks} blocks (state #{$state->id}).");
    }
}
