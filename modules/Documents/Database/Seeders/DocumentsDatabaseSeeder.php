<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DocumentsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SeedDocumentPermissionTableSeeder::class);
        $this->call(SeedDocumentCategoryTableSeeder::class);
    }
}
