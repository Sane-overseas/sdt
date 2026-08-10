<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'coordinator_level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('coordinator_level', 20)
                    ->default('district')
                    ->after('role')
                    ->comment('district | state — only meaningful for role=2');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'coordinator_level')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('coordinator_level');
            });
        }
    }
};
