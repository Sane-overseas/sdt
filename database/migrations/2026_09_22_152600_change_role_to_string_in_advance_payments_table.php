<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('advance_payments') && Schema::hasColumn('advance_payments', 'role')) {
            DB::statement("ALTER TABLE `advance_payments` MODIFY `role` VARCHAR(50) NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('advance_payments') && Schema::hasColumn('advance_payments', 'role')) {
            DB::statement("ALTER TABLE `advance_payments` MODIFY `role` INT(11) NULL");
        }
    }
};
