<?php

namespace App\Livewire\Koordinator;

use App\Models\SesiGroupBuying;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class SesiIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $sesi = SesiGroupBuying::with(['produk.petani'])
            ->where('koordinator_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('livewire.koordinator.sesi-index', [
            'sesi' => $sesi
        ])->layoutData([
            'title' => 'Sesi Group Buying — PanenKeluarga',
            'header' => 'Sesi Group Buying',
            'subheader' => 'Kelola sesi pembelian bersama di wilayah Anda'
        ]);
    }
}