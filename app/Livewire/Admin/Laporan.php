<?php

namespace App\Livewire\Admin;

use App\Models\Transaksi;
use App\Models\User;
use App\Models\SesiGroupBuying;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class Laporan extends Component
{
    public $bulanPilihan; 
    public $bulan;
    public $tahun;

    public function mount()
    {
        $this->bulanPilihan = now()->format('Y-m');
        $this->parseBulanPilihan();
    }

    public function updatedBulanPilihan($value)
    {
        $this->parseBulanPilihan();

        // Ambil data grafik terbaru lalu kirim event ke frontend JavaScript
        $grafikData = $this->getGrafikData();
        $this->dispatch('updateChart', $grafikData);
    }

    private function parseBulanPilihan()
    {
        if ($this->bulanPilihan && str_contains($this->bulanPilihan, '-')) {
            [$this->tahun, $this->bulan] = explode('-', $this->bulanPilihan);
        } else {
            $this->tahun = date('Y');
            $this->bulan = date('m');
        }
    }

    private function getGrafikData()
    {
        $grafikData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulanNama = ucfirst($date->translatedFormat('M')); 
            $m = $date->format('m');
            $y = $date->format('Y');

            $gmvBulanIni = Transaksi::whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->sum('jumlah_bayar');

            $grafikData[] = [
                'bulan' => $bulanNama,
                'gmv'   => (float) $gmvBulanIni
            ];
        }

        return $grafikData;
    }

    public function eksporPdf()
    {
        $this->parseBulanPilihan();

        $transaksi = Transaksi::whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun)
            ->get();

        $totalTransaksi = $transaksi->count();
        $totalGmv = $transaksi->sum('jumlah_bayar');
        $surplusSubsidi = $transaksi->sum('alokasi_subsidi');

        $pdf = Pdf::loadView('pdf.laporan-bulanan', [
            'bulan'          => $this->bulan,
            'tahun'          => $this->tahun,
            'transaksi'      => $transaksi,
            'totalTransaksi' => $totalTransaksi,
            'totalGmv'       => $totalGmv,
            'surplusSubsidi' => $surplusSubsidi,
        ]);

        $fileName = 'Laporan_PanenKeluarga_' . $this->tahun . '_' . $this->bulan . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output(); 
        }, $fileName);
    }

    public function eksporExcel()
    {
        $this->parseBulanPilihan();

        $transaksi = Transaksi::whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun)
            ->get();

        $fileName = 'Laporan_PanenKeluarga_' . $this->tahun . '_' . $this->bulan . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($transaksi) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['ID Transaksi', 'Kode Transaksi', 'Jumlah Bayar (Rp)', 'Alokasi Subsidi (Rp)', 'Metode Bayar', 'Status', 'Tanggal']);

            foreach ($transaksi as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->kode_transaksi ?? '-',
                    $item->jumlah_bayar ?? 0,
                    $item->alokasi_subsidi ?? 0,
                    $item->metode_bayar ?? '-',
                    $item->status_pembayaran ?? '-',
                    $item->created_at ? $item->created_at->format('Y-m-d H:i') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $this->parseBulanPilihan();

        // 1. Data Ringkasan Bulan Ini
        $transaksi = Transaksi::whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun)
            ->get();

        $totalTransaksi = $transaksi->count();
        $totalGmv       = $transaksi->sum('jumlah_bayar');
        $surplusSubsidi = $transaksi->sum('alokasi_subsidi');
        
        $petaniAktif      = User::whereIn('peran', ['petani', 'Petani'])->count();
        $koordinatorAktif = User::whereIn('peran', ['koordinator', 'Koordinator'])->count();

        // 2. Opsi Periode Bulan (6 Bulan Terakhir)
        $opsiBulan = [];
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            $opsiBulan[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->translatedFormat('F Y')
            ];
        }

        // 3. Data Grafik
        $grafikData = $this->getGrafikData();

        // 4. Data Kinerja per Wilayah
        $totalSesi     = SesiGroupBuying::count();
        $totalPeserta  = $transaksi->pluck('user_id')->filter()->unique()->count();

        $kinerjaWilayah = [];
        if ($totalTransaksi > 0 || $totalSesi > 0) {
            $kinerjaWilayah[] = [
                'nama'    => 'Kecamatan Subang',
                'sesi'    => $totalSesi,
                'peserta' => $totalPeserta > 0 ? $totalPeserta : $totalTransaksi,
                'gmv'     => $totalGmv,
            ];
        }

        return view('livewire.admin.laporan', [
            'totalTransaksi'   => $totalTransaksi,
            'totalGmv'         => $totalGmv,
            'surplusSubsidi'   => $surplusSubsidi,
            'petaniAktif'      => $petaniAktif,
            'koordinatorAktif' => $koordinatorAktif,
            'kinerjaWilayah'   => $kinerjaWilayah,
            'grafikData'       => $grafikData,
            'opsiBulan'        => $opsiBulan,
        ]);
    }
}