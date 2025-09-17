<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cutoff_schedules', function (Blueprint $table) {
            // Add start/end fields for 1st and 2nd half
            if (!Schema::hasColumn('cutoff_schedules', 'first_half_start')) {
                $table->integer('first_half_start')->nullable()->after('year');
            }
            if (!Schema::hasColumn('cutoff_schedules', 'first_half_end')) {
                $table->string('first_half_end')->nullable()->after('first_half_start');
            }
            if (!Schema::hasColumn('cutoff_schedules', 'second_half_start')) {
                $table->integer('second_half_start')->nullable()->after('first_half_end');
            }
            if (!Schema::hasColumn('cutoff_schedules', 'second_half_end')) {
                $table->string('second_half_end')->nullable()->after('second_half_start');
            }

            // Add regular and night shift times
            if (!Schema::hasColumn('cutoff_schedules', 'regular_start')) {
                $table->time('regular_start')->nullable()->after('second_half_end');
            }
            if (!Schema::hasColumn('cutoff_schedules', 'regular_end')) {
                $table->time('regular_end')->nullable()->after('regular_start');
            }
            if (!Schema::hasColumn('cutoff_schedules', 'night_start')) {
                $table->time('night_start')->nullable()->after('regular_end');
            }
            if (!Schema::hasColumn('cutoff_schedules', 'night_end')) {
                $table->time('night_end')->nullable()->after('night_start');
            }
        });
    }

    public function down()
    {
        Schema::table('cutoff_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'first_half_start', 'first_half_end',
                'second_half_start', 'second_half_end',
                'regular_start', 'regular_end',
                'night_start', 'night_end'
            ]);
        });
    }
};
