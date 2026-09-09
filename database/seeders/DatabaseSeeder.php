<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            WilayahSeeder::class,
            UserSeeder::class,
            ProdukSeeder::class,
            SesiGroupBuyingSeeder::class,
        ]);
    }
}