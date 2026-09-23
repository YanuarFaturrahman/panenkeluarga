<div>
    <x-slot:header>Dashboard</x-slot:header>
    <x-slot:subheader>{{ $wilayahNama }}</x-slot:subheader>

    {{-- Alert Notifikasi Session --}}
    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- STATISTIK UTAMA --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        {{-- Card 1: Sesi Aktif --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-28">
            <span class="text-xs font-semibold text-gray-500">Sesi Aktif</span>
            <div>
                <span class="text-3xl font-bold text-gray-800 tracking-tight">{{ $sesiAktif }}</span>
            </div>
        </div>

        {{-- Card 2: Peserta Bulan Ini --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-28">
            <span class="text-xs font-semibold text-gray-500">Peserta Bulan Ini</span>
            <div>
                <span class="text-3xl font-bold text-gray-800 tracking-tight">{{ $pesertaBulanIni }}</span>
            </div>
        </div>

        {{-- Card 3: Komoditas Terlaris --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-28">
            <span class="text-xs font-semibold text-gray-500">Komoditas Terlaris</span>
            <div>
                <h4 class="text-xl font-bold text-gray-800 leading-snug truncate">{{ $komoditasTerlaris }}</h4>
            </div>
        </div>

        {{-- Card 4: Titik Pengambilan --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-28">
            <span class="text-xs font-semibold text-gray-500">Titik Pengambilan</span>
            <div>
                <span class="text-3xl font-bold text-gray-800 tracking-tight">{{ $titikPengambilan }}</span>
            </div>
        </div>

    </div>

    {{-- Panel Sesi Group Buying --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-serif text-lg font-bold text-gray-800">Sesi Group Buying Aktif</h3>
            <a href="{{ route('koordinator.sesi.buka') }}" wire:navigate class="px-4 py-2 bg-[#538253] hover:bg-[#436a43] text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1">
                <span>+</span> <span>Buka Sesi Baru</span>
            </a>
        </div>

        <div class="space-y-3">
            @forelse ($sesiTerbaru as $s)
                @php
                    $targetKuota = $s->kuota_minimum ?? $s->target_kuota ?? 10;
                    $terkumpul = $s->jumlah_terkumpul ?? $s->peserta_count ?? 0;
                    
                    $tenggat = $s->tenggat_waktu ?? $s->tanggal_selesai;
                    $deadline = $tenggat ? \Carbon\Carbon::parse($tenggat) : null;
                    
                    $sisaHari = $deadline ? (int) now()->diffInDays($deadline, false) : 0;
                    $sisaJam = $deadline ? (int) now()->diffInHours($deadline, false) : 0;
                @endphp

                <div class="p-4 rounded-xl border border-gray-100 hover:border-gray-200 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    
                    <div class="w-full sm:w-1/3">
                        <h4 class="font-bold text-gray-900 text-sm">
                            {{ $s->produk?->nama_komoditas ?? $s->produk?->nama ?? 'Sesi Group Buying' }}
                        </h4>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Petani: {{ $s->produk?->petani?->name ?? 'Anonim' }}
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
                            @elseif($sisaJam > 0)
                                {{ $sisaJam }} jam lagi
                            @else
                                Berakhir
                            @endif
                        </span>

                        <a href="{{ route('koordinator.sesi.index') }}" wire:navigate class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
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