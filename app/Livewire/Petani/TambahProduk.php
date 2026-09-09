<?php

namespace App\Livewire\Petani;

use App\Models\Produk;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class TambahProduk extends Component
{
    use WithFileUploads;

    public $nama_komoditas = '';
    public $kategori = 'sayur';
    public $satuan = '';
    public $harga = '';
    public $estimasi_stok = '';
    public $estimasi_tanggal_panen = '';
    public $deskripsi = '';
    public $foto;

    public function mount()
    {
        $this->estimasi_tanggal_panen = date('Y-m-d');
    }

    public function simpan($status = 'aktif')
    {
        $this->validate([
            'nama_komoditas' => 'required|string|max:255',
            'kategori' => 'required|string',
            'satuan' => 'required|string|max:30',
            'harga' => 'required|numeric|min:0',
            'estimasi_stok' => 'required|numeric|min:0',
            'estimasi_tanggal_panen' => 'required|date',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|max:5120',
        ]);

        $pathFoto = null;
        if ($this->foto) {
            $pathFoto = $this->foto->store('produk', 'public');
        }

        Produk::create([
            'petani_id' => auth()->id(),
            'nama_komoditas' => $this->nama_komoditas,
            'kategori' => strtolower($this->kategori),
            'satuan' => $this->satuan,
            'harga' => $this->harga,
            'estimasi_stok' => $this->estimasi_stok,
            'estimasi_tanggal_panen' => $this->estimasi_tanggal_panen,
            'deskripsi' => $this->deskripsi,
            'foto' => $pathFoto,
            'status' => $status,
        ]);

        $pesan = $status === 'aktif' ? 'Produk berhasil diterbitkan!' : 'Draf produk berhasil disimpan.';
        session()->flash('sukses', $pesan);

        return $this->redirect(route('petani.produk.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.petani.tambah-produk')->title('Tambah Produk — PanenKeluarga');
    }
}