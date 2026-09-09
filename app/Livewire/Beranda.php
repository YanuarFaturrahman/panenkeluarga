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

        // Ambil sesi group buying aktif di wilayah user secara aman
        try {
            $sesiAktif = SesiGroupBuying::with('produk', 'produk.petani')
                ->where('wilayah_id', $user?->wilayah_id)
                ->where('status', 'berjalan')
                ->latest()
                ->take(3)
                ->get();
        } catch (Throwable $e) {
            $sesiAktif = collect();
        }

        // Ambil total subsidi bulan ini secara aman
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