<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('holidays', 'date') && !Schema::hasColumn('holidays', 'holiday_date')) {
            DB::statement('ALTER TABLE holidays CHANGE `date` `holiday_date` DATE NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('holidays', 'holiday_date') && !Schema::hasColumn('holidays', 'date')) {
            DB::statement('ALTER TABLE holidays CHANGE `holiday_date` `date` DATE NOT NULL');
        }
    }
};
