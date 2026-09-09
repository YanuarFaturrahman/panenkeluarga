<div>
    <x-slot:header>Pesanan Masuk</x-slot:header>
    <x-slot:subheader>Pantau seluruh pesanan dari sesi group buying yang kamu buka</x-slot:subheader>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Total Pesanan</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $totalPesanan }}</h3>
            <span class="text-xs font-semibold text-emerald-600 mt-2 block">+3 minggu ini</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Menunggu Kuota</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $menungguKuota }}</h3>
            <span class="text-xs font-semibold text-amber-600 mt-2 block">perlu dipantau</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Siap Kirim</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $siapKirim }}</h3>
            <span class="text-xs font-semibold text-emerald-600 mt-2 block">kuota terpenuhi</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Terkirim Bulan Ini</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $terkirim }}</h3>
            <span class="text-xs font-medium text-gray-400 mt-2 block">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <!-- Filter Tabs -->
        <div class="flex border-b border-gray-100 gap-6 mb-6 text-sm">
            <button wire:click="$set('filterStatus', 'semua')" 
                class="pb-3 font-semibold transition-colors relative {{ $filterStatus === 'semua' ? 'text-gray-900 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">
                Semua ({{ $totalPesanan }})
            </button>
            <button wire:click="$set('filterStatus', 'menunggu_kuota')" 
                class="pb-3 font-semibold transition-colors relative {{ $filterStatus === 'menunggu_kuota' ? 'text-gray-900 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">
                Menunggu Kuota ({{ $menungguKuota }})
            </button>
            <button wire:click="$set('filterStatus', 'siap_kirim')" 
                class="pb-3 font-semibold transition-colors relative {{ $filterStatus === 'siap_kirim' ? 'text-gray-900 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">
                Siap Kirim ({{ $siapKirim }})
            </button>
            <button wire:click="$set('filterStatus', 'terkirim')" 
                class="pb-3 font-semibold transition-colors relative {{ $filterStatus === 'terkirim' ? 'text-gray-900 border-b-2 border-emerald-600' : 'text-gray-400 hover:text-gray-600' }}">
                Terkirim ({{ $terkirim }})
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3 pl-2">Komoditas</th>
                        <th class="pb-3">Pembeli</th>
                        <th class="pb-3">Kuota</th>
                        <th class="pb-3">Titik Pengambilan</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right pr-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse ($pesanan as $p)
                        @php
                            $terkumpul = $p->sesi->total_terkumpul ?? 0;
                            $target = $p->sesi->target_kuota ?? 1;
                            $persen = min(100, round(($terkumpul / $target) * 100));
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 pl-2 font-bold text-gray-800">
                                {{ $p->sesi->produk->nama_komoditas ?? '-' }}
                            </td>
                            <td class="py-4 text-gray-600 font-medium">
                                {{ $p->sesi->peserta_count ?? 1 }} keluarga
                            </td>
                            <td class="py-4">
                                <div class="w-24 bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-emerald-600 h-2.5 rounded-full" style="width: {{ $persen }}%"></div>
                                    </div>
                            </td>
                            <td class="py-4 text-gray-600 font-medium">
                                {{ $p->sesi->titik_pengambilan ?? '-' }}
                            </td>
                            <td class="py-4">
                                @if(stripos($p->status, 'tunggu') !== false)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100/70 text-amber-700">
                                        Menunggu Kuota
                                    </span>
                                @elseif(stripos($p->status, 'siap') !== false)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100/70 text-emerald-700">
                                        Siap Kirim
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        Terkirim
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 text-right pr-2">
                                <div class="flex items-center justify-end gap-2">
                                    @if(stripos($p->status, 'tunggu') !== false)
                                        <button disabled class="px-3 py-1 bg-gray-100 text-gray-400 rounded-lg text-xs font-bold cursor-not-allowed">
                                            Proses
                                        </button>
                                    @elseif(stripos($p->status, 'siap') !== false)
                                        <button wire:click="updateStatus({{ $p->id }}, 'terkirim')" 
                                                class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 transition-colors">
                                            Tandai Terkirim
                                        </button>
                                    @else
                                        <span class="text-xs font-bold text-gray-400 bg-gray-50 px-3 py-1 rounded-lg">Selesai</span>
                                    @endif

                                    <a href="#" class="text-xs font-bold text-emerald-800 hover:text-emerald-900 ml-1">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada pesanan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $pesanan->links() }}
        </div>
    </div>
</div>