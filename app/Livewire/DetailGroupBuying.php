<?php

namespace App\Livewire;

use App\Models\PesertaSesi;
use App\Models\SesiGroupBuying;
use App\Models\SubsidiNutrisi;
use App\Models\Transaksi;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DetailGroupBuying extends Component
{
    public SesiGroupBuying $sesi;
    public int $jumlahPesanan = 1;
    public bool $sudahGabung = false;

    public function mount(SesiGroupBuying $sesi): void
    {
        $this->sesi = $sesi->load('produk.petani', 'wilayah', 'titikPengambilan', 'peserta.konsumen');
        $this->sudahGabung = $this->sesi->peserta->contains('konsumen_id', auth()->id());
    }

    /**
     * Aksi utama: konsumen bergabung ke sesi group buying.
     * - Membuat baris peserta_sesi (status awal: menunggu_kuota)
     * - Membuat transaksi (status menunggu bayar, disederhanakan jadi langsung "lunas" utk demo)
     * - Menambah jumlah_terkumpul pada sesi
     * - Mengecek & mengonfirmasi kuota otomatis
     * - Mengalokasikan subsidi nutrisi otomatis dari surplus transaksi
     */
    public function gabungGroupBuying(): void
    {
        $this->validate([
            'jumlahPesanan' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        if ($this->sesi->status !== 'berjalan') {
            $this->addError('umum', 'Sesi ini sudah tidak menerima peserta baru.');
            return;
        }

        $subtotal = $this->jumlahPesanan * $this->sesi->harga_satuan;

        $peserta = PesertaSesi::create([
            'sesi_id' => $this->sesi->id,
            'konsumen_id' => auth()->id(),
            'jumlah_pesanan' => $this->jumlahPesanan,
            'subtotal' => $subtotal,
            'status' => 'menunggu_kuota', // Diubah dari 'dikonfirmasi' agar masuk ke antrean menunggu kuota penuh
        ]);

        $alokasiSubsidi = (int) round($subtotal * (Transaksi::PERSEN_SUBSIDI / 100));

        $transaksi = Transaksi::create([
            'peserta_sesi_id' => $peserta->id,
            'kode_transaksi' => 'PK-'.strtoupper(Str::random(8)),
            'jumlah_bayar' => $subtotal,
            'alokasi_subsidi' => $alokasiSubsidi,
            'metode_bayar' => 'transfer_manual',
            'status_pembayaran' => 'lunas',
        ]);

        SubsidiNutrisi::create([
            'wilayah_id' => $this->sesi->wilayah_id,
            'transaksi_id' => $transaksi->id,
            'jumlah_dialokasikan' => $alokasiSubsidi,
            'status' => 'terkumpul',
        ]);

        $this->sesi->increment('jumlah_terkumpul', $this->jumlahPesanan);
        $this->sesi->refresh();
        $this->sesi->cekDanKonfirmasiKuota(); // otomatis ubah status jika kuota tercapai

        $this->sesi->refresh();
        $this->sudahGabung = true;

        session()->flash('sukses', 'Berhasil bergabung! Anda akan mendapat notifikasi saat kuota tercapai.');
    }

    public function render()
    {
        return view('livewire.detail-group-buying')->title($this->sesi->produk->nama_komoditas.' — PanenKeluarga');
    }
}