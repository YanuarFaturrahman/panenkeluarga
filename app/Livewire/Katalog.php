<?php

namespace App\Livewire;

use App\Models\SesiGroupBuying;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Katalog extends Component
{
    use WithPagination;

    public string $cari = '';
    public string $kategori = '';

    public function updating($property)
    {
        if (in_array($property, ['cari', 'kategori'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        try {
            $sesi = SesiGroupBuying::with(['produk' => function ($q) {
                    $q->with('petani');
                }, 'wilayah'])
                // Cek status sesi (gunakan orWhere jika status di DB bernama 'aktif' atau 'open')
                ->whereIn('status', ['berjalan', 'aktif', 'open'])
                ->whereHas('produk', function ($q) {
                    $q->when($this->cari, fn ($qq) => $qq->where('nama_komoditas', 'like', "%{$this->cari}%"))
                      ->when($this->kategori, fn ($qq) => $qq->where('kategori', $this->kategori));
                })
                ->latest()
                ->paginate(9);
        } catch (\Throwable $e) {
            // Log error untuk mempermudah debugging jika ada issue DB
            \Illuminate\Support\Facades\Log::error('Error Katalog: ' . $e->getMessage());
            $sesi = SesiGroupBuying::paginate(9); // Fallback paginator kosong agar tidak error di $sesi->links()
        }

        return view('livewire.katalog', ['sesi' => $sesi])->title('Katalog Produk — PanenKeluarga');
    }
}