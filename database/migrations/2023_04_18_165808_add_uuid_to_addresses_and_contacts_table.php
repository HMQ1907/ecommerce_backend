<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class AddUuidToAddressesAndContactsTable
 */
class AddUuidToAddressesAndContactsTable extends Migration
{
    /**
     * Table names.
     *
     * @var string   The addresses table.
     * @var string    The contacts table.
     */
    protected $table_addresses;

    protected $table_contacts;

    /**
     * Create a new migration instance.
     */
    public function __construct()
    {
        $this->table_addresses = config('lecturize.addresses.table', 'addresses');
        $this->table_contacts = config('lecturize.contacts.table', 'contacts');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_addresses, function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });

        Schema::table($this->table_contacts, function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table_addresses, function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table($this->table_contacts, function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
