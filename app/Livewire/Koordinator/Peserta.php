<?php

namespace App\Livewire\Koordinator;

use App\Models\PesertaSesi;
use App\Notifications\StatusPesananUpdated;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Peserta extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $status = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function ubahStatus($id, $statusBaru)
    {
        // Load relasi konsumen, sesi, produk, dan petani
        $peserta = PesertaSesi::with(['konsumen', 'sesi.produk.petani'])
            ->whereHas('sesi', function ($q) {
                $q->where('koordinator_id', auth()->id());
            })->findOrFail($id);

        $peserta->update([
            'status' => $statusBaru,
        ]);

        $namaKomoditas = $peserta->sesi->produk->nama_komoditas ?? 'Komoditas';
        $namaKonsumen = $peserta->konsumen->name ?? 'Warga';

        // 1. PETANI: Notifikasi saat status "dikonfirmasi"
        if ($statusBaru === 'dikonfirmasi') {
            $petani = $peserta->sesi->produk->petani ?? null;
            if ($petani) {
                $pesanPetani = "Pesanan {$namaKomoditas} dari {$namaKonsumen} ({$peserta->jumlah_pesanan} unit) telah dikonfirmasi. Silakan siapkan produk/hasil panen.";
                $petani->notify(new StatusPesananUpdated($peserta, $pesanPetani));
            }
        }

        // 2. KONSUMEN: Notifikasi saat status "siap_diambil"
        if ($statusBaru === 'siap_diambil') {
            $konsumen = $peserta->konsumen;
            if ($konsumen) {
                $pesanKonsumen = "Pesanan {$namaKomoditas} Anda telah Tiba di Titik Pengambilan! Silakan lakukan pengambilan.";
                $konsumen->notify(new StatusPesananUpdated($peserta, $pesanKonsumen));
            }
        }

        // 3. KONSUMEN: Notifikasi saat status "selesai"
        if ($statusBaru === 'selesai') {
            $konsumen = $peserta->konsumen;
            if ($konsumen) {
                $pesanKonsumen = "Pesanan {$namaKomoditas} Anda telah diserahkan dan transaksi dinyatakan selesai. Terima kasih!";
                $konsumen->notify(new StatusPesananUpdated($peserta, $pesanKonsumen));
            }
        }

        session()->flash('sukses', 'Status pesanan berhasil diperbarui & notifikasi terkirim.');
    }

    public function render()
    {
        $peserta = PesertaSesi::with(['konsumen', 'sesi.produk'])
            ->whereHas('sesi', fn ($q) => $q->where('koordinator_id', auth()->id()))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('konsumen', fn ($k) => $k->where('name', 'like', '%'.$this->search.'%'))
                      ->orWhereHas('sesi.produk', fn ($p) => $p->where('nama_komoditas', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.koordinator.peserta', compact('peserta'))->title('Peserta — PanenKeluarga');
    }
}