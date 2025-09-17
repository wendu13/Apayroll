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
        Schema::dropIfExists('deduction_rules');
        Schema::dropIfExists('type');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // optional: recreate if needed
        Schema::create('deduction_rules', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    
        Schema::create('type', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
