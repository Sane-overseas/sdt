<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('holidays')) {
            return;
        }

        if (!Schema::hasColumn('holidays', 'entry_type')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->string('entry_type', 20)->default('off')->after('title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('holidays') && Schema::hasColumn('holidays', 'entry_type')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->dropColumn('entry_type');
            });
        }
    }
};
