<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            if (!Schema::hasColumn('asigned_schools', 'auth_letter_path')) {
                $table->string('auth_letter_path')->nullable()->after('approved_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            if (Schema::hasColumn('asigned_schools', 'auth_letter_path')) {
                $table->dropColumn('auth_letter_path');
            }
        });
    }
};
