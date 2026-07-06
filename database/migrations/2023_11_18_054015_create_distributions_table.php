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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('distribution_file')->nullable();
            $table->string('coordinator')->nullable();
            $table->string('district')->nullable();
            $table->string('block')->nullable();
            $table->string('school_name')->nullable();
            $table->string('school_address')->nullable();
            $table->time('intime')->nullable();
            $table->time('outtime')->nullable();
            $table->string('route_date')->nullable();
            $table->string('created_date')->nullable();
            $table->integer('status')->default(0);
            $table->string('distribution_note')->nullable();
            $table->string('complete_students')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('uploaded_user')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
