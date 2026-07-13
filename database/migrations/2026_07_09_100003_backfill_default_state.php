<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('states')) {
            return;
        }

        $stateId = DB::table('states')
            ->where('is_active', true)
            ->orderBy('id')
            ->value('id');

        if (!$stateId) {
            return;
        }

        if (Schema::hasTable('districts') && Schema::hasColumn('districts', 'state_id')) {
            DB::table('districts')->whereNull('state_id')->update(['state_id' => $stateId]);
        }

        if (Schema::hasTable('cordinators') && Schema::hasColumn('cordinators', 'state_id')) {
            DB::table('cordinators')->whereNull('state_id')->update(['state_id' => $stateId]);
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'state_id')) {
            DB::table('users')
                ->whereIn('role', [0, 2])
                ->whereNull('state_id')
                ->update(['state_id' => $stateId]);
        }

        if (Schema::hasTable('holidays') && Schema::hasColumn('holidays', 'state_id')) {
            DB::table('holidays')->whereNull('state_id')->update(['state_id' => $stateId]);
        }
    }

    public function down(): void
    {
        //
    }
};
