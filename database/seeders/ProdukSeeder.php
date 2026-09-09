<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $petani = User::where('peran', 'petani')->first();

        $produk = [
            ['nama_komoditas' => 'Bayam Segar Organik', 'kategori' => 'sayur', 'satuan' => 'ikat', 'harga' => 6000, 'estimasi_stok' => 120],
            ['nama_komoditas' => 'Telur Ayam Kampung', 'kategori' => 'protein', 'satuan' => 'butir', 'harga' => 3500, 'estimasi_stok' => 300],
            ['nama_komoditas' => 'Tomat Merah Segar', 'kategori' => 'buah', 'satuan' => 'kg', 'harga' => 7000, 'estimasi_stok' => 80],
        ];

        foreach ($produk as $item) {
            Produk::create($item + [
                'petani_id' => $petani->id,
                'estimasi_tanggal_panen' => now()->addDays(3),
                'deskripsi' => 'Hasil panen segar langsung dari kebun petani mitra PanenKeluarga.',
                'status' => 'aktif',
            ]);
        }
    }
}