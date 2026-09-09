<div>
    <x-slot:header>Dashboard</x-slot:header>
    <x-slot:subheader>{{ $wilayahNama }}</x-slot:subheader>

    {{-- Alert Notifikasi Session --}}
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- KONDISI 1: JIKA KOORDINATOR BELUM PUNYA WILAYAH --}}
    @if (!auth()->user()->wilayah_id)

        @if ($pengajuanPending)
            {{-- Tampilan saat pengajuan sedang diproses Admin --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center mb-6">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3 text-xl font-bold">
                    ⏳
                </div>
                <h3 class="font-bold text-gray-800 text-base mb-1">Pengajuan Wilayah Dalam Proses Verifikasi</h3>
                <p class="text-xs text-gray-600 max-w-md mx-auto mb-3">
                    Pengajuan untuk <strong>RT {{ $pengajuanPending->rt }} / RW {{ $pengajuanPending->rw }} — Kel. {{ $pengajuanPending->kelurahan }}</strong> sedang ditinjau oleh Admin. Anda akan menerima pemberitahuan setelah disetujui.
                </p>
                <span class="inline-block px-3 py-1 bg-amber-200 text-amber-800 rounded-full text-xs font-semibold">
                    Status: Menunggu Persetujuan
                </span>
            </div>
        @else
            {{-- Form Pengajuan Wilayah Baru --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-6">
                <div class="mb-4">
                    <h3 class="font-serif text-lg font-bold text-gray-800">Ajukan Wilayah Domisili</h3>
                    <p class="text-xs text-gray-500">
                        Akun Anda belum terhubung dengan wilayah cakupan. Silakan isi form di bawah untuk mengajukan wilayah tugas Anda kepada Admin.
                    </p>
                </div>

                <form wire:submit="ajukanWilayah" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nomor RT</label>
                            <input type="text" wire:model="rt" placeholder="Contoh: 05" class="w-full text-sm rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('rt') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nomor RW</label>
                            <input type="text" wire:model="rw" placeholder="Contoh: 03" class="w-full text-sm rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('rw') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Kelurahan / Desa</label>
                            <input type="text" wire:model="kelurahan" placeholder="Contoh: Cigadung" class="w-full text-sm rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('kelurahan') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Kecamatan</label>
                            <input type="text" wire:model="kecamatan" placeholder="Contoh: Subang" class="w-full text-sm rounded-xl border-gray-200 focus:border-emerald-500 focus:ring-emerald-500">
                            @error('kecamatan') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-[#538253] hover:bg-[#436a43] text-white text-xs font-semibold rounded-xl shadow-sm transition">
                            Kirim Pengajuan Wilayah
                        </button>
                    </div>
                </form>
            </div>
        @endif

    @endif

    {{-- KONDISI 2: STATISTIK UTAMA (TAMPIL SETELAH WILAYAH TERVERIFIKASI ATAU TETAP TERLIHAT) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        {{-- Card 1: Sesi Aktif --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32">
            <span class="text-xs font-semibold text-gray-500">Sesi Aktif</span>
            <div>
                <span class="text-3xl font-bold text-gray-800 tracking-tight">{{ $sesiAktif }}</span>
                @if($sesiMendekatiTenggat > 0)
                    <p class="text-[11px] font-medium text-emerald-700 mt-1">
                        {{ $sesiMendekatiTenggat }} mendekati tenggat
                    </p>
                @endif
            </div>
        </div>

        {{-- Card 2: Peserta Bulan Ini --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32">
            <span class="text-xs font-semibold text-gray-500">Peserta Bulan Ini</span>
            <div>
                <span class="text-3xl font-bold text-gray-800 tracking-tight">{{ $pesertaBulanIni }}</span>
                <p class="text-[11px] font-medium text-emerald-700 mt-1">
                    {{ $selisihPeserta >= 0 ? '+'.$selisihPeserta : $selisihPeserta }} dari bulan lalu
                </p>
            </div>
        </div>

        {{-- Card 3: Komoditas Terlaris --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32">
            <span class="text-xs font-semibold text-gray-500">Komoditas Terlaris</span>
            <div>
                <h4 class="text-lg font-bold text-gray-800 leading-snug truncate">{{ $komoditasTerlaris }}</h4>
                <p class="text-[11px] font-medium text-emerald-700 mt-1">
                    {{ $totalTerlarisDibuka }}× dibuka bulan ini
                </p>
            </div>
        </div>

        {{-- Card 4: Titik Pengambilan --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32">
            <span class="text-xs font-semibold text-gray-500">Titik Pengambilan</span>
            <div>
                <span class="text-3xl font-bold text-gray-800 tracking-tight">{{ $titikPengambilan }}</span>
                <p class="text-[11px] font-medium text-emerald-700 mt-1 truncate">
                    {{ $titikUtamaNama }}
                </p>
            </div>
        </div>

    </div>

    {{-- Panel Sesi Group Buying --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-serif text-lg font-bold text-gray-800">Sesi Group Buying Aktif</h3>
            @if (auth()->user()->wilayah_id)
                <a href="{{ route('koordinator.sesi.buka') }}" class="px-4 py-2 bg-[#538253] hover:bg-[#436a43] text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1">
                    <span>+</span> <span>Buka Sesi Baru</span>
                </a>
            @endif
        </div>

        <div class="space-y-3">
            @forelse ($sesiTerbaru as $s)
                @php
                    $targetKuota = $s->target_kuota ?? $s->kuota_target ?? 10;
                    $terkumpul = $s->peserta_count ?? 0;
                    $sisaHari = $s->tanggal_selesai ? max(0, (int) now()->diffInDays($s->tanggal_selesai, false)) : 2;
                    $sisaJam = $s->tanggal_selesai ? max(0, (int) now()->diffInHours($s->tanggal_selesai, false)) : 18;
                @endphp

                <div class="p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    
                    <div class="w-full sm:w-1/3">
                        <h4 class="font-bold text-gray-900 text-sm">
                            {{ $s->produk?->nama_komoditas ?? $s->produk?->nama ?? 'Sesi Group Buying' }}
                        </h4>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $s->produk?->petani?->name ?? 'Pak Slamet' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="flex items-center space-x-1">
                            @for ($i = 1; $i <= $targetKuota; $i++)
                                <span class="w-2.5 h-2.5 rounded-full {{ $i <= $terkumpul ? 'bg-[#538253]' : 'bg-gray-200' }}"></span>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500 font-medium ml-2">
                            {{ $terkumpul }}/{{ $targetKuota }} keluarga
                        </span>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                        <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-700 font-medium text-xs">
                            @if($sisaHari > 0)
                                {{ $sisaHari }} hari lagi
                            @else
                                {{ $sisaJam }} jam lagi
                            @endif
                        </span>

                        <a href="{{ route('koordinator.sesi.index') }}" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                            Kelola
                        </a>
                    </div>

                </div>
            @empty
                <div class="py-8 text-center text-gray-400 text-sm">
                    Belum ada sesi group buying yang sedang aktif di wilayah Anda.
                </div>
            @endforelse
        </div>
    </div>
</div>