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
        Schema::create('employee_variable_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_id')->references('id')->on('employee_salaries')->onDelete('cascade');
            $table->foreignId('variable_component_id')->references('id')->on('salary_components')->onDelete('cascade');
            $table->decimal('variable_value', 16, 0)->nullable();
            $table->decimal('current_value', 16, 0);
            $table->enum('adjustment_type', ['initial', 'increment', 'decrement'])->nullable();
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
        Schema::dropIfExists('employee_variable_salaries');
    }
};
