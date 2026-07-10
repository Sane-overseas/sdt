<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'asigned_schools',
        'videos',
        'images',
        'completions',
        'distributions',
        'paid_schools',
        'advance_payments',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'session_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('session_id')->nullable()->after('id');
                    $table->index('session_id');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'session_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['session_id']);
                    $table->dropColumn('session_id');
                });
            }
        }
    }
};
