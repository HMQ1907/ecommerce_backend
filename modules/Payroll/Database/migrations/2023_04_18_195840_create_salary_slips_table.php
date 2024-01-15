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
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->dateTime('salary_from')->nullable();
            $table->dateTime('salary_to')->nullable();
            $table->date('paid_on')->nullable();
            $table->enum('status', ['generated', 'review', 'locked', 'paid'])->default('generated');
            $table->text('salary_json')->nullable();
            $table->text('extra_json')->nullable();
            $table->decimal('net_salary', 16, 0)->default(0);
            $table->decimal('gross_salary', 16, 0)->default(0);
            $table->decimal('total', 16, 0)->default(0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('salary_slips');
    }
};
