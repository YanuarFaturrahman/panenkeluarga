<?php

namespace App\Livewire\Koordinator;

use App\Models\PengajuanWilayah;
use App\Models\PesertaSesi;
use App\Models\SesiGroupBuying;
use App\Models\TitikPengambilan;
use App\Models\User;
use App\Notifications\PengajuanWilayahBaruNotification;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    // Properti Form Pengajuan Wilayah
    public string $rt = '';
    public string $rw = '';
    public string $kelurahan = '';
    public string $kecamatan = '';

    protected function rules(): array
    {
        return [
            'rt' => 'required|numeric|digits_between:1,3',
            'rw' => 'required|numeric|digits_between:1,3',
            'kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
        ];
    }

    public function ajukanWilayah(): void
    {
        $this->validate();

        /** @var User $user */
        $user = auth()->user();

        // 1. Simpan pengajuan ke database
        $pengajuan = PengajuanWilayah::create([
            'user_id' => $user->id,
            'rt' => sprintf('%02d', $this->rt),
            'rw' => sprintf('%02d', $this->rw),
            'kelurahan' => $this->kelurahan,
            'kecamatan' => $this->kecamatan,
            'status' => 'pending',
        ]);

        // 2. Kirim notifikasi ke semua user ber-role Admin
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PengajuanWilayahBaruNotification($pengajuan));
            }
        } catch (Throwable $e) {
            // Mengabaikan error jika pengiriman notifikasi gagal agar proses tidak terhenti
        }

        session()->flash('success', 'Pengajuan wilayah berhasil dikirim. Menunggu verifikasi Admin.');
        $this->reset(['rt', 'rw', 'kelurahan', 'kecamatan']);
    }

    public function render()
    {
        /** @var User $user */
        $user = auth()->user();
        $koordinatorId = $user->id;

        // Cek Status Pengajuan Pending
        $pengajuanPending = PengajuanWilayah::where('user_id', $koordinatorId)
            ->where('status', 'pending')
            ->latest()
            ->first();

        // 1. Informasi Wilayah Koordinator
        $wilayahNama = 'Belum Ada Wilayah';
        if ($user->wilayah) {
            $w = $user->wilayah;
            $wilayahNama = "RT {$w->nama_rt} / RW {$w->nama_rw}" . ($w->kelurahan ? " — Kel. {$w->kelurahan}" : '');
        }

        // 2. Sesi Aktif
        $sesiAktif = 0;
        $sesiMendekatiTenggat = 0;
        try {
            $semuaSesiKoordinator = SesiGroupBuying::where('koordinator_id', $koordinatorId)->get();

            $sesiAktifCollection = $semuaSesiKoordinator->filter(function ($item) {
                $status = strtolower(trim($item->status));
                return in_array($status, ['berjalan', 'kuota_tercapai', 'aktif', 'active']);
            });

            $sesiAktif = $sesiAktifCollection->count();

            $sesiMendekatiTenggat = $sesiAktifCollection->filter(function ($item) {
                return $item->tanggal_selesai && \Carbon\Carbon::parse($item->tanggal_selesai)->lte(now()->addDays(2));
            })->count();
        } catch (Throwable $e) {
            $sesiAktif = 0;
        }

        // 3. Total Peserta
        $pesertaBulanLalu = 0;
        $pesertaBulanIni = 0;
        try {
            $pesertaBulanIni = PesertaSesi::whereHas('sesi', fn ($q) => $q->where('koordinator_id', $koordinatorId))
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $pesertaBulanLalu = PesertaSesi::whereHas('sesi', fn ($q) => $q->where('koordinator_id', $koordinatorId))
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();
        } catch (Throwable $e) {
            $pesertaBulanIni = 0;
        }
        $selisihPeserta = $pesertaBulanIni - $pesertaBulanLalu;

        // 4. Komoditas Terlaris
        $komoditasTerlaris = '-';
        $totalTerlarisDibuka = 0;
        try {
            $sesiDenganPeserta = SesiGroupBuying::where('koordinator_id', $koordinatorId)
                ->with(['produk', 'peserta'])
                ->get();

            if ($sesiWithCount = $sesiDenganPeserta->sortByDesc(fn ($s) => $s->peserta->count())->first()) {
                $komoditasTerlaris = $sesiWithCount->produk?->nama_komoditas ?? $sesiWithCount->produk?->nama ?? 'Umum';
                $totalTerlarisDibuka = $sesiWithCount->peserta->count();
            }
        } catch (Throwable $e) {
        }

        // 5. Titik Pengambilan
        $titikUtamaNama = '-';
        $titikPengambilan = 0;
        try {
            $titikPengambilan = TitikPengambilan::where('koordinator_id', $koordinatorId)->count();
            $titikFirst = TitikPengambilan::where('koordinator_id', $koordinatorId)->first();
            if ($titikFirst) {
                $titikUtamaNama = $titikFirst->nama_titik ?? $titikFirst->nama;
            }
        } catch (Throwable $e) {
            $titikPengambilan = 0;
        }

        // 6. Sesi Terbaru
        try {
            $sesiTerbaru = SesiGroupBuying::with(['produk', 'produk.petani'])
                ->withCount('peserta as peserta_count')
                ->where('koordinator_id', $koordinatorId)
                ->latest()
                ->take(5)
                ->get();
        } catch (Throwable $e) {
            $sesiTerbaru = collect();
        }

        return view('livewire.koordinator.dashboard', compact(
            'wilayahNama',
            'pengajuanPending',
            'sesiAktif',
            'sesiMendekatiTenggat',
            'pesertaBulanIni',
            'selisihPeserta',
            'komoditasTerlaris',
            'totalTerlarisDibuka',
            'titikPengambilan',
            'titikUtamaNama',
            'sesiTerbaru'
        ))->title('Dashboard Koordinator — PanenKeluarga');
    }
}