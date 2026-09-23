<?php

namespace App\Livewire;

use App\Models\SesiGroupBuying;
use App\Models\SubsidiNutrisi;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class Beranda extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Ambil ID wilayah user (fleksibel mendukung kolom wilayah_id maupun relasi village)
        $wilayahId = $user?->wilayah_id ?? $user?->village_id ?? null;

        // Ambil sesi group buying aktif
        try {
            $query = SesiGroupBuying::with(['produk', 'produk.petani', 'wilayah'])
                ->where('status', 'berjalan');

            // Jika user memiliki wilayah_id, filter berdasarkan wilayah user
            if ($wilayahId) {
                $query->where('wilayah_id', $wilayahId);
            }

            $sesiAktif = $query->latest()->take(6)->get();

            // Jika tidak ada sesi di wilayah spesifik user, tampilkan sesi aktif umum/semua wilayah
            if ($sesiAktif->isEmpty()) {
                $sesiAktif = SesiGroupBuying::with(['produk', 'produk.petani', 'wilayah'])
                    ->where('status', 'berjalan')
                    ->latest()
                    ->take(6)
                    ->get();
            }
        } catch (Throwable $e) {
            $sesiAktif = collect();
        }

        // Ambil total alokasi subsidi bulan ini
        try {
            $totalSubsidiBulanIni = SubsidiNutrisi::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('jumlah_dialokasikan') ?? 0;
        } catch (Throwable $e) {
            $totalSubsidiBulanIni = 0;
        }

        return view('livewire.beranda', [
            'sesiAktif'            => $sesiAktif,
            'totalSubsidiBulanIni' => $totalSubsidiBulanIni,
        ])->title('Beranda — PanenKeluarga');
    }
}