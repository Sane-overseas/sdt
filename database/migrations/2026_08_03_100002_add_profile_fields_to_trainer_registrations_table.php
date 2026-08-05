<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainer_registrations', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('instructor_name');
            $table->text('address')->nullable()->after('instructor_number');
            $table->string('martial_art_type')->nullable()->after('block');
            $table->string('blood_group')->nullable()->after('martial_art_type');
            $table->string('reference_by')->nullable()->after('blood_group');
            $table->text('comment')->nullable()->after('reference_by');
            $table->string('aadhar_doc')->nullable()->after('comment');
            $table->string('qualification_doc')->nullable()->after('aadhar_doc');
            $table->string('martial_art_doc')->nullable()->after('qualification_doc');
            $table->string('photo')->nullable()->after('martial_art_doc');
            $table->text('admin_remarks')->nullable()->after('rejection_note');
            $table->string('edit_token', 64)->nullable()->unique()->after('admin_remarks');
        });
    }

    public function down(): void
    {
        Schema::table('trainer_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'father_name',
                'address',
                'martial_art_type',
                'blood_group',
                'reference_by',
                'comment',
                'aadhar_doc',
                'qualification_doc',
                'martial_art_doc',
                'photo',
                'admin_remarks',
                'edit_token',
            ]);
        });
    }
};
