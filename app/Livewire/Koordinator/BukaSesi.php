<?php

namespace App\Livewire\Koordinator;

use App\Models\Produk;
use App\Models\SesiGroupBuying;
use App\Models\TitikPengambilan;
use Illuminate\Support\Facades\Route;
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
        $this->harga_satuan = $produk?->harga ?? $produk?->harga_satuan;
    }

    #[Computed]
    public function produkList()
    {
        $user = auth()->user();

        // Filter produk berdasarkan wilayah/desa milik Koordinator
        return Produk::where('status', 'aktif')
            ->whereHas('petani', function ($query) use ($user) {
                if ($user->village_code) {
                    $query->where('village_code', $user->village_code);
                } elseif ($user->wilayah_id) {
                    $query->where('wilayah_id', $user->wilayah_id);
                }
            })
            ->get();
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
            'tenggat_waktu' => ['required', 'date', 'after:now'],
        ]);

        // Ambil produk untuk memastikan harga asli dari master data petani
        $produk = Produk::findOrFail($validated['produk_id']);
        $hargaAsli = $produk->harga ?? $produk->harga_satuan;

        SesiGroupBuying::create([
            'koordinator_id' => auth()->id(),
            'wilayah_id' => auth()->user()->wilayah_id ?? auth()->user()->village_code,
            'produk_id' => $validated['produk_id'],
            'titik_pengambilan_id' => $validated['titik_pengambilan_id'],
            'kuota_minimum' => $validated['kuota_minimum'],
            'jumlah_terkumpul' => 0,
            'harga_satuan' => $hargaAsli, // Gunakan harga asli dari produk
            'tenggat_waktu' => $validated['tenggat_waktu'],
            'status' => 'berjalan',
        ]);

        session()->flash('sukses', 'Sesi group buying berhasil dibuka.');
        
        if (Route::has('koordinator.sesi.index')) {
            return $this->redirect(route('koordinator.sesi.index'), navigate: true);
        }

        return $this->redirect('/koordinator/sesi', navigate: true);
    }

    public function render()
    {
        return view('livewire.koordinator.buka-sesi')->layoutData([
            'title' => 'Buka Sesi Baru — PanenKeluarga',
            'header' => 'Buka Sesi Group Buying',
            'subheader' => 'Formulir pembuatan sesi pembelian bersama'
        ]);
    }
}