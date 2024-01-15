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
        Schema::table('employee_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('job')->after('description');
            $table->unsignedDecimal('new_salary', 10, 2)->after('job');
            $table->unsignedDecimal('new_position_allowance', 10, 2)->after('new_salary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_transfers', function (Blueprint $table) {
            $table->dropColumn('job');
            $table->dropColumn('new_salary');
            $table->dropColumn('new_position_allowance');
        });
    }
};
