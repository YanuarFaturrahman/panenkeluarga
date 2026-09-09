<?php

namespace App\Livewire\Admin;

use App\Models\SubsidiNutrisi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Subsidi Nutrisi — PanenKeluarga')]
class Subsidi extends Component
{
    use WithPagination;

    public $selectedSubsidi = null;
    public $showDetailModal = false;

    public function salurkan(int $id, int $jumlahAnak): void
    {
        $subsidi = SubsidiNutrisi::findOrFail($id);
        $subsidi->update([
            'jumlah_disalurkan' => $subsidi->jumlah_dialokasikan,
            'jumlah_anak_penerima' => $jumlahAnak,
            'status' => 'disalurkan',
        ]);
        
        $this->showDetailModal = false;
        session()->flash('sukses', 'Subsidi berhasil disalurkan.');
    }

    public function lihatDetail(int $id): void
    {
        $this->selectedSubsidi = SubsidiNutrisi::with('wilayah')->find($id);
        $this->showDetailModal = true;
    }

    public function closeModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedSubsidi = null;
    }

    public function buatLaporanPublik()
    {
        // Pengalihan ke halaman transparansi / laporan publik
        return redirect()->route('subsidi.transparansi');
    }

    public function render()
    {
        $pengajuan = SubsidiNutrisi::with('wilayah')->latest()->paginate(10);

        $surplusTerkumpul   = SubsidiNutrisi::sum('jumlah_dialokasikan');
        $sudahDisalurkan    = SubsidiNutrisi::sum('jumlah_disalurkan');
        $penerimaManfaat    = SubsidiNutrisi::sum('jumlah_anak_penerima');
        $totalRW            = SubsidiNutrisi::distinct('wilayah_id')->count('wilayah_id');
        $menungguPenyaluran = SubsidiNutrisi::whereIn('status', ['diajukan', 'terkumpul'])->sum('jumlah_dialokasikan');
        $totalPengajuan     = SubsidiNutrisi::whereIn('status', ['diajukan', 'terkumpul'])->count();

        $penyaluranWilayah  = SubsidiNutrisi::with('wilayah')
            ->selectRaw('wilayah_id, SUM(jumlah_anak_penerima) as total_anak')
            ->groupBy('wilayah_id')
            ->get();

        $maxAnak = $penyaluranWilayah->max('total_anak') ?: 1;

        return view('livewire.admin.subsidi', compact(
            'pengajuan',
            'surplusTerkumpul',
            'sudahDisalurkan',
            'penerimaManfaat',
            'totalRW',
            'menungguPenyaluran',
            'totalPengajuan',
            'penyaluranWilayah',
            'maxAnak'
        ));
    }
}