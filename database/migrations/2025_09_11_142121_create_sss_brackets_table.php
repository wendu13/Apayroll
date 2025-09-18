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
    public function up(): void
    {
        Schema::create('sss_brackets', function (Blueprint $table) {
            $table->id();
            $table->decimal('from', 12, 2);   // salary from
            $table->decimal('to', 12, 2);     // salary to
            $table->decimal('er', 12, 2);     // employer contribution
            $table->decimal('ee', 12, 2);     // employee contribution
            $table->decimal('total', 12, 2);  // ER + EE
            $table->string('others')->nullable();
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
        Schema::dropIfExists('sss_brackets');
    }
};
