<?php

namespace App\Livewire\Admin;

use App\Models\Transaksi;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Throwable;

class Laporan extends Component
{
    // Menggunakan bulanPilihan sesuai select di view (format "YYYY-MM")
    public $bulanPilihan = '2026-08'; 
    public $bulan;
    public $tahun;

    public function mount()
    {
        $this->parseBulanPilihan();
    }

    // Update otomatis saat dropdown diganti di view
    public function updatedBulanPilihan($value)
    {
        $this->parseBulanPilihan();
    }

    private function parseBulanPilihan()
    {
        // Memecah "2026-08" menjadi tahun dan bulan terpisah
        if ($this->bulanPilihan && str_contains($this->bulanPilihan, '-')) {
            [$this->tahun, $this->bulan] = explode('-', $this->bulanPilihan);
        } else {
            $this->tahun = date('Y');
            $this->bulan = date('m');
        }
    }

    // Aksi Download PDF
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
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
            'transaksi' => $transaksi,
            'totalTransaksi' => $totalTransaksi,
            'totalGmv' => $totalGmv,
            'surplusSubsidi' => $surplusSubsidi,
        ]);

        $fileName = 'Laporan_PanenKeluarga_' . $this->tahun . '_' . $this->bulan . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output(); 
        }, $fileName);
    }

    // Aksi Download Excel/CSV
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
            
            // BOM untuk UTF-8 agar Excel membuka karakter dengan benar
            fputs($file, "\xEF\xBB\xBF");

            // Header Tabel
            fputcsv($file, ['ID Transaksi', 'Kode Transaksi', 'Jumlah Bayar (Rp)', 'Alokasi Subsidi (Rp)', 'Metode Bayar', 'Status', 'Tanggal']);

            // Baris Data
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

        $transaksi = Transaksi::whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun)
            ->get();

        $totalTransaksi = $transaksi->count();
        $totalGmv = $transaksi->sum('jumlah_bayar');
        $surplusSubsidi = $transaksi->sum('alokasi_subsidi');
        $petaniAktif = User::whereIn('peran', ['petani', 'Petani'])->count();
        $koordinatorAktif = User::whereIn('peran', ['koordinator', 'Koordinator'])->count();

        // --- MENGAMBIL DATA GRAFIK DARI DATABASE (6 BULAN TERAKHIR) ---
        $grafikData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            
            // Menggunakan format 3 huruf untuk nama bulan (Jan, Feb, Mar, dst)
            $bulanNama = ucfirst($date->translatedFormat('M')); 
            $m = $date->format('m');
            $y = $date->format('Y');

            $gmvBulanIni = Transaksi::whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->sum('jumlah_bayar');

            $grafikData[] = [
                'bulan' => $bulanNama,
                'gmv' => $gmvBulanIni
            ];
        }
        // -------------------------------------------------------------

        $kinerjaWilayah = [
            ['nama' => 'Kecamatan Sukamaju', 'sesi' => 12, 'peserta' => 140, 'gmv' => 4500000, 'pertumbuhan' => '+15%'],
            ['nama' => 'Kecamatan Makmur', 'sesi' => 8, 'peserta' => 95, 'gmv' => 3100000, 'pertumbuhan' => '+8%'],
        ];

        return view('livewire.admin.laporan', [
            'totalTransaksi'   => $totalTransaksi,
            'totalGmv'         => $totalGmv,
            'surplusSubsidi'   => $surplusSubsidi,
            'petaniAktif'      => $petaniAktif,
            'koordinatorAktif' => $koordinatorAktif,
            'kinerjaWilayah'   => $kinerjaWilayah,
            'grafikData'       => $grafikData,
        ]);
    }
}