<?php
namespace App\Livewire\Petani;

use App\Models\Transaksi;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Pendapatan extends Component
{
    use WithPagination;

    public function cairkanSekarang()
    {
        session()->flash('message', 'Permintaan pencairan dana berhasil diajukan.');
    }

    public function render()
    {
        $petaniId = auth()->id();

        $baseQuery = Transaksi::with(['pesertaSesi.sesi.produk', 'pesertaSesi.konsumen'])
            ->whereHas('pesertaSesi.sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
            ->where('status_pembayaran', 'lunas');

        // Total Pendapatan
        $totalPendapatan = (clone $baseQuery)->sum('jumlah_bayar');

        // Pendapatan Bulan Ini
        $pendapatanBulanIni = (clone $baseQuery)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah_bayar');

        // Cek apakah kolom status_pencairan ada di tabel transaksi
        $hasStatusPencairan = Schema::hasColumn('transaksi', 'status_pencairan');

        if ($hasStatusPencairan) {
            $menungguPencairan = (clone $baseQuery)
                ->where('status_pencairan', 'menunggu')
                ->sum('jumlah_bayar');

            $transaksiMenungguCount = (clone $baseQuery)
                ->where('status_pencairan', 'menunggu')
                ->count();

            $sudahDicairkan = (clone $baseQuery)
                ->where('status_pencairan', 'dicairkan')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('jumlah_bayar');
        } else {
            // Fallback default jika belum membuat migration kolom status_pencairan
            $menungguPencairan = 0;
            $transaksiMenungguCount = 0;
            $sudahDicairkan = $pendapatanBulanIni;
        }

        $transaksi = (clone $baseQuery)->latest()->paginate(10);

        return view('livewire.petani.pendapatan', compact(
            'transaksi',
            'totalPendapatan',
            'pendapatanBulanIni',
            'menungguPencairan',
            'transaksiMenungguCount',
            'sudahDicairkan'
        ))->title('Pendapatan — PanenKeluarga');
    }
}