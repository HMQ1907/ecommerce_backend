<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Documents\Models\DocumentCategory;

class SeedDocumentCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $categories = [
            'Report',
            'Decision',
            'Documentary',
            'Application',
            'Appointment',
            'Contract – Agreement',
            'Letter',
            'Price quotation',
            'Other',
        ];

        foreach ($categories as $branch) {
            DocumentCategory::query()->firstOrCreate([
                'name' => $branch,
                'created_by' => 1,
            ]);
        }
    }
}
