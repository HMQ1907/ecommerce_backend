<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddEmailInvoiceToContactsTable
 */
class AddEmailInvoiceToContactsTable extends Migration
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
            $table->string('email_invoice')->nullable()->after('email');
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
            $table->dropColumn('email_invoice');
        });
    }
}
