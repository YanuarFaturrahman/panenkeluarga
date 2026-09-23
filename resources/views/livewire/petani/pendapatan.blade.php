<div>
    <x-slot:header>Pendapatan</x-slot:header>
    <x-slot:subheader>Ringkasan hasil penjualan dan pencairan dana</x-slot:subheader>

    <!-- Flash Message Notification -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 text-sm text-emerald-800 bg-emerald-100/80 rounded-2xl border border-emerald-200 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-base">✅</span>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Top Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Total Pendapatan</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
            <span class="text-xs font-medium text-gray-400 mt-2 block">sejak bergabung</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Bulan Ini</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
            <span class="text-xs font-semibold text-emerald-600 mt-2 block">+18% dari bulan lalu</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Menunggu Pencairan</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($menungguPencairan, 0, ',', '.') }}</h3>
            <span class="text-xs font-bold text-amber-600 mt-2 block">{{ $transaksiMenungguCount }} transaksi</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Sudah Dicairkan</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($sudahDicairkan, 0, ',', '.') }}</h3>
            <span class="text-xs font-medium text-gray-400 mt-2 block">bulan ini</span>
        </div>
    </div>

    <!-- Chart & Withdrawal Method Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Chart Container -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <h4 class="font-bold text-gray-800 mb-4">Pendapatan 6 Bulan Terakhir</h4>
            
            <div class="relative w-full h-56" wire:ignore>
                <canvas id="pendapatanChart"></canvas>
            </div>
        </div>

        <!-- Withdrawal Method Info & Action Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-bold text-gray-800">Metode Pencairan</h4>
                    <button wire:click="bukaModal" class="text-xs font-semibold text-[#538253] hover:underline cursor-pointer">
                        Pengaturan ⚙️
                    </button>
                </div>
                
                <div class="mb-4 bg-pk-cream/50 p-3.5 rounded-xl border border-gray-100">
                    <p class="text-xs text-gray-400 font-medium">Metode & Rekening Tujuan</p>
                    <p class="text-sm font-bold text-pk-dark mt-0.5">
                        {{ $metode }} {{ $nomor_rekening ? '· ' . substr($nomor_rekening, 0, 4) . '-xxxx-' . substr($nomor_rekening, -4) : '· Belum Diatur' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5 capitalize">a.n. {{ $nama_pemilik ?: '-' }}</p>
                </div>

                <div class="mb-6">
                    <p class="text-xs text-gray-400 font-medium">Jadwal Pencairan Berikutnya</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">18 Agustus 2026</p>
                </div>
            </div>

            <button wire:click="bukaModal" 
                class="w-full bg-[#538253] hover:bg-[#436a45] text-white font-semibold py-3 px-4 rounded-xl text-sm transition-all shadow-sm cursor-pointer flex items-center justify-center gap-2">
                <span>💸</span>
                <span>Cairkan Sekarang</span>
            </button>
        </div>
    </div>

    <!-- Payout History Table -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h4 class="font-bold text-gray-800 mb-4">Riwayat Pencairan</h4>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3 pl-2">Tanggal</th>
                        <th class="pb-3">Komoditas Terjual</th>
                        <th class="pb-3">Jumlah</th>
                        <th class="pb-3 text-right pr-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse ($transaksi as $t)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 pl-2 text-gray-600 font-medium text-xs">
                                {{ \Carbon\Carbon::parse($t->created_at)->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-4 font-semibold text-gray-800">
                                {{ $t->pesertaSesi->sesi->produk->nama_komoditas ?? '-' }}
                            </td>
                            <td class="py-4 font-bold text-gray-800">
                                Rp{{ number_format($t->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td class="py-4 text-right pr-2">
                                @if(($t->status_pencairan ?? 'dicairkan') === 'dicairkan')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100/70 text-emerald-800 inline-block">
                                        Dicairkan
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100/70 text-amber-800 inline-block">
                                        Menunggu
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">Belum ada riwayat pencairan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transaksi->links() }}
        </div>
    </div>

    <!-- MODAL PENCAIRAN DINAMIS -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-all">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 relative transform transition-all scale-100">
                
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="text-lg font-bold text-pk-dark flex items-center gap-2">
                        <span>🏦</span> Form Pencairan Dana
                    </h3>
                    <button wire:click="tutupModal" class="text-gray-400 hover:text-gray-600 font-bold text-xl leading-none cursor-pointer">&times;</button>
                </div>

                <form wire:submit.prevent="cairkanSekarang" class="space-y-4">
                    
                    <!-- Pilih Bank / E-Wallet -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Metode Pencairan</label>
                        <select wire:model="metode" class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 p-2.5 border focus:bg-white focus:border-[#538253] focus:ring-[#538253] transition">
                            @foreach ($daftarMetode as $kategori => $pilihan)
                                <optgroup label="{{ $kategori }}">
                                    @foreach ($pilihan as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('metode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nomor Rekening / E-Wallet -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Rekening / No. HP E-Wallet</label>
                        <input type="text" wire:model="nomor_rekening" placeholder="Contoh: 009201012345534" 
                            class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 p-2.5 border focus:bg-white focus:border-[#538253] focus:ring-[#538253] transition">
                        @error('nomor_rekening') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nama Pemilik -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Atas Nama (Pemilik)</label>
                        <input type="text" wire:model="nama_pemilik" placeholder="Nama lengkap sesuai rekening" 
                            class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 p-2.5 border focus:bg-white focus:border-[#538253] focus:ring-[#538253] transition">
                        @error('nama_pemilik') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nominal Pencairan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal yang Dicairkan (Rp)</label>
                        <input type="number" wire:model="nominal_pencairan" placeholder="Contoh: 50000" 
                            class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 p-2.5 border focus:bg-white focus:border-[#538253] focus:ring-[#538253] transition">
                        @error('nominal_pencairan') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-center gap-3 pt-3">
                        <button type="button" wire:click="tutupModal" 
                            class="w-1/2 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                            class="w-1/2 py-2.5 rounded-xl bg-[#538253] hover:bg-[#436a45] text-white font-semibold text-sm transition shadow-sm cursor-pointer">
                            Ajukan Pencairan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    @endif
</div>

<!-- Chart.js Script Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('pendapatanChart').getContext('2d');
        const grafikData = @json($grafikData);

        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(83, 130, 83, 0.25)');
        gradient.addColorStop(1, 'rgba(83, 130, 83, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: grafikData.map(item => item.bulan),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: grafikData.map(item => item.nominal),
                    borderColor: '#538253',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#538253',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#214332',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let val = context.raw || 0;
                                return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 10 },
                            color: '#9ca3af',
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value/1000000) + 'jt';
                                if (value >= 1000) return 'Rp ' + (value/1000) + 'rb';
                                return 'Rp ' + value;
                            }
                        },
                        grid: { color: '#f3f4f6' }
                    },
                    x: {
                        ticks: { font: { size: 11 }, color: '#4b5563' },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>