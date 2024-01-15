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
        Schema::table('attendances', function (Blueprint $table) {
            $table->after('clock_out', function (Blueprint $table) {
                $table->float('clock_in_latitude', 12, 6)->nullable();
                $table->float('clock_in_longitude', 12, 6)->nullable();
                $table->float('clock_out_latitude', 12, 6)->nullable();
                $table->float('clock_out_longitude', 12, 6)->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('clock_in_latitude', 'clock_in_longitude', 'clock_out_latitude', 'clock_out_longitude');
        });
    }
};
