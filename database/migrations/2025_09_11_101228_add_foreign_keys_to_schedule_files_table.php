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
        Schema::table('schedule_files', function (Blueprint $table) {
            $table->foreign(['cutoff_schedule_id'], 'fk_schedule_files_cutoff')->references(['id'])->on('cutoff_schedules')->onDelete('CASCADE');
            $table->foreign(['employee_id'], 'fk_schedule_files_employee')->references(['id'])->on('employees')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_files', function (Blueprint $table) {
            $table->dropForeign('fk_schedule_files_cutoff');
            $table->dropForeign('fk_schedule_files_employee');
        });
    }
};
