<?php

namespace App\Livewire\Admin;

use App\Models\Transaksi as TransaksiModel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Transaksi — PanenKeluarga')]
class Transaksi extends Component
{
    use WithPagination;

    public function render()
    {
        $transaksi = TransaksiModel::with([
            'pesertaSesi.sesi.produk', 
            'pesertaSesi.konsumen'
        ])->latest()->paginate(15);

        return view('livewire.admin.transaksi', compact('transaksi'));
    }
}