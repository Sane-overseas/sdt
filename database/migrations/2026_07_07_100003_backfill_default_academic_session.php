<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('academic_sessions')) {
            return;
        }

        $sessionId = DB::table('academic_sessions')->where('name', '2025-26')->value('id');

        if (!$sessionId) {
            $sessionId = DB::table('academic_sessions')->insertGetId([
                'name' => '2025-26',
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
                'is_active' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('academic_sessions')->where('id', $sessionId)->update([
                'is_active' => true,
                'status' => 'active',
                'updated_at' => now(),
            ]);
        }

        foreach (['asigned_schools', 'videos', 'images', 'completions', 'distributions', 'paid_schools', 'advance_payments'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'session_id')) {
                DB::table($table)->whereNull('session_id')->update(['session_id' => $sessionId]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
