<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\District;
use App\Models\School;
use App\Models\State;
use App\Models\User;
use App\Services\BlockSyncService;
use Illuminate\Database\Seeder;

class HaryanaDistrictBlockSeeder extends Seeder
{
    /**
     * Keep only Haryana districts/blocks that exist in the schools table
     * (plus optional JSON overrides for spelling).
     */
    public function run(): void
    {
        $state = State::firstOrCreate(
            ['code' => 'HR'],
            ['name' => 'Haryana', 'is_active' => true]
        );

        if ($state->name !== 'Haryana') {
            $state->name = 'Haryana';
            $state->save();
        }

        // Preferred spellings from JSON (only for districts that have schools).
        $path = database_path('data/haryana_districts_blocks.json');
        $jsonPairs = [];
        if (is_file($path)) {
            $jsonPairs = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR) ?: [];
        }

        // Build required map from schools table.
        $fromSchools = School::query()
            ->join('districts', 'districts.id', '=', 'schools.district_id')
            ->where('districts.state_id', $state->id)
            ->select('districts.id', 'districts.district', 'schools.block')
            ->get()
            ->groupBy('id');

        if ($fromSchools->isEmpty()) {
            // Fresh DB: seed JSON districts/blocks as starting list.
            $pairs = $jsonPairs;
        } else {
            $pairs = [];
            foreach ($fromSchools as $districtId => $rows) {
                $districtName = $rows->first()->district;
                $blockNames = $rows->pluck('block')
                    ->map(fn ($b) => trim((string) $b))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                // Prefer JSON spelling when present for this district.
                if (isset($jsonPairs[$districtName]) && is_array($jsonPairs[$districtName])) {
                    $preferred = [];
                    foreach ($blockNames as $schoolBlock) {
                        $match = collect($jsonPairs[$districtName])->first(
                            fn ($official) => BlockSyncService::normalizeKey($official) === BlockSyncService::normalizeKey($schoolBlock)
                        );
                        $preferred[] = $match ?: $schoolBlock;
                    }
                    $blockNames = array_values(array_unique($preferred));
                }

                $pairs[$districtName] = $blockNames;
            }
        }

        $districtsTouched = [];
        $blocksCreated = 0;
        $schoolsUpdated = 0;
        $usersUpdated = 0;
        $blocksRemoved = 0;
        $districtsRemoved = 0;

        foreach ($pairs as $districtName => $blockNames) {
            $districtName = trim((string) $districtName);
            $district = District::firstOrCreate(
                [
                    'state_id' => $state->id,
                    'district' => $districtName,
                ]
            );
            $districtsTouched[] = $district->id;

            $officialNames = [];
            foreach ((array) $blockNames as $blockName) {
                $blockName = trim((string) $blockName);
                if ($blockName === '') {
                    continue;
                }

                $block = Block::query()
                    ->where('district_id', $district->id)
                    ->get()
                    ->first(fn (Block $b) => BlockSyncService::normalizeKey((string) $b->block) === BlockSyncService::normalizeKey($blockName));

                if ($block) {
                    if ($block->block !== $blockName) {
                        School::where('district_id', $district->id)->where('block', $block->block)->update(['block' => $blockName]);
                        User::where('state_id', $state->id)
                            ->whereRaw('LOWER(TRIM(district)) = ?', [mb_strtolower($districtName)])
                            ->where('block', $block->block)
                            ->update(['block' => $blockName]);
                        $block->block = $blockName;
                        $block->save();
                    }
                } else {
                    $block = Block::create([
                        'district_id' => $district->id,
                        'block' => $blockName,
                    ]);
                    $blocksCreated++;
                }

                $officialNames[] = $block->block;
            }

            $officialKeys = collect($officialNames)
                ->map(fn ($n) => BlockSyncService::normalizeKey($n))
                ->flip()
                ->all();

            // Sync school/user spellings to blocks list.
            School::where('district_id', $district->id)->orderBy('id')->chunkById(200, function ($schools) use ($officialNames, &$schoolsUpdated) {
                $byKey = [];
                foreach ($officialNames as $name) {
                    $byKey[BlockSyncService::normalizeKey($name)] = $name;
                }
                foreach ($schools as $school) {
                    $raw = trim((string) ($school->block ?? ''));
                    if ($raw === '') {
                        continue;
                    }
                    $key = BlockSyncService::normalizeKey($raw);
                    if (isset($byKey[$key]) && $school->block !== $byKey[$key]) {
                        $school->block = $byKey[$key];
                        $school->save();
                        $schoolsUpdated++;
                    }
                }
            });

            User::where('state_id', $state->id)
                ->whereRaw('LOWER(TRIM(district)) = ?', [mb_strtolower($districtName)])
                ->orderBy('id')
                ->chunkById(200, function ($users) use ($officialNames, &$usersUpdated) {
                    $byKey = [];
                    foreach ($officialNames as $name) {
                        $byKey[BlockSyncService::normalizeKey($name)] = $name;
                    }
                    foreach ($users as $user) {
                        $raw = trim((string) ($user->block ?? ''));
                        if ($raw === '') {
                            continue;
                        }
                        $key = BlockSyncService::normalizeKey($raw);
                        if (isset($byKey[$key]) && $user->block !== $byKey[$key]) {
                            $user->block = $byKey[$key];
                            $user->save();
                            $usersUpdated++;
                        }
                    }
                });

            // Remove blocks not present in schools for this district.
            foreach (Block::where('district_id', $district->id)->get() as $block) {
                $key = BlockSyncService::normalizeKey((string) $block->block);
                if (! isset($officialKeys[$key])) {
                    $block->delete();
                    $blocksRemoved++;
                }
            }
        }

        // Remove Haryana districts that have no schools.
        $unusedDistricts = District::where('state_id', $state->id)
            ->whereNotIn('id', $districtsTouched ?: [0])
            ->get();

        foreach ($unusedDistricts as $district) {
            if (School::where('district_id', $district->id)->exists()) {
                continue;
            }
            Block::where('district_id', $district->id)->delete();
            $district->delete();
            $districtsRemoved++;
        }

        $this->command?->info(
            "Haryana (schools-only): kept ".count($districtsTouched)." districts, ".
            "+{$blocksCreated} blocks, {$schoolsUpdated} schools synced, {$usersUpdated} users synced, ".
            "{$blocksRemoved} unused blocks removed, {$districtsRemoved} empty districts removed."
        );
    }
}
