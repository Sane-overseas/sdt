<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'aadhar_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('aadhar_number', 12)->nullable()->unique()->after('instructor_number');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'aadhar_number')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['aadhar_number']);
            $table->dropColumn('aadhar_number');
        });
    }
};
