<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $villageCode = DB::table('indonesia_villages')->value('code') ?? '3204050001';

        // 1. Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@panenkeluarga.id'],
            [
                'name'              => 'Admin PanenKeluarga',
                'password'          => Hash::make('password'),
                'peran'             => 'admin',
                'nomor_hp'          => '081200000000',
                'village_code'      => null,
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        // 2. Akun Petani (Dibutuhkan oleh ProdukSeeder)
        User::updateOrCreate(
            ['email' => 'petani@panenkeluarga.id'],
            [
                'name'              => 'Petani Utama',
                'password'          => Hash::make('password'),
                'peran'             => 'petani',
                'nomor_hp'          => '081200000001',
                'village_code'      => $villageCode,
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        // 3. Akun Koordinator (Dibutuhkan oleh SesiGroupBuyingSeeder)
        User::updateOrCreate(
            ['email' => 'koordinator@panenkeluarga.id'],
            [
                'name'              => 'Koordinator Utama',
                'password'          => Hash::make('password'),
                'peran'             => 'koordinator',
                'nomor_hp'          => '081200000002',
                'village_code'      => $villageCode,
                'status_verifikasi' => 'terverifikasi',
            ]
        );
    }
}