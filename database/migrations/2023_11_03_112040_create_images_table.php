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
    Schema::create('images', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        $table->string('ifsb_image')->nullable();
        $table->string('group_image')->nullable();
        $table->string('fst_aimage')->nullable();
        $table->string('snd_aimage')->nullable();
        $table->string('trd_aimage')->nullable();

        $table->string('cordinator')->nullable(); // Fixed typo: "coordinator" in code but DB has "cordinator"
        $table->string('district')->nullable();
        $table->string('bloack')->nullable(); // Fixed typo: "block" in code but DB has "bloack"
        $table->string('school_name')->nullable();
        $table->string('school_address')->nullable();

        $table->time('intime')->nullable();
        $table->time('outtime')->nullable();

        $table->string('created_date')->nullable();
        $table->string('route_date', 250)->nullable();

        $table->integer('status')->default(0);

        $table->string('image_note', 250)->nullable();

        $table->unsignedBigInteger('uploaded_user')->nullable();
        $table->unsignedBigInteger('school_id')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
