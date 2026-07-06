<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            $table->integer('working_days')->nullable()->after('route_date');
        });
    }

    public function down(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            $table->dropColumn('working_days');
        });
    }
};
