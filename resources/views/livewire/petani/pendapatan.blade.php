<div>
    <x-slot:header>Pendapatan</x-slot:header>
    <x-slot:subheader>Ringkasan hasil penjualan dan pencairan dana</x-slot:subheader>

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
            
            <!-- SVG Line Chart Mock/Visual -->
            <div class="w-full h-48 relative flex items-end">
                <svg class="w-full h-full overflow-visible" viewBox="0 0 500 150" preserveAspectRatio="none">
                    <!-- Line Path -->
                    <path d="M 30,110 L 110,90 L 190,105 L 270,60 L 350,45 L 430,20" 
                          fill="none" 
                          stroke="#487B51" 
                          stroke-width="3" 
                          stroke-linecap="round" 
                          stroke-linejoin="round" />
                    <!-- Chart Nodes -->
                    <circle cx="30" cy="110" r="4" fill="#487B51" />
                    <circle cx="110" cy="90" r="4" fill="#487B51" />
                    <circle cx="190" cy="105" r="4" fill="#487B51" />
                    <circle cx="270" cy="60" r="4" fill="#487B51" />
                    <circle cx="350" cy="45" r="4" fill="#487B51" />
                    <circle cx="430" cy="20" r="4" fill="#487B51" />
                </svg>
            </div>
            
            <!-- Month Labels -->
            <div class="flex justify-between text-xs font-medium text-gray-400 mt-2 px-2">
                <span>Mar</span>
                <span>Apr</span>
                <span>Mei</span>
                <span>Jun</span>
                <span>Jul</span>
                <span>Agu</span>
            </div>
        </div>

        <!-- Withdrawal Method Info -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <h4 class="font-bold text-gray-800 mb-4">Metode Pencairan</h4>
                
                <div class="mb-4">
                    <p class="text-xs text-gray-400 font-medium">Rekening Tujuan</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">BRI · 0092-01-xxxxxx-53-4</p>
                </div>

                <div class="mb-6">
                    <p class="text-xs text-gray-400 font-medium">Jadwal Pencairan Berikutnya</p>
                    <p class="text-sm font-bold text-gray-800 mt-0.5">18 Agustus 2026</p>
                </div>
            </div>

            <button wire:click="cairkanSekarang" 
                class="w-full bg-[#528054] hover:bg-[#436a45] text-white font-semibold py-3 px-4 rounded-xl text-sm transition-colors shadow-sm">
                Cairkan Sekarang
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
</div>