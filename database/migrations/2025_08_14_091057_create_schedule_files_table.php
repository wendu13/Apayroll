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
        Schema::create('schedule_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->index('fk_schedule_files_employee');
            $table->unsignedBigInteger('cutoff_schedule_id')->index('fk_schedule_files_cutoff');
            $table->time('time_in');
            $table->time('time_out');
            $table->integer('weeks');
            $table->json('days_json')->nullable(); // stores the selected days data
            $table->timestamps();

            // Foreign keys
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('cutoff_schedule_id')->references('id')->on('cutoff_schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_files');
    }
};