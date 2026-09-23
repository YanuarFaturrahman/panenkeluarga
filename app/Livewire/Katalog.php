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
            $user = auth()->user();
            
            // Kode wilayah user (Konsumen kscibogo = "3213172002")
            $kodeWilayahUser = $user->village_code ?? $user->wilayah_id ?? null;

            $sesi = SesiGroupBuying::with(['produk'])
                // 1. Filter Status Sesi
                ->whereIn(\DB::raw('LOWER(status)'), ['berjalan', 'aktif', 'open'])

                // 2. Filter Wilayah Langsung dari Kolom wilayah_id / village_code di Sesi & User
                ->when($kodeWilayahUser, function ($query) use ($kodeWilayahUser) {
                    $query->where(function ($q) use ($kodeWilayahUser) {
                        // Cocokkan langsung dengan wilayah_id pada tabel sesi_group_buying
                        $q->where('wilayah_id', $kodeWilayahUser)
                          ->orWhere('wilayah_id', (int)$kodeWilayahUser)
                          // Jika ada relasi produk
                          ->orWhereHas('produk', function ($qp) use ($kodeWilayahUser) {
                              $qp->whereHas('petani', function ($qpetani) use ($kodeWilayahUser) {
                                  $qpetani->where('village_code', $kodeWilayahUser)
                                          ->orWhere('wilayah_id', $kodeWilayahUser);
                              });
                          });
                    });
                })

                // 3. Filter Pencarian & Kategori Produk
                ->whereHas('produk', function ($q) {
                    $q->when($this->cari, fn ($qq) => $qq->where(function ($sub) {
                        $sub->where('nama_komoditas', 'like', "%{$this->cari}%")
                            ->orWhere('nama', 'like', "%{$this->cari}%");
                    }))
                    ->when($this->kategori, fn ($qq) => $qq->where('kategori', $this->kategori));
                })
                ->latest()
                ->paginate(9);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error Katalog: ' . $e->getMessage());
            $sesi = SesiGroupBuying::whereRaw('1 = 0')->paginate(9);
        }

        return view('livewire.katalog', ['sesi' => $sesi])->title('Katalog Produk — PanenKeluarga');
    }
}