<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            if (!Schema::hasColumn('asigned_schools', 'claim_status')) {
                $table->tinyInteger('claim_status')->default(0)->after('paid_status');
            }
            if (!Schema::hasColumn('asigned_schools', 'claimed_at')) {
                $table->timestamp('claimed_at')->nullable()->after('claim_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asigned_schools', function (Blueprint $table) {
            if (Schema::hasColumn('asigned_schools', 'claim_status')) {
                $table->dropColumn('claim_status');
            }
            if (Schema::hasColumn('asigned_schools', 'claimed_at')) {
                $table->dropColumn('claimed_at');
            }
        });
    }
};
