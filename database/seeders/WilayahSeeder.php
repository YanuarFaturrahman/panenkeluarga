<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // Data dikosongkan agar wilayah diisi dari pengajuan Koordinator
        $data = [];

        foreach ($data as $item) {
            Wilayah::create($item);
        }
    }
}