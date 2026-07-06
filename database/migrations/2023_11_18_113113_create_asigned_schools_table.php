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
        Schema::create('asigned_schools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('district')->nullable();
            $table->string('block')->nullable();
            $table->string('school_name')->nullable();
            $table->time('start_route_plan')->nullable();
            $table->time('end_route_plan')->nullable();
            $table->string('route_date')->nullable();
            $table->string('end_date', 250)->nullable();
            $table->string('remark', 550)->nullable();
            $table->integer('uc_submitted')->default(0);
            $table->integer('status')->nullable()->default(0);
            $table->integer('asigned_by')->nullable();
            $table->integer('paid_status')->default(0);
            $table->timestamp('add_route_plan_date')->nullable();
            $table->integer('added_by_route_plan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asigned_schools');
    }
};
