<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('districts') && !Schema::hasColumn('districts', 'state_id')) {
            Schema::table('districts', function (Blueprint $table) {
                $table->unsignedBigInteger('state_id')->nullable()->after('id');
                $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'state_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('state_id')->nullable()->after('role');
                $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
            });
        }

        if (Schema::hasTable('cordinators') && !Schema::hasColumn('cordinators', 'state_id')) {
            Schema::table('cordinators', function (Blueprint $table) {
                $table->unsignedBigInteger('state_id')->nullable()->after('id');
                $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
            });
        }

        if (Schema::hasTable('holidays') && !Schema::hasColumn('holidays', 'state_id')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->unsignedBigInteger('state_id')->nullable()->after('id');
                $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['holidays', 'cordinators', 'users', 'districts'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'state_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropForeign(['state_id']);
                    $blueprint->dropColumn('state_id');
                });
            }
        }
    }
};
