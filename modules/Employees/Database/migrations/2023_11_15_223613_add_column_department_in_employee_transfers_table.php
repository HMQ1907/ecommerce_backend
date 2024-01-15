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
            $table->unsignedBigInteger('from_department_id')->nullable()->after('from_branch_id');
            $table->unsignedBigInteger('to_department_id')->nullable()->after('to_branch_id');
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
            $table->dropColumn('from_department_id');
            $table->dropColumn('to_department_id');
        });
    }
};
