<div>
    <x-slot name="header">Subsidi Nutrisi</x-slot>
    <x-slot name="subheader">Kelola alokasi dan penyaluran dana subsidi gizi anak</x-slot>

    <!-- Flash Message -->
    @if (session()->has('sukses'))
        <div class="mb-4 p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200">
            {{ session('sukses') }}
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Surplus Terkumpul</p>
            <h3 class="text-2xl font-bold text-gray-900">Rp{{ number_format($surplusTerkumpul ?? 0, 0, ',', '.') }}</h3>
            <p class="text-xs text-gray-400 mt-1">bulan ini</p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Sudah Disalurkan</p>
            <h3 class="text-2xl font-bold text-gray-900">Rp{{ number_format($sudahDisalurkan ?? 0, 0, ',', '.') }}</h3>
            <p class="text-xs font-semibold text-emerald-600 mt-1">
                {{ $surplusTerkumpul > 0 ? round(($sudahDisalurkan / $surplusTerkumpul) * 100) : 0 }}% dari surplus
            </p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Anak Penerima Manfaat</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ $penerimaManfaat ?? 0 }}</h3>
            <p class="text-xs text-gray-400 mt-1">di {{ $totalRW ?? 0 }} RW</p>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 mb-1">Menunggu Penyaluran</p>
            <h3 class="text-2xl font-bold text-gray-900">Rp{{ number_format($menungguPenyaluran ?? 0, 0, ',', '.') }}</h3>
            <p class="text-xs font-semibold text-amber-600 mt-1">{{ $totalPengajuan ?? 0 }} pengajuan</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Table Pengajuan -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Pengajuan Penyaluran</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-3 font-semibold">Wilayah</th>
                            <th class="pb-3 font-semibold">Koordinator</th>
                            <th class="pb-3 font-semibold">Anak</th>
                            <th class="pb-3 font-semibold">Nominal</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($pengajuan as $item)
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-4 font-bold text-gray-800">
                                    {{ $item->wilayah?->nama_lengkap ?? 'RT 05 / RW 03 — Kel. Cigadung' }}
                                </td>
                                <td class="py-4 text-gray-600">Bu Wulandari</td>
                                <td class="py-4 text-gray-600">{{ $item->jumlah_anak_penerima ?? 0 }} anak</td>
                                <td class="py-4 font-bold text-gray-900">
                                    Rp{{ number_format($item->jumlah_dialokasikan ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-4">
                                    @if ($item->status === 'disalurkan' || $item->status === 'disetujui')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Disetujui</span>
                                    @elseif ($item->status === 'diajukan')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Diajukan</span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">{{ ucfirst($item->status) }}</span>
                                    @endif
                                </td>
                                <td class="py-4 text-right">
                                    <button wire:click="lihatDetail({{ $item->id }})" class="text-xs font-semibold text-emerald-700 hover:underline">
                                        Lihat
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">Belum ada data pengajuan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pengajuan->links() }}
            </div>
        </div>

        <!-- Sidebar Penyaluran -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-6">Penyaluran per Wilayah</h2>

                <div class="space-y-5">
                    @forelse ($penyaluranWilayah as $row)
                        @php $percentage = ($row->total_anak / $maxAnak) * 100; @endphp
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1.5">
                                <span class="text-gray-800">{{ $row->wilayah?->nama_lengkap ?? 'RT 05 / RW 03 — Kel. Cigadung' }}</span>
                                <span class="text-gray-400">{{ $row->total_anak }} anak</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-emerald-700 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 py-6 text-sm">Belum ada data wilayah.</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-8">
                <button wire:click="buatLaporanPublik" class="w-full py-3 px-4 bg-emerald-800 hover:bg-emerald-900 text-white text-sm font-semibold rounded-xl transition">
                    Buat Laporan Publik
                </button>
            </div>
        </div>

    </div>

    <!-- Modal Detail Pengajuan -->
    @if ($showDetailModal && $selectedSubsidi)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Detail Pengajuan Subsidi</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Wilayah</span>
                        <span class="font-semibold text-gray-800">{{ $selectedSubsidi->wilayah?->nama_lengkap ?? 'RT 05 / RW 03 — Kel. Cigadung' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Nominal Alokasi</span>
                        <span class="font-semibold text-gray-900">Rp{{ number_format($selectedSubsidi->jumlah_dialokasikan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Jumlah Anak Penerima</span>
                        <span class="font-semibold text-gray-800">{{ $selectedSubsidi->jumlah_anak_penerima ?? 1 }} Anak</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Status</span>
                        <span class="font-semibold text-emerald-600">{{ ucfirst($selectedSubsidi->status) }}</span>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button wire:click="closeModal" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl">
                        Tutup
                    </button>
                    @if ($selectedSubsidi->status !== 'disalurkan' && $selectedSubsidi->status !== 'disetujui')
                        <button wire:click="salurkan({{ $selectedSubsidi->id }}, {{ $selectedSubsidi->jumlah_anak_penerima ?? 1 }})" class="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs rounded-xl">
                            Proses Disalurkan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>