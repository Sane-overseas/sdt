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

        $stateId = DB::table('states')->where('code', 'S1')->value('id');

        if (!$stateId) {
            $stateId = DB::table('states')->insertGetId([
                'name' => 'State 1',
                'code' => 'S1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
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
