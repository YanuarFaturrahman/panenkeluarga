<?php

namespace App\Livewire\Petani;

use App\Models\User;
use App\Models\Notifikasi;
use App\Models\Transaksi;
use App\Notifications\PengajuanPencairanNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Pendapatan extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public string $metode = 'BRI';
    public string $nomor_rekening = '';
    public string $nama_pemilik = '';
    public string $nominal_pencairan = '';

    public array $daftarMetode = [
        'Bank Transfer' => ['BRI', 'BCA', 'Mandiri', 'BNI'],
        'E-Wallet' => ['DANA', 'OVO', 'ShopeePay', 'Gopay']
    ];

    protected $rules = [
        'metode' => 'required',
        'nomor_rekening' => 'required|numeric|digits_between:8,20',
        'nama_pemilik' => 'required|string|min:3|max:100',
        'nominal_pencairan' => 'required|numeric|min:10000',
    ];

    protected $messages = [
        'metode.required' => 'Pilih metode pencairan terlebih dahulu.',
        'nomor_rekening.required' => 'Nomor rekening / e-wallet wajib diisi.',
        'nomor_rekening.numeric' => 'Nomor rekening harus berupa angka.',
        'nama_pemilik.required' => 'Nama pemilik rekening wajib diisi.',
        'nominal_pencairan.required' => 'Nominal pencairan wajib diisi.',
        'nominal_pencairan.min' => 'Minimal pencairan adalah Rp10.000.',
    ];

    public function mount()
    {
        $this->nama_pemilik = auth()->user()->name ?? '';
    }

    public function bukaModal()
    {
        $this->resetValidation();
        $this->showModal = true;
    }

    public function tutupModal()
    {
        $this->showModal = false;
    }

    public function cairkanSekarang()
    {
        $this->validate();

        $petaniId = auth()->id();
        $baseQuery = Transaksi::whereHas('pesertaSesi.sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
            ->where('status_pembayaran', 'lunas');

        $hasStatusPencairan = Schema::hasColumn('transaksi', 'status_pencairan');
        $saldoTersedia = $hasStatusPencairan 
            ? (clone $baseQuery)->where('status_pencairan', 'menunggu')->sum('jumlah_bayar')
            : (clone $baseQuery)->sum('jumlah_bayar');

        if ($saldoTersedia > 0 && (float)$this->nominal_pencairan > $saldoTersedia) {
            $this->addError('nominal_pencairan', 'Nominal pencairan melebihi batas saldo tersedia.');
            return;
        }

        if ($hasStatusPencairan) {
            (clone $baseQuery)->where('status_pencairan', '!=', 'dicairkan')->update(['status_pencairan' => 'menunggu']);
        }

        // Ambil admin dengan pengecekan ketersediaan kolom
        $adminQuery = User::query();
        $hasPeran = Schema::hasColumn('users', 'peran');
        $hasRole = Schema::hasColumn('users', 'role');

        if ($hasPeran && $hasRole) {
            $adminQuery->whereIn('peran', ['admin', 'Admin'])->orWhereIn('role', ['admin', 'Admin']);
        } elseif ($hasPeran) {
            $adminQuery->whereIn('peran', ['admin', 'Admin']);
        } elseif ($hasRole) {
            $adminQuery->whereIn('role', ['admin', 'Admin']);
        }

        $admins = $adminQuery->get();
        
        $payload = [
            'petani_id' => $petaniId,
            'petani_nama' => auth()->user()->name ?? 'Petani',
            'nominal' => $this->nominal_pencairan,
            'metode' => $this->metode,
            'nomor_rekening' => $this->nomor_rekening,
            'nama_pemilik' => $this->nama_pemilik,
        ];

        // Kirim Notifikasi jika class Notification & User tersedia
        if ($admins->isNotEmpty() && class_exists(PengajuanPencairanNotification::class)) {
            Notification::send($admins, new PengajuanPencairanNotification($payload));
        }

        session()->flash('message', 'Permintaan pencairan dana berhasil diajukan ke Admin!');
        
        $this->showModal = false;
        $this->reset(['nominal_pencairan']);
    }

    public function render()
    {
        $petaniId = auth()->id();

        $baseQuery = Transaksi::with(['pesertaSesi.sesi.produk', 'pesertaSesi.konsumen'])
            ->whereHas('pesertaSesi.sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
            ->where('status_pembayaran', 'lunas');

        $totalPendapatan = (clone $baseQuery)->sum('jumlah_bayar');

        $pendapatanBulanIni = (clone $baseQuery)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah_bayar');

        $hasStatusPencairan = Schema::hasColumn('transaksi', 'status_pencairan');

        if ($hasStatusPencairan) {
            $menungguPencairan = (clone $baseQuery)
                ->where('status_pencairan', 'menunggu')
                ->sum('jumlah_bayar');

            $transaksiMenungguCount = (clone $baseQuery)
                ->where('status_pencairan', 'menunggu')
                ->count();

            $sudahDicairkan = (clone $baseQuery)
                ->where('status_pencairan', 'dicairkan')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('jumlah_bayar');
        } else {
            $menungguPencairan = 0;
            $transaksiMenungguCount = 0;
            $sudahDicairkan = $pendapatanBulanIni;
        }

        $grafikData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulanNama = ucfirst($date->translatedFormat('M'));
            $m = $date->format('m');
            $y = $date->format('Y');

            $nominal = (clone $baseQuery)
                ->whereMonth('created_at', $m)
                ->whereYear('created_at', $y)
                ->sum('jumlah_bayar');

            $grafikData[] = [
                'bulan'   => $bulanNama,
                'nominal' => (float) $nominal
            ];
        }

        $transaksi = (clone $baseQuery)->latest()->paginate(10);

        return view('livewire.petani.pendapatan', compact(
            'transaksi',
            'totalPendapatan',
            'pendapatanBulanIni',
            'menungguPencairan',
            'transaksiMenungguCount',
            'sudahDicairkan',
            'grafikData'
        ))->title('Pendapatan — PanenKeluarga');
    }
}