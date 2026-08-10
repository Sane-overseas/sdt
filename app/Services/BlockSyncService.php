<?php

namespace App\Services;

use App\Models\Block;
use App\Models\School;
use Illuminate\Support\Collection;

class BlockSyncService
{
    /**
     * Resolve Excel/manual block text to the official name in `blocks`.
     * Matches existing blocks (case/alias insensitive). Creates the block if missing.
     */
    public static function resolveOrCreate(int $districtId, string $blockName): string
    {
        $blockName = trim($blockName);
        if ($blockName === '') {
            return $blockName;
        }

        $existing = self::findMatchingBlock($districtId, $blockName);
        if ($existing) {
            return $existing->block;
        }

        $official = self::formatBlockName($blockName);

        $block = Block::firstOrCreate(
            [
                'district_id' => $districtId,
                'block' => $official,
            ]
        );

        return $block->block;
    }

    /** Find an existing district block that matches the given name (aliases included). */
    public static function findMatchingBlock(int $districtId, string $blockName): ?Block
    {
        $want = self::normalizeKey($blockName);
        if ($want === '') {
            return null;
        }

        return Block::where('district_id', $districtId)
            ->get()
            ->first(fn (Block $b) => self::normalizeKey((string) $b->block) === $want);
    }

    /**
     * What spelling would be used for this block name (existing block preferred).
     * Does not create rows.
     */
    public static function resolveSpelling(int $districtId, string $blockName): ?string
    {
        $blockName = trim($blockName);
        if ($blockName === '') {
            return null;
        }

        $existing = self::findMatchingBlock($districtId, $blockName);

        return $existing?->block ?? self::formatBlockName($blockName);
    }

    /**
     * Re-sync all schools in a district so schools.block matches blocks.block.
     * Creates missing blocks from school names when needed.
     */
    public static function syncDistrictSchools(int $districtId): int
    {
        $updated = 0;

        School::where('district_id', $districtId)
            ->orderBy('id')
            ->chunkById(200, function (Collection $schools) use ($districtId, &$updated) {
                foreach ($schools as $school) {
                    $raw = trim((string) ($school->block ?? ''));
                    if ($raw === '') {
                        continue;
                    }

                    $official = self::resolveOrCreate($districtId, $raw);
                    if ($school->block !== $official) {
                        $school->block = $official;
                        $school->save();
                        $updated++;
                    }
                }
            });

        return $updated;
    }

    public static function normalizeKey(string $block): string
    {
        $key = preg_replace('/[^a-z0-9]+/', '', mb_strtolower(trim($block))) ?? '';

        $aliases = [
            'gurgaon' => 'gurugram',
            'fnagar' => 'farrukhnagar',
            'farnagar' => 'farrukhnagar',
            'farukhnagar' => 'farrukhnagar',
            'mahendergarh' => 'mahendragarh',
            'mahendergar' => 'mahendragarh',
            'nangalchoud' => 'nangalchoudhary',
            'fpjhirka' => 'ferozepurjhirka',
            'ferozpurjhirka' => 'ferozepurjhirka',
            'nathusaricho' => 'nathusarichopta',
            'sonepat' => 'sonipat',
            'sanaulikhur' => 'sanolikhur',
            'sanaulikhurd' => 'sanolikhurd',
            'pillukher' => 'pillukhera',
            'pillukhera' => 'pillukhera',
            'jhojhukalan' => 'jhojhukalan',
            'charkidadri' => 'charkhidadri',
            'charkhidadri' => 'charkhidadri',
        ];

        return $aliases[$key] ?? $key;
    }

    public static function matchKeys(string $block): array
    {
        $canonical = self::normalizeKey($block);

        $reverse = [
            'gurugram' => ['gurugram', 'gurgaon'],
            'farrukhnagar' => ['farrukhnagar', 'fnagar', 'farnagar', 'farukhnagar'],
            'mahendragarh' => ['mahendragarh', 'mahendergarh', 'mahendergar'],
            'nangalchoudhary' => ['nangalchoudhary', 'nangalchoud'],
            'ferozepurjhirka' => ['ferozepurjhirka', 'fpjhirka', 'ferozpurjhirka'],
            'nathusarichopta' => ['nathusarichopta', 'nathusaricho'],
            'sonipat' => ['sonipat', 'sonepat'],
        ];

        return array_values(array_unique($reverse[$canonical] ?? [$canonical]));
    }

    public static function formatBlockName(string $block): string
    {
        $block = trim(preg_replace('/\s+/', ' ', $block) ?? '');

        return mb_convert_case(mb_strtolower($block), MB_CASE_TITLE, 'UTF-8');
    }
}
