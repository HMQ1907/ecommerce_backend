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
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['earning', 'deduction']);
            $table->decimal('value', 16, 3)->default(0);
            $table->enum('value_type', ['fixed', 'percent', 'basic_percent', 'variable']);
            $table->boolean('is_company')->default(0);
            $table->tinyInteger('weight_formulate')->nullable();
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
        Schema::dropIfExists('salary_components');
    }
};
