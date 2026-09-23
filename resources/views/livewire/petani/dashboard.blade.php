<div>
    <x-slot:header>Dashboard</x-slot:header>
    <x-slot:subheader>Selamat datang kembali, {{ auth()->user()->name ?? 'Petani' }}</x-slot:subheader>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Produk Aktif -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Produk Aktif</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $produkAktif ?? 0 }}</h3>
            <p class="text-xs text-emerald-600 mt-2 font-medium">+{{ $produkAktifMingguIni ?? 0 }} minggu ini</p>
        </div>

        <!-- Pesanan Masuk -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Pesanan Masuk</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $pesananMasuk ?? 0 }}</h3>
            <p class="text-xs text-blue-600 mt-2 font-medium">{{ $pesananMenungguKuota ?? 0 }} menunggu kuota</p>
        </div>

        <!-- Pendapatan Bulan Ini -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Pendapatan Bulan Ini</p>
            <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</h3>
            <p class="text-xs {{ $persentaseKenaikan >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-2 font-medium">
                {{ $persentaseKenaikan >= 0 ? '+' : '' }}{{ $persentaseKenaikan }}% dari bulan lalu
            </p>
        </div>

        <!-- Rata-rata Rating -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-2">Rata-rata Rating</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $rataRating ?? '0.0' }}</h3>
            <p class="text-xs text-amber-600 mt-2 font-medium">dari {{ $jumlahUlasan ?? 0 }} ulasan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pesanan Terbaru -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Pesanan Terbaru</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="px-6 py-3 text-left text-gray-600 font-semibold">KOMODITAS</th>
                            <th class="px-6 py-3 text-left text-gray-600 font-semibold">KUOTA PESERTA</th>
                            <th class="px-6 py-3 text-left text-gray-600 font-semibold">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesananTerbaru ?? [] as $p)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800">{{ $p->sesi?->produk?->nama_komoditas ?? $p->sesi?->produk?->nama ?? 'Komoditas' }}</p>
                                    <p class="text-xs text-gray-500">{{ $p->konsumen?->name ?? 'Pembeli' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        // Hitung jumlah keluarga/peserta unik yang bergabung di sesi ini
                                        $jumlahKeluarga = $p->sesi?->peserta?->count() ?? 0;
                                        $targetKuota = $p->sesi?->kuota_minimum ?? 1;
                                        $persentase = $targetKuota > 0 ? (int)round(($jumlahKeluarga / $targetKuota) * 100) : 0;
                                    @endphp
                                    <p class="font-medium text-gray-800">{{ $jumlahKeluarga }}/{{ $targetKuota }} keluarga</p>
                                    <div class="w-24 h-2 bg-gray-200 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full bg-emerald-500" style="width: {{ min(100, $persentase) }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusStr = strtolower($p->status ?? '');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($statusStr === 'menunggu_kuota' || $statusStr === 'menunggu')
                                            bg-amber-100 text-amber-800
                                        @elseif ($statusStr === 'dikonfirmasi' || $statusStr === 'terpenuhi')
                                            bg-emerald-100 text-emerald-800
                                        @elseif ($statusStr === 'selesai')
                                            bg-blue-100 text-blue-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        @if ($statusStr === 'menunggu_kuota' || $statusStr === 'menunggu')
                                            Menunggu Kuota
                                        @else
                                            {{ str_replace('_', ' ', ucwords($statusStr, '_')) }}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                    <p>Belum ada pesanan masuk saat ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pendapatan 7 Hari Terakhir -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <h3 class="font-semibold text-gray-800 mb-4">Pendapatan 7 Hari Terakhir</h3>
            
            @php
                $values = array_values($pendapatan7Hari ?? []);
                $maxPendapatan = !empty($values) ? max($values) : 0;
            @endphp

            <!-- Area Grafik Batang -->
            <div class="flex items-end justify-between h-52 gap-2 pt-6 pb-2 px-1">
                @foreach ($pendapatan7Hari ?? [] as $hari => $jumlah)
                    @php
                        $heightPercent = $maxPendapatan > 0 ? round(($jumlah / $maxPendapatan) * 100) : 0;
                        if ($jumlah > 0 && $heightPercent < 8) {
                            $heightPercent = 8;
                        }
                    @endphp
                    <div class="flex flex-col items-center h-full justify-end flex-1 group relative">
                        <!-- Tooltip Nilai Rupiah saat Hover -->
                        <div class="absolute -top-8 hidden group-hover:flex flex-col items-center z-10">
                            <span class="bg-gray-800 text-white text-[10px] rounded px-1.5 py-0.5 whitespace-nowrap shadow-md">
                                Rp{{ number_format($jumlah, 0, ',', '.') }}
                            </span>
                            <div class="w-1.5 h-1.5 bg-gray-800 rotate-45 -mt-1"></div>
                        </div>

                        <!-- Bar Container -->
                        <div class="w-full bg-gray-100 rounded-t-lg h-full flex items-end overflow-hidden">
                            <div class="w-full bg-emerald-500 group-hover:bg-emerald-600 transition-all duration-300 rounded-t-lg"
                                 style="height: {{ $jumlah > 0 ? $heightPercent : 4 }}%; opacity: {{ $jumlah > 0 ? '1' : '0.3' }};">
                            </div>
                        </div>

                        <!-- Label Hari -->
                        <p class="text-xs text-gray-600 font-medium mt-2">{{ $hari }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500 text-center">
                <p>Total: <span class="font-semibold text-gray-800">Rp{{ number_format(array_sum($pendapatan7Hari ?? []), 0, ',', '.') }}</span></p>
            </div>
        </div>
    </div>
</div>