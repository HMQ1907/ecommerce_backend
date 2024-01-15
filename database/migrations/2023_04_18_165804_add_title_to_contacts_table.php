<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddTitleToContactsTable
 */
class AddTitleToContactsTable extends Migration
{
    /**
     * Table names.
     *
     * @var string   The main table name for this migration.
     */
    protected $table;

    /**
     * Create a new migration instance.
     */
    public function __construct()
    {
        $this->table = config('lecturize.contacts.table', 'contacts');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->string('title', 20)->nullable()->before('first_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
}
