<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('designation_id')->nullable()->after('user_id');
            $table->foreign('designation_id')->references('id')->on('designations');
            $table->unsignedBigInteger('branch_id')->nullable()->after('designation_id');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->renameColumn('code', 'employee_code');
            $table->renameColumn('joining_date', 'date_to_company');
            $table->decimal('allowance', 8, 2)->after('type')->nullable();
            $table->string('indicator')->after('allowance')->nullable();
            $table->date('date_to_job')->after('indicator')->nullable();
            $table->integer('job')->after('date_to_job')->nullable();
            $table->date('date_of_engagement')->after('job')->nullable();
            $table->string('education')->after('date_of_engagement')->nullable();
            $table->integer('jg')->after('education')->nullable();
            $table->integer('actua_working_days')->after('jg')->nullable();
            $table->integer('normal_retirement_date')->after('actua_working_days')->nullable();
            $table->dropColumn('passport');
            $table->date('date_to_job_group')->after('job')->nullable();
            $table->decimal('service', 6, 2)->after('date_to_job_group')->nullable();
        });

        DB::statement("ALTER TABLE employees MODIFY COLUMN type ENUM('staff','contractor','expat','removal') NOT NULL DEFAULT 'staff'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn('designation_id');
            $table->dropColumn('branch_id');
            $table->renameColumn('employee_code', 'code');
            $table->renameColumn('date_to_company', 'joining_date');
            $table->dropColumn('allowance');
            $table->dropColumn('indicator');
            $table->dropColumn('date_to_job');
            $table->dropColumn('job');
            $table->dropColumn('date_of_engagement');
            $table->dropColumn('education');
            $table->dropColumn('jg');
            $table->dropColumn('actua_working_days');
            $table->dropColumn('normal_retirement_date');
            $table->string('passport')->nullable();
            $table->dropColumn('date_to_job_group');
            $table->dropColumn('service');
        });
    }
};
