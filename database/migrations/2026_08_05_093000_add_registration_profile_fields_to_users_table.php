<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'father_name')) {
                $table->string('father_name')->nullable()->after('instructor_name');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('aadhar_number');
            }
            if (!Schema::hasColumn('users', 'martial_art_type')) {
                $table->string('martial_art_type')->nullable()->after('block');
            }
            if (!Schema::hasColumn('users', 'blood_group')) {
                $table->string('blood_group', 20)->nullable()->after('martial_art_type');
            }
            if (!Schema::hasColumn('users', 'comment')) {
                $table->text('comment')->nullable()->after('blood_group');
            }
            if (!Schema::hasColumn('users', 'aadhar_doc')) {
                $table->string('aadhar_doc')->nullable()->after('comment');
            }
            if (!Schema::hasColumn('users', 'qualification_doc')) {
                $table->string('qualification_doc')->nullable()->after('aadhar_doc');
            }
            if (!Schema::hasColumn('users', 'martial_art_doc')) {
                $table->string('martial_art_doc')->nullable()->after('qualification_doc');
            }
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('martial_art_doc');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [
                'father_name',
                'address',
                'martial_art_type',
                'blood_group',
                'comment',
                'aadhar_doc',
                'qualification_doc',
                'martial_art_doc',
                'photo',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
