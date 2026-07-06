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
        // Schema::table('users', function (Blueprint $table) {
        //     $table->integer('district')->after('role')->nullable();
        //     $table->string('block')->after('district')->nullable();
        //     $table->string('school_name')->after('block')->nullable();
        // });
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'district')) {
                $table->integer('district')->after('role')->nullable();
            }
            if (!Schema::hasColumn('users', 'block')) {
                $table->string('block')->after('district')->nullable();
            }
            if (!Schema::hasColumn('users', 'school_name')) {
                $table->string('school_name')->after('block')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('district');
            $table->dropColumn('block');
            $table->dropColumn('school_name');
        });
    }
};
