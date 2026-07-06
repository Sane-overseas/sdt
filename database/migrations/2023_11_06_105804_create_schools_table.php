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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->string('school_name');
            $table->string('school_code');
            $table->string('block');
            $table->integer('status')->default(0);
            $table->integer('image_status')->default(0);
            $table->integer('video_status')->default(0);
            $table->integer('completion_status')->default(0);
            $table->integer('distribution_status')->default(0);
            $table->integer('asigned_school')->default(0);
            $table->integer('total_students')->nullable();
            $table->integer('paid_status')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
