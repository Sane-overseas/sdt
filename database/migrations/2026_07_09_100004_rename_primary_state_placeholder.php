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

        DB::table('states')
            ->where('code', 'PS')
            ->where('name', 'Primary State')
            ->update([
                'name' => 'State 1',
                'code' => 'S1',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
