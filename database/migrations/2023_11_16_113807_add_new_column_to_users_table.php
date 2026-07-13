<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'block')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('block')->after('district')->nullable();
            });
        }

        if (!Schema::hasColumn('users', 'school_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('school_name')->after('block')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'school_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('school_name');
            });
        }

        if (Schema::hasColumn('users', 'block')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('block');
            });
        }
    }
};
