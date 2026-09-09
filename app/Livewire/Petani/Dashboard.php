<?php

namespace App\Livewire\Petani;

use App\Models\PesertaSesi;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\Ulasan;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $petaniId = auth()->id();

        // 1. Total Produk Aktif (dengan info minggu ini)
        try {
            $produkAktif = Produk::where('petani_id', $petaniId)
                ->where('status', 'aktif')
                ->count();
            
            $produkAktifMingguIni = Produk::where('petani_id', $petaniId)
                ->where('status', 'aktif')
                ->where('created_at', '>=', now()->subDays(7))
                ->count();
        } catch (Throwable $e) {
            $produkAktif = 0;
            $produkAktifMingguIni = 0;
        }

        // 2. Pesanan Masuk (menunggu kuota)
        try {
            $pesananMasuk = PesertaSesi::whereHas('sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
                ->whereIn('status', ['menunggu', 'dikonfirmasi'])
                ->count();
            
            $pesananMenungguKuota = PesertaSesi::whereHas('sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
                ->whereHas('sesi', fn ($q) => $q->where('status', 'berjalan'))
                ->where('status', 'menunggu')
                ->count();
        } catch (Throwable $e) {
            $pesananMasuk = 0;
            $pesananMenungguKuota = 0;
        }

        // 3. Pendapatan Bulan Ini (dengan perbandingan bulan lalu)
        try {
            $pendapatanBulanIni = Transaksi::whereHas('pesertaSesi.sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
                ->where('status_pembayaran', 'lunas')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('jumlah_bayar') ?? 0;
            
            $pendapatanBulanLalu = Transaksi::whereHas('pesertaSesi.sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
                ->where('status_pembayaran', 'lunas')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('jumlah_bayar') ?? 0;
            
            $persentaseKenaikan = $pendapatanBulanLalu > 0 
                ? (int)round((($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100)
                : 0;
        } catch (Throwable $e) {
            $pendapatanBulanIni = 0;
            $pendapatanBulanLalu = 0;
            $persentaseKenaikan = 0;
        }

        // 4. Rata-rata Rating
        try {
            $rataRating = Ulasan::whereHas('produk', fn ($q) => $q->where('petani_id', $petaniId))
                ->avg('rating') ?? 0;
            
            $rataRating = round($rataRating, 1);
            
            $jumlahUlasan = Ulasan::whereHas('produk', fn ($q) => $q->where('petani_id', $petaniId))
                ->count();
        } catch (Throwable $e) {
            $rataRating = 0;
            $jumlahUlasan = 0;
        }

        // 5. Pesanan Terbaru dengan detail sesi
        try {
            $pesananTerbaru = PesertaSesi::with(['sesi.produk', 'konsumen'])
                ->whereHas('sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
                ->latest()
                ->take(5)
                ->get();
        } catch (Throwable $e) {
            $pesananTerbaru = collect();
        }

        // 6. Pendapatan 7 hari terakhir untuk chart
        try {
            $pendapatan7Hari = [];
            for ($i = 6; $i >= 0; $i--) {
                $tanggal = now()->subDays($i);
                $totalHari = Transaksi::whereHas('pesertaSesi.sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
                    ->where('status_pembayaran', 'lunas')
                    ->whereDate('created_at', $tanggal->toDateString())
                    ->sum('jumlah_bayar') ?? 0;
                
                $pendapatan7Hari[$tanggal->format('D')] = $totalHari;
            }
        } catch (Throwable $e) {
            $pendapatan7Hari = [];
        }

        return view('livewire.petani.dashboard', compact(
            'produkAktif',
            'produkAktifMingguIni',
            'pesananMasuk',
            'pesananMenungguKuota',
            'pendapatanBulanIni',
            'persentaseKenaikan',
            'rataRating',
            'jumlahUlasan',
            'pesananTerbaru',
            'pendapatan7Hari'
        ))->title('Dashboard Petani — PanenKeluarga');
    }
}