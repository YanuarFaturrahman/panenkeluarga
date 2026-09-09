<?php

namespace App\Livewire;

use App\Models\PesertaSesi;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class GroupBuyingSaya extends Component
{
    public function render()
    {
        $riwayat = PesertaSesi::with('sesi.produk', 'sesi.wilayah', 'transaksi')
            ->where('konsumen_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('livewire.group-buying-saya', ['riwayat' => $riwayat])->title('Group Buying Saya — PanenKeluarga');
    }
}