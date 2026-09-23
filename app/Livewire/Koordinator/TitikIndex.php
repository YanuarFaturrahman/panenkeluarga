<?php

namespace App\Livewire\Koordinator;

use App\Models\TitikPengambilan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TitikIndex extends Component
{
    public string $nama_lokasi = '';
    public string $alamat = '';
    public string $jam_operasional = '';
    public bool $formTerbuka = false;

    public function simpan(): void
    {
        $data = $this->validate([
            'nama_lokasi'     => ['required', 'string', 'max:255'],
            'alamat'          => ['required', 'string', 'max:255'],
            'jam_operasional' => ['nullable', 'string', 'max:100'],
        ]);

        $user = auth()->user();

        if (!$user->village_code) {
            $this->addError('nama_lokasi', 'Akun Anda belum memiliki kode wilayah desa (village_code).');
            return;
        }

        $data['koordinator_id'] = $user->id;
        $data['wilayah_id']     = $user->village_code;

        TitikPengambilan::create($data);

        $this->reset('nama_lokasi', 'alamat', 'jam_operasional', 'formTerbuka');
    }

    public function render()
    {
        $titik = TitikPengambilan::where('koordinator_id', auth()->id())->latest()->get();
        return view('livewire.koordinator.titik-index', compact('titik'))->title('Titik Pengambilan — PanenKeluarga');
    }
}