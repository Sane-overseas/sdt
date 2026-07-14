<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('holidays')) {
            return;
        }

        // Allow same date for different districts / state-wide vs district-wise
        $this->dropHolidayDateUniqueIfExists();

        if (!Schema::hasColumn('holidays', 'district_id')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->unsignedBigInteger('district_id')->nullable()->after('state_id');
                $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
                $table->index(['state_id', 'district_id', 'holiday_date']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('holidays') && Schema::hasColumn('holidays', 'district_id')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->dropForeign(['district_id']);
                $table->dropIndex(['state_id', 'district_id', 'holiday_date']);
                $table->dropColumn('district_id');
            });
        }
    }

    private function dropHolidayDateUniqueIfExists(): void
    {
        $indexes = collect(DB::select('SHOW INDEX FROM holidays'))
            ->where('Non_unique', 0)
            ->where('Column_name', 'holiday_date')
            ->pluck('Key_name')
            ->unique()
            ->values();

        foreach ($indexes as $indexName) {
            if ($indexName === 'PRIMARY') {
                continue;
            }
            Schema::table('holidays', function (Blueprint $table) use ($indexName) {
                $table->dropUnique($indexName);
            });
        }
    }
};
