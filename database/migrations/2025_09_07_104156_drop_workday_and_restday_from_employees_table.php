<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['workday', 'restday']);
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add them back if you rollback
            $table->string('workday')->nullable();
            $table->string('restday')->nullable();
        });
    }
};
