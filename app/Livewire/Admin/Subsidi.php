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
    public $jumlahAnakInput = 1;

    public function lihatDetail(int $id): void
    {
        $this->selectedSubsidi = SubsidiNutrisi::with(['wilayah', 'transaksi.user'])->find($id);
        
        if ($this->selectedSubsidi) {
            $this->jumlahAnakInput = $this->selectedSubsidi->jumlah_anak_penerima ?? 1;
            $this->showDetailModal = true;
        }
    }

    public function salurkan(): void
    {
        if (!$this->selectedSubsidi) {
            return;
        }

        $this->validate([
            'jumlahAnakInput' => 'required|integer|min:1',
        ], [
            'jumlahAnakInput.required' => 'Jumlah anak penerima wajib diisi.',
            'jumlahAnakInput.integer'  => 'Jumlah anak harus berupa angka.',
            'jumlahAnakInput.min'      => 'Minimal 1 anak penerima.',
        ]);

        $this->selectedSubsidi->update([
            'jumlah_disalurkan'    => $this->selectedSubsidi->jumlah_dialokasikan,
            'jumlah_anak_penerima' => $this->jumlahAnakInput,
            'status'               => 'disalurkan',
        ]);

        $this->closeModal();
        session()->flash('sukses', 'Subsidi berhasil disalurkan.');
    }

    public function updateJumlahAnak(): void
    {
        if (!$this->selectedSubsidi) {
            return;
        }

        $this->validate([
            'jumlahAnakInput' => 'required|integer|min:1',
        ], [
            'jumlahAnakInput.required' => 'Jumlah anak penerima wajib diisi.',
            'jumlahAnakInput.integer'  => 'Jumlah anak harus berupa angka.',
            'jumlahAnakInput.min'      => 'Minimal 1 anak penerima.',
        ]);

        $this->selectedSubsidi->update([
            'jumlah_anak_penerima' => $this->jumlahAnakInput,
        ]);

        $this->closeModal();
        session()->flash('sukses', 'Jumlah anak penerima manfaat berhasil diperbarui.');
    }

    public function closeModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedSubsidi = null;
        $this->jumlahAnakInput = 1;
        $this->resetValidation();
    }

    public function buatLaporanPublik()
    {
        return redirect()->route('subsidi.transparansi');
    }

    public function render()
    {
        // Panggil relasi wilayah & transaksi
        $pengajuan = SubsidiNutrisi::with(['wilayah', 'transaksi.user'])->latest()->paginate(10);

        $surplusTerkumpul   = SubsidiNutrisi::sum('jumlah_dialokasikan');
        $sudahDisalurkan    = SubsidiNutrisi::sum('jumlah_disalurkan');
        $penerimaManfaat    = SubsidiNutrisi::sum('jumlah_anak_penerima');
        $totalDesa          = SubsidiNutrisi::distinct('wilayah_id')->whereNotNull('wilayah_id')->count('wilayah_id');
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
            'totalDesa',
            'menungguPenyaluran',
            'totalPengajuan',
            'penyaluranWilayah',
            'maxAnak'
        ));
    }
}