<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('service');
            $table->dropColumn('normal_retirement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('normal_retirement_date')->after('actua_working_days')->nullable();
            $table->decimal('service', 6, 2)->after('date_to_job_group')->nullable();
        });
    }
};
