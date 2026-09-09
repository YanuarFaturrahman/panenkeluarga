<?php

namespace App\Livewire\Subsidi;

use App\Models\SubsidiNutrisi;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Transparansi extends Component
{
    public function render()
    {
        $riwayat = SubsidiNutrisi::with('wilayah')->latest()->paginate(10);
        $totalTerkumpul = SubsidiNutrisi::sum('jumlah_dialokasikan');
        $totalAnakPenerima = SubsidiNutrisi::sum('jumlah_anak_penerima');

        return view('livewire.subsidi.transparansi', compact('riwayat', 'totalTerkumpul', 'totalAnakPenerima'))
            ->title('Transparansi Subsidi Nutrisi — PanenKeluarga');
    }
}