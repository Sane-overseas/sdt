<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('instructor_name');
            $table->string('email');
            $table->string('instructor_number')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('district')->nullable();
            $table->string('block')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_note')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_registrations');
    }
};
