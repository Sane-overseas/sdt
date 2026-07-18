<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('testimonial_video')->nullable();
            $table->string('cordinator')->nullable();
            $table->string('district')->nullable();
            $table->string('bloack')->nullable();
            $table->string('school_name')->nullable();
            $table->string('school_address')->nullable();
            $table->time('intime')->nullable();
            $table->time('outtime')->nullable();
            $table->string('route_date')->nullable();
            $table->string('created_date')->nullable();
            $table->integer('status')->default(0);
            $table->string('testimonial_note')->nullable();
            $table->unsignedBigInteger('uploaded_user')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('academic_sessions')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'school_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
