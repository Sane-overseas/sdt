<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('trainer_registrations', 'aadhar_number')) {
            Schema::table('trainer_registrations', function (Blueprint $table) {
                $table->string('aadhar_number', 12)->nullable()->unique()->after('instructor_number');
            });

            return;
        }

        $hasUnique = collect(DB::select('SHOW INDEX FROM trainer_registrations WHERE Column_name = ?', ['aadhar_number']))
            ->contains(fn ($row) => (int) $row->Non_unique === 0);

        if (!$hasUnique) {
            Schema::table('trainer_registrations', function (Blueprint $table) {
                $table->unique('aadhar_number');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('trainer_registrations', 'aadhar_number')) {
            return;
        }

        Schema::table('trainer_registrations', function (Blueprint $table) {
            $table->dropUnique(['aadhar_number']);
            $table->dropColumn('aadhar_number');
        });
    }
};
