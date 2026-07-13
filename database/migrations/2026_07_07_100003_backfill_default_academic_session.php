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

        $sessionId = DB::table('academic_sessions')
            ->where('is_active', true)
            ->value('id')
            ?? DB::table('academic_sessions')->orderByDesc('id')->value('id');

        if (!$sessionId) {
            return;
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
