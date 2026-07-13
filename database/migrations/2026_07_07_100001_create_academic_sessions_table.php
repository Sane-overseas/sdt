<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up(): void

    {

        if (Schema::hasTable('academic_sessions')) {

            return;

        }



        Schema::create('academic_sessions', function (Blueprint $table) {

            $table->id();

            $table->string('name')->unique();

            $table->date('start_date')->nullable();

            $table->date('end_date')->nullable();

            $table->boolean('is_active')->default(false);

            $table->string('status')->default('active');

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

        });

    }



    public function down(): void

    {

        Schema::dropIfExists('academic_sessions');

    }

};

