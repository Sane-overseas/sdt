<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coordinator_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('instructor_name');
            $table->string('father_name')->nullable();
            $table->string('email');
            $table->string('instructor_code')->nullable();
            $table->string('instructor_number')->nullable();
            $table->string('aadhar_number', 12)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('district')->nullable();
            $table->string('block')->nullable();
            $table->string('martial_art_type')->nullable();
            $table->string('blood_group', 20)->nullable();
            $table->text('comment')->nullable();
            $table->string('aadhar_doc')->nullable();
            $table->string('qualification_doc')->nullable();
            $table->string('martial_art_doc')->nullable();
            $table->string('photo')->nullable();
            $table->string('status')->default('pending'); // pending, revision, approved, rejected
            $table->text('rejection_note')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->string('edit_token', 64)->nullable()->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cordinator_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
            $table->index('state_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coordinator_registrations');
    }
};
