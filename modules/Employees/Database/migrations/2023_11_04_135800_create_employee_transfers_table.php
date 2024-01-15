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
        Schema::create('employee_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('from_branch_id');
            $table->unsignedBigInteger('to_branch_id');
            $table->unsignedBigInteger('from_designation_id');
            $table->unsignedBigInteger('to_designation_id');
            $table->string('description')->nullable();
            $table->date('transfer_date');
            $table->date('notice_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('from_branch_id')->references('id')->on('branches');
            $table->foreign('to_branch_id')->references('id')->on('branches');
            $table->foreign('from_designation_id')->references('id')->on('designations');
            $table->foreign('to_designation_id')->references('id')->on('designations');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_transfers');
    }
};
