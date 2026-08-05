<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainer_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_cordinator_id')->nullable()->after('reference_by');
        });
    }

    public function down(): void
    {
        Schema::table('trainer_registrations', function (Blueprint $table) {
            $table->dropColumn('reference_cordinator_id');
        });
    }
};
