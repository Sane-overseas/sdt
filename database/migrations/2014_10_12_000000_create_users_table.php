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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('instructor_name');
            $table->string('email')->unique();
            $table->string('instructor_code')->unique();
            $table->string('password', 250);
            $table->string('instructor_number')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->unsignedBigInteger('cordinator_id')->default(6);
            $table->integer('role')->default(0)->comment('1 - admin, 0 - users');
            $table->string('claim_note', 250)->nullable();
            $table->integer('salary_status')->default(0);
            $table->string('payment_history', 500)->nullable();
            $table->integer('amount')->nullable();
            $table->integer('paid_schools')->nullable();
            $table->string('district', 250)->nullable();
            $table->integer('school_assigned_status')->default(0);
            $table->integer('data_upload_status')->default(0);
            $table->integer('active_status')->default(1);
            $table->integer('extra_amount', false, true)->nullable();
            $table->integer('total_amount')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
