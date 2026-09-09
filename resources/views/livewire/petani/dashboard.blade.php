<div>
    <x-slot:header>Dashboard Petani</x-slot:header>
    <x-slot:subheader>Selamat datang kembali, Pak Slamet</x-slot:subheader>

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
                            <th class="px-6 py-3 text-left text-gray-600 font-semibold">KUOTA</th>
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
                                    <p class="font-medium text-gray-800">{{ $p->jumlah_pesanan ?? 0 }}/{{ $p->sesi?->kuota_minimum ?? 0 }} keluarga</p>
                                    <div class="w-24 h-2 bg-gray-200 rounded-full mt-1 overflow-hidden">
                                        @php
                                            $persentase = $p->sesi?->kuota_minimum > 0 
                                                ? (int)round(($p->jumlah_pesanan / $p->sesi?->kuota_minimum) * 100)
                                                : 0;
                                        @endphp
                                        <div class="h-full bg-emerald-500" style="width: {{ min(100, $persentase) }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($p->status === 'menunggu')
                                            bg-yellow-100 text-yellow-800
                                        @elseif ($p->status === 'dikonfirmasi')
                                            bg-emerald-100 text-emerald-800
                                        @elseif ($p->status === 'selesai')
                                            bg-blue-100 text-blue-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($p->status ?? '-') }}
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-6">Pendapatan 7 Hari Terakhir</h3>
            
            <div class="flex items-end justify-between h-48 gap-2">
                @php
                    $maxPendapatan = max($pendapatan7Hari ?? [1]);
                    $hari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                @endphp

                @foreach ($pendapatan7Hari ?? [] as $index => $jumlah)
                    <div class="flex flex-col items-center gap-2 flex-1">
                        <div class="w-full bg-emerald-100 rounded-t-lg overflow-hidden" style="height: {{ ($maxPendapatan > 0 ? ($jumlah / $maxPendapatan) * 100 : 10) }}%">
                            <div class="w-full h-full bg-emerald-500 hover:bg-emerald-600 transition" title="Rp{{ number_format($jumlah, 0, ',', '.') }}"></div>
                        </div>
                        <p class="text-xs text-gray-600 font-medium">{{ $index }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 text-xs text-gray-500">
                <p class="text-center">Total: <span class="font-semibold text-gray-800">Rp{{ number_format(array_sum($pendapatan7Hari ?? []), 0, ',', '.') }}</span></p>
            </div>
        </div>
    </div>
</div>