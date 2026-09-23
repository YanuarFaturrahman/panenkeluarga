<?php

namespace App\Livewire\Koordinator;

use App\Models\PesertaSesi;
use App\Models\SesiGroupBuying;
use App\Models\TitikPengambilan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        /** @var User $user */
        $user = auth()->user();
        $koordinatorId = $user->id;

        // 1. Informasi Wilayah Koordinator
        $wilayahNama = 'Belum Ada Wilayah';

        if ($user->village_code) {
            try {
                $village = DB::table('villages')
                    ->where('code', $user->village_code)
                    ->orWhere('id', $user->village_code)
                    ->first();

                if (!$village) {
                    $village = DB::table('indonesia_villages')
                        ->where('code', $user->village_code)
                        ->orWhere('id', $user->village_code)
                        ->first();
                }

                if ($village) {
                    $namaDesa = $village->name ?? $village->nama ?? '';
                    $wilayahNama = 'Desa/Kel. '.$namaDesa;
                } else {
                    $cleanName = ucfirst(preg_replace('/^k/i', '', $user->name));
                    $wilayahNama = 'Desa/Kel. '.$cleanName;
                }
            } catch (Throwable $e) {
                $cleanName = ucfirst(preg_replace('/^k/i', '', $user->name));
                $wilayahNama = 'Desa/Kel. '.$cleanName;
            }
        } elseif ($user->name) {
            $cleanName = ucfirst(preg_replace('/^k/i', '', $user->name));
            $wilayahNama = 'Desa/Kel. '.$cleanName;
        }

        // 2. Query Sesi yang benar-benar Aktif (Status 'berjalan' & Belum lewat tenggat)
        $sesiAktif = 0;
        $sesiMendekatiTenggat = 0;
        
        try {
            $semuaSesiKoordinator = SesiGroupBuying::where('koordinator_id', $koordinatorId)->get();

            $sesiAktifCollection = $semuaSesiKoordinator->filter(function ($item) {
                $status = strtolower(trim($item->status ?? ''));
                $tenggat = $item->tenggat_waktu ?? $item->tanggal_selesai;
                $isAktifStatus = in_array($status, ['berjalan', 'kuota_tercapai', 'aktif']);
                $belumExpired = $tenggat ? Carbon::parse($tenggat)->isFuture() : true;

                return $isAktifStatus && $belumExpired;
            });

            $sesiAktif = $sesiAktifCollection->count();

            $sesiMendekatiTenggat = $sesiAktifCollection->filter(function ($item) {
                $tenggat = $item->tenggat_waktu ?? $item->tanggal_selesai;
                return $tenggat && Carbon::parse($tenggat)->lte(now()->addDays(2));
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
                if ($sesiWithCount->peserta->count() > 0) {
                    $komoditasTerlaris = $sesiWithCount->produk?->nama_komoditas ?? $sesiWithCount->produk?->nama ?? 'Umum';
                    $totalTerlarisDibuka = $sesiWithCount->peserta->count();
                }
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
                $titikUtamaNama = $titikFirst->nama_lokasi ?? $titikFirst->nama_titik ?? $titikFirst->nama ?? $titikFirst->alamat;
            }
        } catch (Throwable $e) {
            $titikPengambilan = 0;
        }

        // 6. Sesi Group Buying Aktif untuk List Utama
        try {
            $sesiTerbaru = SesiGroupBuying::with(['produk', 'produk.petani'])
                ->withCount('peserta as peserta_count')
                ->where('koordinator_id', $koordinatorId)
                ->whereIn('status', ['berjalan', 'aktif'])
                ->where(function ($q) {
                    $q->whereNull('tenggat_waktu')
                      ->orWhere('tenggat_waktu', '>', now());
                })
                ->latest()
                ->take(5)
                ->get();
        } catch (Throwable $e) {
            $sesiTerbaru = collect();
        }

        return view('livewire.koordinator.dashboard', compact(
            'wilayahNama',
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