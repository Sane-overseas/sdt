<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainer_registrations', function (Blueprint $table) {
            $table->string('instructor_code')->nullable()->unique()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('trainer_registrations', function (Blueprint $table) {
            $table->dropColumn('instructor_code');
        });
    }
};
