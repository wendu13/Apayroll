<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('cutoff_schedule_id');
            $table->unsignedBigInteger('schedule_file_id')->nullable();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('type', ['work', 'rest'])->default('work');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('cutoff_schedule_id')->references('id')->on('cutoff_schedules')->onDelete('cascade');
            $table->foreign('schedule_file_id')->references('id')->on('schedule_files')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_schedules');
    }
};