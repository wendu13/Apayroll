<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hrs', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->unique();
            $table->string('position');
            $table->string('department');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('security_question');
            $table->string('security_answer');
            $table->string('password');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hrs');
    }
};
