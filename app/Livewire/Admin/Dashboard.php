<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\SesiGroupBuying;
use App\Models\Wilayah;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

class Dashboard extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        // 1. Total Petani Mitra (Hanya yang terverifikasi / disetujui)
        $totalPetani = User::where(function($q) {
            $q->where('peran', 'like', '%petani%')
              ->orWhere('peran', 'like', '%Petani%');
        })
        ->whereIn('status_verifikasi', ['terverifikasi', 'disetujui', 'diterima', 'approved'])
        ->count();

        // Fallback jika string status_verifikasi di database bernilai lain
        if ($totalPetani === 0) {
            $totalPetani = User::where(function($q) {
                $q->where('peran', 'like', '%petani%')
                  ->orWhere('peran', 'like', '%Petani%');
            })
            ->whereNotIn('status_verifikasi', ['ditolak', 'rejected', 'pending'])
            ->count();
        }

        $petaniBulanIni = User::where(function($q) {
            $q->where('peran', 'like', '%petani%')
              ->orWhere('peran', 'like', '%Petani%');
        })
        ->whereIn('status_verifikasi', ['terverifikasi', 'disetujui', 'diterima', 'approved'])
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

        // 2. Total Koordinator (Hanya yang terverifikasi)
        $totalKoordinator = User::where(function($q) {
            $q->where('peran', 'like', '%koordinator%')
              ->orWhere('peran', 'like', '%Koordinator%');
        })
        ->whereIn('status_verifikasi', ['terverifikasi', 'disetujui', 'diterima', 'approved'])
        ->count();

        // Fallback untuk Koordinator jika ada variasi string status
        if ($totalKoordinator === 0) {
            $totalKoordinator = User::where(function($q) {
                $q->where('peran', 'like', '%koordinator%')
                  ->orWhere('peran', 'like', '%Koordinator%');
            })
            ->whereNotIn('status_verifikasi', ['ditolak', 'rejected', 'pending'])
            ->count();
        }
        
        try {
            $totalKecamatan = Wilayah::distinct('kecamatan')->count('kecamatan');
            if ($totalKecamatan === 0) {
                $totalKecamatan = Wilayah::count();
            }
        } catch (Throwable $e) {
            $totalKecamatan = 0;
        }

        // 3. Transaksi & GMV & Surplus Terkumpul
        try {
            $transaksiBulanIni = Transaksi::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $gmvBulanIni = Transaksi::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('jumlah_bayar') ?? 0;

            $surplusTerkumpul = Transaksi::sum('alokasi_subsidi') ?? 0;
            if ($surplusTerkumpul == 0) {
                $surplusTerkumpul = Transaksi::sum('jumlah_bayar') ?? 0;
            }
        } catch (Throwable $e) {
            $transaksiBulanIni = 0;
            $gmvBulanIni = 0;
            $surplusTerkumpul = 0;
        }

        // 4. Tren Transaksi 12 Minggu Terakhir
        $trenMingguan = [];
        try {
            for ($i = 11; $i >= 0; $i--) {
                $startOfWeek = now()->subWeeks($i)->startOfWeek();
                $endOfWeek = now()->subWeeks($i)->endOfWeek();

                $count = Transaksi::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                $trenMingguan[] = $count;
            }
        } catch (Throwable $e) {
            $trenMingguan = array_fill(0, 12, 0);
        }

        // 5. Formasi Aktivitas Terbaru
        $aktivitasTerbaru = [];
        try {
            $petaniBaru = User::where(function($q) {
                $q->where('peran', 'like', '%petani%')
                  ->orWhere('peran', 'like', '%Petani%');
            })->latest()->take(2)->get();

            foreach ($petaniBaru as $p) {
                $aktivitasTerbaru[] = [
                    'judul' => 'Petani baru terverifikasi',
                    'sub'   => $p->name . ' — ' . ($p->wilayah?->kelurahan ?? 'Wilayah'),
                    'waktu' => $p->created_at?->diffForHumans() ?? 'Baru saja',
                    'timestamp' => $p->created_at ? $p->created_at->timestamp : 0,
                ];
            }

            if (class_exists('\App\Models\SesiGroupBuying')) {
                $sesiBaru = SesiGroupBuying::with(['produk', 'koordinator.wilayah'])->latest()->take(2)->get();
                foreach ($sesiBaru as $s) {
                    $rtRw = $s->koordinator?->wilayah ? "RT {$s->koordinator->wilayah->nama_rt}/RW {$s->koordinator->wilayah->nama_rw}" : 'Wilayah';
                    $aktivitasTerbaru[] = [
                        'judul' => 'Sesi group buying',
                        'sub'   => ($s->produk?->nama_komoditas ?? 'Komoditas') . ' — ' . $rtRw,
                        'waktu' => $s->created_at?->diffForHumans() ?? 'Baru saja',
                        'timestamp' => $s->created_at ? $s->created_at->timestamp : 0,
                    ];
                }
            }

            usort($aktivitasTerbaru, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
            $aktivitasTerbaru = array_slice($aktivitasTerbaru, 0, 4);
        } catch (Throwable $e) {
            $aktivitasTerbaru = [];
        }

        return view('livewire.admin.dashboard', [
            'totalPetani'       => $totalPetani,
            'petaniBulanIni'    => $petaniBulanIni,
            'totalKoordinator'  => $totalKoordinator,
            'totalKecamatan'    => $totalKecamatan,
            'transaksiBulanIni' => $transaksiBulanIni,
            'gmvBulanIni'       => $gmvBulanIni,
            'surplusTerkumpul'  => $surplusTerkumpul,
            'trenMingguan'      => $trenMingguan,
            'aktivitasTerbaru'  => $aktivitasTerbaru,
        ]);
    }
}