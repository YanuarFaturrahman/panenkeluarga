<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\PengajuanWilayah; // Sesuaikan dengan nama model pengajuan kamu
use App\Notifications\PengajuanWilayahDisetujuiNotification;

class VerifikasiWilayah extends Component
{
    public function setujui($id)
    {
        $pengajuan = PengajuanWilayah::findOrFail($id);
        $pengajuan->update(['status' => 'disetujui']);

        // Ambil user koordinator yang membuat pengajuan
        $koordinator = User::find($pengajuan->user_id);

        if ($koordinator) {
            // Kirim notifikasi ke koordinator
            $koordinator->notify(new PengajuanWilayahDisetujuiNotification($pengajuan->nama_wilayah));
        }

        session()->flash('message', 'Pengajuan wilayah berhasil disetujui.');
    }

    public function render()
    {
        return view('livewire.admin.verifikasi-wilayah', [
            'pengajuanList' => PengajuanWilayah::where('status', 'pending')->get()
        ]);
    }
}