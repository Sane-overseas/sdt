<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            if (!Schema::hasColumn('asigned_schools', 'approval_status')) {
                $table->string('approval_status', 20)->default('approved')->after('status');
            }
            if (!Schema::hasColumn('asigned_schools', 'approval_note')) {
                $table->string('approval_note', 500)->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('asigned_schools', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approval_note');
            }
            if (!Schema::hasColumn('asigned_schools', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            }
        });

        // Existing admin-assigned rows stay approved
        DB::table('asigned_schools')
            ->whereNull('approval_status')
            ->orWhere('approval_status', '')
            ->update(['approval_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            foreach (['approval_status', 'approval_note', 'approved_at', 'approved_by'] as $col) {
                if (Schema::hasColumn('asigned_schools', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
