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
            <select wire:model.live="bulanPilihan" class="text-xs font-semibold bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-gray-700 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                <option value="2026-08">Agustus 2026</option>
                <option value="2026-07">Juli 2026</option>
                <option value="2026-06">Juni 2026</option>
            </select>

            <button wire:click="eksporPdf" wire:loading.attr="disabled" class="flex items-center gap-1.5 text-xs font-semibold bg-gray-50 hover:bg-gray-100 border border-gray-200 px-4 py-2 rounded-xl text-gray-700 transition disabled:opacity-50">
                <svg wire:loading.remove wire:target="eksporPdf" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span wire:loading wire:target="eksporPdf">Memproses...</span>
                <span wire:loading.remove wire:target="eksporPdf">Ekspor PDF</span>
            </button>

            <button wire:click="eksporExcel" wire:loading.attr="disabled" class="flex items-center gap-1.5 text-xs font-semibold bg-gray-50 hover:bg-gray-100 border border-gray-200 px-4 py-2 rounded-xl text-gray-700 transition disabled:opacity-50">
                <svg wire:loading.remove wire:target="eksporExcel" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span wire:loading wire:target="eksporExcel">Memproses...</span>
                <span wire:loading.remove wire:target="eksporExcel">Ekspor Excel</span>
            </button>
        </div>
    </div>

    <!-- Section Middle: Pertumbuhan Transaksi & Ringkasan Bulan Ini -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Kiri: Grafik Pertumbuhan Transaksi & GMV -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <h2 class="text-base font-bold text-gray-900 mb-6">Pertumbuhan Transaksi & GMV</h2>

            <div class="relative h-44 flex items-end justify-between px-4 pb-2 border-b border-gray-100">
                <svg class="absolute inset-0 w-full h-full p-4 overflow-visible" preserveAspectRatio="none" viewBox="0 0 500 100">
                    <path d="M 30,80 Q 110,65 190,60 T 350,40 T 470,15" fill="none" stroke="#047857" stroke-width="3" stroke-linecap="round"/>
                    <circle cx="30" cy="80" r="4" fill="#047857" />
                    <circle cx="120" cy="68" r="4" fill="#047857" />
                    <circle cx="210" cy="58" r="4" fill="#047857" />
                    <circle cx="300" cy="48" r="4" fill="#047857" />
                    <circle cx="390" cy="35" r="4" fill="#047857" />
                    <circle cx="470" cy="15" r="4" fill="#047857" />
                </svg>

                <!-- Data Dinamis dari Database -->
                @foreach ($grafikData as $data)
                    <span class="text-xs {{ $loop->last ? 'font-bold text-gray-700' : 'text-gray-500' }} z-10">
                        {{ $data['bulan'] }}
                    </span>
                @endforeach
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
                        <th class="pb-3 font-semibold">GMV</th>
                        <th class="pb-3 font-semibold text-right">Pertumbuhan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($kinerjaWilayah ?? [] as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-4 font-bold text-gray-800">{{ $item['nama'] }}</td>
                            <td class="py-4 text-gray-600">{{ $item['sesi'] }} sesi</td>
                            <td class="py-4 text-gray-600">{{ $item['peserta'] }} keluarga</td>
                            <td class="py-4 font-bold text-gray-900">Rp{{ number_format($item['gmv'], 0, ',', '.') }}</td>
                            <td class="py-4 text-right">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                    {{ $item['pertumbuhan'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">Belum ada data kinerja wilayah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>