<?php

namespace App\Livewire;

use App\Models\PesertaSesi;
use App\Models\SesiGroupBuying;
use App\Models\SubsidiNutrisi;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $this->sesi = $sesi->load(['produk.petani', 'wilayah', 'titikPengambilan', 'peserta.konsumen']);
        
        if (auth()->check()) {
            $this->sudahGabung = $this->sesi->peserta->contains('konsumen_id', auth()->id());
        }
    }

    /**
     * Aksi utama: konsumen bergabung ke sesi group buying.
     */
    public function gabungGroupBuying(): void
    {
        // 1. Cek Autentikasi User
        if (!auth()->check()) {
            session()->flash('error', 'Silakan login terlebih dahulu.');
            $this->redirect(route('login'));
            return;
        }

        $userId = auth()->id();
        if (!$userId) {
            $this->addError('umum', 'Pengguna tidak terautentikasi.');
            return;
        }

        // 2. Proteksi jika sudah pernah bergabung
        if ($this->sudahGabung) {
            $this->addError('umum', 'Anda sudah terdaftar dalam sesi group buying ini.');
            return;
        }

        // 3. Hitung Sisa Stok Dinamis
        $sisaStokDinamis = $this->sesi->stok_sisa;
        $isExpired = $this->sesi->tenggat_waktu ? $this->sesi->tenggat_waktu->isPast() : false;

        // Validasi ketersediaan sesi dan stok
        if (strtolower($this->sesi->status) !== 'berjalan' || $isExpired) {
            $this->addError('umum', 'Sesi ini sudah tidak menerima peserta baru atau waktu telah berakhir.');
            return;
        }

        if ($sisaStokDinamis <= 0) {
            $this->addError('umum', 'Stok komoditas untuk sesi ini telah habis.');
            return;
        }

        // Hitung batas maksimal yang boleh dipesan (Maksimal 20 atau sejumlah sisa stok jika sisa stok < 20)
        $maxBatas = min(20, $sisaStokDinamis);

        // 4. Validasi Input Pesanan dengan Batas Maksimal Dinamis
        $this->validate([
            'jumlahPesanan' => [
                'required', 
                'integer', 
                'min:1', 
                "max:{$maxBatas}"
            ],
        ], [
            'jumlahPesanan.max' => "Maksimal pemesanan adalah {$maxBatas} {$this->sesi->produk->satuan}.",
            'jumlahPesanan.min' => "Minimal pemesanan adalah 1 {$this->sesi->produk->satuan}.",
        ]);

        // Double check persediaan stok
        if ($sisaStokDinamis < $this->jumlahPesanan) {
            $this->addError('umum', "Stok komoditas tidak mencukupi. Sisa stok tersedia: {$sisaStokDinamis} {$this->sesi->produk->satuan}.");
            return;
        }

        // 5. Hitung Subtotal & Alokasi Subsidi
        $hargaSatuan = $this->sesi->harga_satuan 
            ?? $this->sesi->harga 
            ?? $this->sesi->produk->harga 
            ?? 0;

        $subtotal = $this->jumlahPesanan * $hargaSatuan;
        $alokasiSubsidi = (int) round($subtotal * (Transaksi::PERSEN_SUBSIDI / 100)); // Subsidi 2% dari konstanta Model

        // 6. Eksekusi Pembelian dengan DB Transaction
        try {
            DB::transaction(function () use ($userId, $subtotal, $alokasiSubsidi) {
                // Simpan Peserta Sesi
                $peserta = new PesertaSesi();
                $peserta->sesi_id = $this->sesi->id;
                $peserta->konsumen_id = $userId;
                $peserta->jumlah_pesanan = $this->jumlahPesanan;
                $peserta->subtotal = $subtotal;
                $peserta->status = 'menunggu_kuota';
                $peserta->save();

                // Simpan Transaksi
                $transaksi = new Transaksi();
                $transaksi->peserta_sesi_id = $peserta->id;
                $transaksi->kode_transaksi = 'PK-' . strtoupper(Str::random(8));
                $transaksi->jumlah_bayar = $subtotal;
                $transaksi->alokasi_subsidi = $alokasiSubsidi;
                $transaksi->metode_bayar = 'transfer_manual';
                $transaksi->status_pembayaran = 'lunas';
                $transaksi->save();

                // Simpan Subsidi Nutrisi
                $subsidi = new SubsidiNutrisi();
                $subsidi->wilayah_id = $this->sesi->wilayah_id;
                $subsidi->transaksi_id = $transaksi->id;
                $subsidi->jumlah_dialokasikan = $alokasiSubsidi;
                $subsidi->status = 'terkumpul';
                $subsidi->save();

                // Update Jumlah Terkumpul Sesi (Otomatis mengurangi stok_sisa)
                $this->sesi->increment('jumlah_terkumpul', $this->jumlahPesanan);
                
                // Segarkan data sesi lalu lakukan verifikasi status kuota
                $this->sesi->refresh();

                if (method_exists($this->sesi, 'cekDanKonfirmasiKuota')) {
                    $this->sesi->cekDanKonfirmasiKuota();
                }
            });

            // Refresh total & relasi peserta komponen
            $this->sesi->load(['produk.petani', 'wilayah', 'titikPengambilan', 'peserta.konsumen']);
            $this->sudahGabung = true;

            session()->flash('sukses', 'Berhasil bergabung! Anda akan mendapat notifikasi saat kuota tercapai.');

        } catch (\Exception $e) {
            Log::error('Error Gabung Group Buying: ' . $e->getMessage());
            $this->addError('umum', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $namaProduk = $this->sesi->produk->nama_komoditas 
            ?? $this->sesi->produk->nama 
            ?? 'Detail Sesi';

        return view('livewire.detail-group-buying')
            ->title($namaProduk . ' — PanenKeluarga');
    }
}