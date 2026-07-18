<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('schools', 'training_hours')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->decimal('training_hours', 8, 2)->nullable()->after('total_students');
            });
        }

        if (Schema::hasTable('school_session_requirements')) {
            $rows = DB::table('school_session_requirements')
                ->select('school_id', DB::raw('MAX(training_hours) as training_hours'))
                ->groupBy('school_id')
                ->get();

            foreach ($rows as $row) {
                DB::table('schools')
                    ->where('id', $row->school_id)
                    ->update(['training_hours' => $row->training_hours]);
            }

            Schema::dropIfExists('school_session_requirements');
        }
    }

    public function down(): void
    {
        Schema::create('school_session_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->decimal('training_hours', 8, 2);
            $table->timestamps();
            $table->unique(['school_id', 'session_id']);
        });

        if (Schema::hasColumn('schools', 'training_hours')) {
            $schools = DB::table('schools')->whereNotNull('training_hours')->get(['id', 'training_hours']);
            $sessionId = DB::table('academic_sessions')->where('is_active', 1)->value('id')
                ?? DB::table('academic_sessions')->orderByDesc('id')->value('id');

            if ($sessionId) {
                foreach ($schools as $school) {
                    DB::table('school_session_requirements')->insert([
                        'school_id' => $school->id,
                        'session_id' => $sessionId,
                        'training_hours' => $school->training_hours,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            Schema::table('schools', function (Blueprint $table) {
                $table->dropColumn('training_hours');
            });
        }
    }
};
