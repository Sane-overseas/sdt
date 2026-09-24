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
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedBigInteger('uploaded_user')->nullable();
                $table->string('cordinator')->nullable();
                $table->string('district')->nullable();
                $table->string('block')->nullable();
                $table->string('school_name')->nullable();
                $table->string('school_address')->nullable();
                $table->time('intime')->nullable();
                $table->time('outtime')->nullable();
                $table->string('route_date')->nullable();
                $table->string('created_date')->nullable();
                $table->integer('status')->default(0);
                $table->string('attendance_note', 250)->nullable();
                $table->string('attendance_file')->nullable();
                $table->text('attendance_files')->nullable(); // JSON array for multi-page images/PDFs
                $table->unsignedBigInteger('school_id')->nullable();
                $table->unsignedBigInteger('session_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
