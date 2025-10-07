<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        DB::table('departments')->insert([
            ['name' => 'Outbound', 'rate' => 42.69],
            ['name' => 'Inbound', 'rate' => 42.96],
            ['name' => 'Promo', 'rate' => 32.89],
            ['name' => 'Cross-dock', 'rate' => 40.00],
        ]);
    }
}
