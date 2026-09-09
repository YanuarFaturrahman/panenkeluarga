<?php

namespace App\Livewire\Koordinator;

use App\Models\Produk;
use App\Models\SesiGroupBuying;
use App\Models\TitikPengambilan;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BukaSesi extends Component
{
    public ?int $produk_id = null;
    public ?int $titik_pengambilan_id = null;
    public ?int $kuota_minimum = 10;
    public ?int $harga_satuan = null;
    public string $tenggat_waktu = '';

    public function updatedProdukId($value): void
    {
        $produk = Produk::find($value);
        $this->harga_satuan = $produk?->harga;
    }

    #[Computed]
    public function produkList()
    {
        return Produk::where('status', 'aktif')->get();
    }

    #[Computed]
    public function titikList()
    {
        return TitikPengambilan::where('koordinator_id', auth()->id())->get();
    }

    public function simpan()
    {
        $validated = $this->validate([
            'produk_id' => ['required', 'exists:produk,id'],
            'titik_pengambilan_id' => ['required', 'exists:titik_pengambilan,id'],
            'kuota_minimum' => ['required', 'integer', 'min:1'],
            'harga_satuan' => ['required', 'integer', 'min:0'],
            'tenggat_waktu' => ['required', 'date', 'after:now'],
        ]);

        SesiGroupBuying::create([
            'koordinator_id' => auth()->id(),
            'wilayah_id' => auth()->user()->wilayah_id,
            'produk_id' => $validated['produk_id'],
            'titik_pengambilan_id' => $validated['titik_pengambilan_id'],
            'kuota_minimum' => $validated['kuota_minimum'],
            'jumlah_terkumpul' => 0,
            'harga_satuan' => $validated['harga_satuan'],
            'tenggat_waktu' => $validated['tenggat_waktu'],
            'status' => 'berjalan',
        ]);

        session()->flash('sukses', 'Sesi group buying berhasil dibuka.');
        return $this->redirect(route('koordinator.sesi.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.koordinator.buka-sesi', [
            'sesi' => SesiGroupBuying::where('koordinator_id', auth()->id())
                        ->with(['produk.petani', 'titikPengambilan'])
                        ->latest()
                        ->get(),
        ])->title('Buka Sesi Baru — PanenKeluarga');
    }
}