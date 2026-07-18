<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            $table->decimal('required_hours', 8, 2)->nullable()->after('working_days');
            $table->decimal('planned_hours', 8, 2)->nullable()->after('required_hours');
        });
    }

    public function down(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            $table->dropColumn(['required_hours', 'planned_hours']);
        });
    }
};
