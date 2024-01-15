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
        Schema::table('awards', function (Blueprint $table) {
            $table->decimal('award_type', 10)->change();
            DB::statement("ALTER TABLE awards MODIFY COLUMN type ENUM('bonus','allowance','birthday') NOT NULL DEFAULT 'bonus'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('awards', function (Blueprint $table) {
            $table->decimal('award_type', 10, 2)->change();
        });
    }
};
