<?php

namespace Modules\Branches\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Branches\Models\Branch;

class BranchesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $branches = [
            'PVOIL TRADING CO.',
            'PVOIL LAO',
        ];

        foreach ($branches as $branch) {
            Branch::query()->firstOrCreate([
                'name' => $branch,
            ]);
        }
    }
}
