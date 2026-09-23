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

    // Filter status tab: 'semua', 'berjalan', 'selesai'
    public $filterStatus = 'semua';

    public function filterByStatus($status)
    {
        $this->filterStatus = $status;
        $this->resetPage();
    }

    public function kelola($id)
    {
        return redirect()->to('/koordinator/sesi/' . $id);
    }

    public function render()
    {
        $query = SesiGroupBuying::with(['produk.petani'])
            ->withCount(['peserta']) // Menghitung total data peserta/transaksi secara aman
            ->where('koordinator_id', auth()->id());

        if ($this->filterStatus !== 'semua') {
            $query->where('status', $this->filterStatus);
        }

        $sesi = $query->latest()->paginate(10);

        return view('livewire.koordinator.sesi-index', [
            'sesi' => $sesi
        ])->layoutData([
            'title' => 'Sesi Group Buying — PanenKeluarga',
            'header' => 'Sesi Group Buying',
            'subheader' => 'Kelola sesi pembelian bersama di wilayah Anda'
        ]);
    }
}