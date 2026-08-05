<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'daily_training_hours')) {
                $table->decimal('daily_training_hours', 8, 2)->nullable()->after('training_hours');
            }
        });

        Schema::table('asigned_schools', function (Blueprint $table) {
            if (!Schema::hasColumn('asigned_schools', 'daily_training_hours')) {
                $table->decimal('daily_training_hours', 8, 2)->nullable()->after('required_hours');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'daily_training_hours')) {
                $table->dropColumn('daily_training_hours');
            }
        });

        Schema::table('asigned_schools', function (Blueprint $table) {
            if (Schema::hasColumn('asigned_schools', 'daily_training_hours')) {
                $table->dropColumn('daily_training_hours');
            }
        });
    }
};
