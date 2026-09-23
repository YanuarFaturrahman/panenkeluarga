<div>
    <x-slot name="header">Laporan</x-slot>
    <x-slot name="subheader">Ringkasan performa platform dan ekspor data laporan</x-slot>

    <!-- Flash Message -->
    @if (session()->has('sukses'))
        <div class="mb-4 p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200">
            {{ session('sukses') }}
        </div>
    @endif

    <!-- Card 1: Periode Laporan & Tombol Ekspor -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <h2 class="text-base font-bold text-gray-900">Periode Laporan</h2>
        
        <div class="flex items-center gap-3">
            {{-- Dropdown Periode Dinamis --}}
            <select wire:model.live="bulanPilihan" class="text-xs font-semibold bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-gray-700 focus:ring-emerald-500 focus:border-emerald-500 outline-none cursor-pointer">
                @foreach ($opsiBulan as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>

            <button wire:click="eksporPdf" wire:loading.attr="disabled" class="flex items-center gap-1.5 text-xs font-semibold bg-gray-50 hover:bg-gray-100 border border-gray-200 px-4 py-2 rounded-xl text-gray-700 transition disabled:opacity-50 cursor-pointer">
                <svg wire:loading.remove wire:target="eksporPdf" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span wire:loading wire:target="eksporPdf">Memproses...</span>
                <span wire:loading.remove wire:target="eksporPdf">Ekspor PDF</span>
            </button>

            <button wire:click="eksporExcel" wire:loading.attr="disabled" class="flex items-center gap-1.5 text-xs font-semibold bg-gray-50 hover:bg-gray-100 border border-gray-200 px-4 py-2 rounded-xl text-gray-700 transition disabled:opacity-50 cursor-pointer">
                <svg wire:loading.remove wire:target="eksporExcel" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span wire:loading wire:target="eksporExcel">Memproses...</span>
                <span wire:loading.remove wire:target="eksporExcel">Ekspor Excel</span>
            </button>
        </div>
    </div>

    <!-- Section Middle: Pertumbuhan Transaksi & Ringkasan Bulan Ini -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Kiri: Grafik Chart.js Interaktif -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900">Pertumbuhan Transaksi & GMV</h2>
                <span class="text-xs bg-emerald-50 text-emerald-700 font-semibold px-2.5 py-1 rounded-lg">6 Bulan Terakhir</span>
            </div>

            <!-- Canvas Chart dengan wire:ignore agar DOM Canvas tidak hancur saat Livewire update -->
            <div class="relative w-full h-56" wire:ignore>
                <canvas id="gmvChart"></canvas>
            </div>
        </div>

        <!-- Kanan: Ringkasan Bulan Ini -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-base font-bold text-gray-900 mb-5">Ringkasan Bulan Ini</h2>

            <div class="space-y-4 text-xs">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-500">Total Transaksi</span>
                    <span class="font-bold text-gray-900 text-sm">{{ number_format($totalTransaksi ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-500">Total GMV</span>
                    <span class="font-bold text-gray-900 text-sm">Rp{{ number_format($totalGmv ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-500">Petani Mitra Aktif</span>
                    <span class="font-bold text-gray-900 text-sm">{{ number_format($petaniAktif ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-500">Koordinator Aktif</span>
                    <span class="font-bold text-gray-900 text-sm">{{ number_format($koordinatorAktif ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-500">Surplus Subsidi</span>
                    <span class="font-bold text-gray-900 text-sm">Rp{{ number_format($surplusSubsidi ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Bottom: Kinerja per Wilayah -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-base font-bold text-gray-900 mb-4">Kinerja per Wilayah</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-xs text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3 font-semibold">Wilayah</th>
                        <th class="pb-3 font-semibold">Sesi Group Buying</th>
                        <th class="pb-3 font-semibold">Peserta</th>
                        <th class="pb-3 font-semibold text-right">GMV</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($kinerjaWilayah as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-4 font-bold text-gray-800">{{ $item['nama'] }}</td>
                            <td class="py-4 text-gray-600">{{ $item['sesi'] }} sesi</td>
                            <td class="py-4 text-gray-600">{{ $item['peserta'] }} keluarga</td>
                            <td class="py-4 font-bold text-gray-900 text-right">Rp{{ number_format($item['gmv'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">Belum ada data kinerja wilayah untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script Chart.js CDN & Inisialisasi -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('gmvChart').getContext('2d');
        
        // Buat Gradient Fill untuk Efek Area Chart yang Cantik
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(4, 120, 87, 0.25)'); // Emerald-700 transparan
        gradient.addColorStop(1, 'rgba(4, 120, 87, 0.0)');

        let grafikData = @json($grafikData);

        let chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: grafikData.map(item => item.bulan),
                datasets: [{
                    label: 'Total GMV',
                    data: grafikData.map(item => item.gmv),
                    borderColor: '#047857', // Emerald-700
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35, // Smooth Curve
                    pointBackgroundColor: '#047857',
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
                        backgroundColor: '#064e3b',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let val = context.raw || 0;
                                return 'GMV: Rp ' + new Intl.NumberFormat('id-ID').format(val);
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

        // Event listener saat ada pembaruan dari Livewire
        Livewire.on('updateChart', (data) => {
            if (chart) {
                chart.data.labels = data[0].map(item => item.bulan);
                chart.data.datasets[0].data = data[0].map(item => item.gmv);
                chart.update();
            }
        });
    });
</script>