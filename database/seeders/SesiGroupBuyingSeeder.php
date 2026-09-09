<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\SesiGroupBuying;
use App\Models\TitikPengambilan;
use App\Models\User;
use Illuminate\Database\Seeder;

class SesiGroupBuyingSeeder extends Seeder
{
    public function run(): void
    {
        $koordinator = User::where('peran', 'koordinator')->first();

        $titik = TitikPengambilan::create([
            'wilayah_id' => $koordinator->wilayah_id,
            'koordinator_id' => $koordinator->id,
            'nama_lokasi' => 'Pos Ronda RT 05',
            'alamat' => 'Depan Pos Ronda, RT 05/RW 03, Kel. Cigadung',
            'jam_operasional' => '16.00 - 19.00 WIB',
        ]);

        foreach (Produk::all() as $produk) {
            SesiGroupBuying::create([
                'produk_id' => $produk->id,
                'koordinator_id' => $koordinator->id,
                'wilayah_id' => $koordinator->wilayah_id,
                'titik_pengambilan_id' => $titik->id,
                'kuota_minimum' => 10,
                'jumlah_terkumpul' => rand(2, 9),
                'harga_satuan' => $produk->harga,
                'tenggat_waktu' => now()->addDays(2),
                'status' => 'berjalan',
            ]);
        }
    }
}