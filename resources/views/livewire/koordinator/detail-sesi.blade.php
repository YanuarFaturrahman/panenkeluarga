<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Navigation Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('koordinator.sesi.index') }}" wire:navigate class="text-xs font-bold text-gray-500 hover:text-gray-700 inline-flex items-center gap-1.5 bg-white px-3 py-2 rounded-xl border border-gray-100 shadow-sm transition">
            ← Kembali ke Sesi Group Buying
        </a>

        @if($sesi && $sesi->status !== 'selesai')
            <button 
                wire:click="selesaikanSesi" 
                wire:confirm="Apakah Anda yakin ingin menyelesaikan sesi ini?"
                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition"
            >
                Selesaikan Sesi
            </button>
        @endif
    </div>

    <!-- Alert Notifications -->
    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-xl flex items-center justify-between shadow-sm">
            <span>⚠️ {{ session('error') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-xl flex items-center justify-between shadow-sm">
            <span>🎉 {{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
        </div>
    @endif

    @if($sesi)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: informasi Sesi & Daftar Peserta -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Card Header Produk -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">
                            {{ $sesi->wilayah?->nama ?? 'Wilayah' }}
                        </span>
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg uppercase tracking-wider">
                            {{ str_replace('_', ' ', $sesi->status ?? 'Sedang Berjalan') }}
                        </span>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 capitalize">
                            {{ $sesi->produk?->nama_komoditas ?? $sesi->produk?->nama ?? 'Komoditas' }}
                        </h1>
                        <p class="text-xs text-gray-500 mt-1">
                            Petani: <strong class="text-gray-700">{{ $sesi->produk?->petani?->name ?? 'Mitra Petani' }}</strong>
                        </p>
                    </div>

                    <div class="pt-2 border-t border-gray-50 flex items-baseline gap-1">
                        <span class="text-2xl font-extrabold text-emerald-700">
                            Rp{{ number_format($sesi->harga_satuan ?? $sesi->harga_per_satuan ?? $sesi->produk?->harga ?? 0, 0, ',', '.') }}
                        </span>
                        <span class="text-xs text-gray-400 font-medium">/ ikat</span>
                    </div>
                </div>

                <!-- Progress Kuota Sesi -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-700">Progres Kuota</span>
                        @php
                            $terkumpul = $sesi->jumlah_terkumpul ?? 0;
                            $minimum = $sesi->kuota_minimum ?? 10;
                            $persen = min(100, round(($terkumpul / max(1, $minimum)) * 100));
                        @endphp
                        <span class="text-gray-500 font-medium">{{ $terkumpul }} / {{ $minimum }} keluarga ({{ $persen }}%)</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                    </div>
                </div>

                <!-- Manajemen Daftar Peserta Sesi -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-50 pb-3">
                        <h3 class="font-bold text-sm text-gray-800">Daftar Pemesan Warga ({{ $sesi->peserta ? $sesi->peserta->count() : 0 }})</h3>
                    </div>

                    @if($sesi->peserta && $sesi->peserta->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($sesi->peserta as $p)
                                <div class="py-3 flex items-center justify-between gap-4 text-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center">
                                            {{ strtoupper(substr($p->konsumen?->name ?? 'W', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $p->konsumen?->name ?? 'Warga' }}</p>
                                            <p class="text-[10px] text-gray-400">
                                                Pesanan: <strong class="text-emerald-700">{{ $p->jumlah_pesanan }} ikat</strong> (Rp{{ number_format($p->subtotal ?? 0, 0, ',', '.') }})
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Dropdown Ubah Status Peserta oleh Koordinator -->
                                    <div class="flex items-center gap-2">
                                        <select 
                                            wire:change="updateStatusPeserta({{ $p->id }}, $event.target.value)"
                                            class="text-xs font-semibold py-1.5 px-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none"
                                        >
                                            <option value="menunggu_kuota" @selected($p->status === 'menunggu_kuota')>Menunggu Kuota</option>
                                            <option value="dikonfirmasi" @selected($p->status === 'dikonfirmasi')>Dikonfirmasi</option>
                                            <option value="siap_diambil" @selected($p->status === 'siap_diambil')>Siap Diambil</option>
                                            <option value="selesai" @selected($p->status === 'selesai')>Selesai</option>
                                            <option value="batal" @selected($p->status === 'batal')>Batal</option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-8 text-center text-xs text-gray-400">
                            Belum ada warga yang memesan pada sesi ini.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Panel Ringkasan Koordinator -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4 sticky top-6">
                    <h3 class="font-bold text-sm text-gray-800 border-b border-gray-50 pb-3">Informasi Titik Pengambilan</h3>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-gray-400 block font-medium">Titik Lokasi:</span>
                            <span class="font-bold text-gray-700">{{ $sesi->titikPengambilan?->nama_lokasi ?? 'Kantor Desa / Posko' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block font-medium">Alamat Detail:</span>
                            <span class="text-gray-600">{{ $sesi->titikPengambilan?->alamat_lengkap ?? '-' }}</span>
                        </div>
                        <div class="pt-2 border-t border-gray-50">
                            <span class="text-gray-400 block font-medium">Total Terkumpul:</span>
                            <span class="text-lg font-extrabold text-emerald-700">
                                {{ $sesi->jumlah_terkumpul ?? 0 }} Ikat
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Fallback Sesi Tidak Ditemukan -->
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm space-y-3">
            <h3 class="text-base font-bold text-gray-700">Sesi Tidak Ditemukan</h3>
            <p class="text-xs text-gray-400">Sesi group buying yang Anda cari tidak ada atau telah dihapus.</p>
            <a href="{{ route('koordinator.sesi.index') }}" wire:navigate class="inline-block px-4 py-2 bg-[#214332] text-white text-xs font-semibold rounded-xl">
                Kembali ke Daftar Sesi
            </a>
        </div>
    @endif
</div>