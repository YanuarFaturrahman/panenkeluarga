<?php

namespace App\Livewire\Petani;

use App\Models\PesertaSesi;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Pesanan extends Component
{
    use WithPagination;

    public string $filterStatus = 'semua';

    // Fungsi untuk mengubah status pesanan secara manual oleh petani
    public function updateStatus($id, $statusBaru)
    {
        $pesanan = PesertaSesi::find($id);
        if ($pesanan) {
            $pesanan->update(['status' => $statusBaru]);
            session()->flash('message', 'Status pesanan berhasil diperbarui!');
        }
    }

    public function render()
    {
        $baseQuery = PesertaSesi::with(['sesi.produk', 'konsumen'])
            ->whereHas('sesi.produk', fn ($q) => $q->where('petani_id', auth()->id()));

        $semuaPeserta = $baseQuery->get();
        $totalPesanan = $semuaPeserta->count();

        // Penghitungan statistik
        $menungguKuota = $semuaPeserta->filter(fn($p) => stripos($p->status, 'tunggu') !== false)->count();
        $siapKirim = $semuaPeserta->filter(fn($p) => stripos($p->status, 'siap') !== false)->count();
        $terkirim = $semuaPeserta->filter(fn($p) => stripos($p->status, 'terkirim') !== false || stripos($p->status, 'selesai') !== false)->count();
        
        $totalPendapatan = $semuaPeserta->filter(fn($p) => stripos($p->status, 'terkirim') !== false || stripos($p->status, 'selesai') !== false)
            ->sum(fn ($item) => $item->jumlah_pesanan * ($item->sesi->harga_per_satuan ?? 0));

        // Query untuk tabel dengan filter
        $pesananQuery = PesertaSesi::with(['sesi.produk', 'konsumen'])
            ->whereHas('sesi.produk', fn ($q) => $q->where('petani_id', auth()->id()));

        if ($this->filterStatus !== 'semua') {
            if ($this->filterStatus === 'menunggu_kuota') {
                $pesananQuery->where('status', 'like', '%tunggu%');
            } elseif ($this->filterStatus === 'siap_kirim') {
                $pesananQuery->where('status', 'like', '%siap%');
            } elseif ($this->filterStatus === 'terkirim') {
                $pesananQuery->where(function($q) {
                    $q->where('status', 'like', '%terkirim%')->orWhere('status', 'like', '%selesai%');
                });
            }
        }

        $pesanan = $pesananQuery->latest()->paginate(10);

        return view('livewire.petani.pesanan', compact(
            'pesanan', 'totalPesanan', 'menungguKuota', 'siapKirim', 'terkirim', 'totalPendapatan'
        ))->title('Pesanan Masuk — PanenKeluarga');
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }
}