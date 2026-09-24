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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pan_number')) {
                $table->string('pan_number', 20)->nullable()->after('aadhar_doc');
            }
            if (!Schema::hasColumn('users', 'pan_doc')) {
                $table->string('pan_doc', 191)->nullable()->after('pan_number');
            }
            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name', 191)->nullable()->after('pan_doc');
            }
            if (!Schema::hasColumn('users', 'account_holder_name')) {
                $table->string('account_holder_name', 191)->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('users', 'account_number')) {
                $table->string('account_number', 50)->nullable()->after('account_holder_name');
            }
            if (!Schema::hasColumn('users', 'ifsc_code')) {
                $table->string('ifsc_code', 20)->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('users', 'passbook_doc')) {
                $table->string('passbook_doc', 191)->nullable()->after('ifsc_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'pan_number',
                'pan_doc',
                'bank_name',
                'account_holder_name',
                'account_number',
                'ifsc_code',
                'passbook_doc',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
