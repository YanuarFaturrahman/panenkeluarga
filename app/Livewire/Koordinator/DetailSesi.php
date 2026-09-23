<?php

namespace App\Livewire\Koordinator;

use App\Models\PesertaSesi;
use App\Models\SesiGroupBuying;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DetailSesi extends Component
{
    public $sesi;
    public $sesiId;

    public function mount($sesi)
    {
        $this->sesiId = $sesi instanceof SesiGroupBuying ? $sesi->id : $sesi;
        $this->loadData();
    }

    public function loadData()
    {
        $this->sesi = SesiGroupBuying::with([
            'produk.petani',
            'peserta.konsumen',
            'titikPengambilan',
            'wilayah'
        ])->find($this->sesiId);
    }

    // Method untuk mengubah status pesanan warga secara langsung oleh Koordinator
    public function updateStatusPeserta($pesertaId, $statusBaru)
    {
        $peserta = PesertaSesi::find($pesertaId);
        if ($peserta) {
            $peserta->update(['status' => $statusBaru]);
            session()->flash('success', 'Status pesanan warga berhasil diperbarui.');
            $this->loadData();
        }
    }

    // Method untuk menutup atau menyelesaikan sesi group buying
    public function selesaikanSesi()
    {
        if ($this->sesi) {
            $this->sesi->update(['status' => 'selesai']);
            session()->flash('success', 'Sesi group buying telah diselesaikan.');
            $this->loadData();
        }
    }

    public function render()
    {
        return view('livewire.koordinator.detail-sesi', [
            'sesi' => $this->sesi
        ])->layoutData([
            'title' => 'Kelola Sesi — PanenKeluarga',
            'header' => 'Kelola Sesi Group Buying',
            'subheader' => 'Detail pemesanan dan manajemen kuota warga'
        ]);
    }
}