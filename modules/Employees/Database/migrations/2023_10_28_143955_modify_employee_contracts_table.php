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
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->enum('type', ['full_time', 'part_time'])->default('full_time')->after('employee_id');
            $table->string('number')->after('type')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('number');
        });
    }
};
