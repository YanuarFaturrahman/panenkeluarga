<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin PanenKeluarga',
            'email' => 'admin@panenkeluarga.id',
            'password' => Hash::make('password'),
            'peran' => 'admin',
            'nomor_hp' => '081200000000',
            'wilayah_id' => null,
            'status_verifikasi' => 'terverifikasi',
        ]);

        User::create([
            'name' => 'Pak Slamet',
            'email' => 'petani@panenkeluarga.id',
            'password' => Hash::make('password'),
            'peran' => 'petani',
            'nomor_hp' => '081211111111',
            'wilayah_id' => 1,
            'status_verifikasi' => 'terverifikasi',
        ]);

        User::create([
            'name' => 'Bu Wulandari',
            'email' => 'koordinator@panenkeluarga.id',
            'password' => Hash::make('password'),
            'peran' => 'koordinator',
            'nomor_hp' => '081222222222',
            'wilayah_id' => 1,
            'status_verifikasi' => 'terverifikasi',
        ]);

        User::create([
            'name' => 'Rina Nurhaliza',
            'email' => 'konsumen@panenkeluarga.id',
            'password' => Hash::make('password'),
            'peran' => 'konsumen',
            'nomor_hp' => '081233333333',
            'wilayah_id' => 1,
            'status_verifikasi' => 'terverifikasi',
        ]);
    }
}