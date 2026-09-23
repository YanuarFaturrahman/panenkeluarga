<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\DistrictsSeeder;
use Laravolt\Indonesia\Seeds\VillagesSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Eksekusi Seeder Wilayah Bawaan Laravolt Terlebih Dahulu
        $this->call([
            ProvincesSeeder::class,
            CitiesSeeder::class,
            DistrictsSeeder::class,
            VillagesSeeder::class,
        ]);

        // 2. Eksekusi Wilayah Kustom (jika ada) dan Data Aplikasi PanenKeluarga
        $this->call([
            WilayahSeeder::class,
            UserSeeder::class,
            ProdukSeeder::class,
            SesiGroupBuyingSeeder::class,
        ]);
    }
}