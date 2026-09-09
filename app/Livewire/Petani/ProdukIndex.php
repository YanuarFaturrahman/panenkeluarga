<?php

namespace App\Livewire\Petani;

use App\Models\Produk;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProdukIndex extends Component
{
    use WithPagination;

    public function toggleStatus($id)
    {
        $produk = Produk::where('petani_id', auth()->id())->find($id);

        if ($produk) {
            $produk->status = $produk->status === 'aktif' ? 'draft' : 'aktif';
            $produk->save();
            session()->flash('sukses', 'Status produk berhasil diperbarui.');
        }
    }

    public function hapus($id)
    {
        $produk = Produk::where('petani_id', auth()->id())->find($id);

        if ($produk) {
            $produk->delete();
            session()->flash('sukses', 'Produk berhasil dihapus.');
        }
    }

    public function render()
    {
        $produk = Produk::where('petani_id', auth()->id())
            ->latest()
            ->paginate(9);

        return view('livewire.petani.produk-index', [
            'produk' => $produk,
        ])->title('Produk Saya — PanenKeluarga');
    }
}