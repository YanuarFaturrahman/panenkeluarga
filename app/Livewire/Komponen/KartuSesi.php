<?php

namespace App\Livewire\Komponen;

use App\Models\SesiGroupBuying;
use Livewire\Component;

class KartuSesi extends Component
{
    public SesiGroupBuying $sesi;

    public function render()
    {
        return view('livewire.komponen.kartu-sesi');
    }
}