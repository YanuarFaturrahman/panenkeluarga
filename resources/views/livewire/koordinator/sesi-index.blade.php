<div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-6">
        <!-- Header & Navigasi Tab -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
            <div>
                <h2 class="font-serif text-xl font-bold text-gray-800">Daftar Sesi Group Buying</h2>
                <p class="text-xs text-gray-400 mt-0.5">Kelola sesi aktif dan pantau riwayat sesi yang telah selesai</p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Tab Filter Status -->
                <div class="flex items-center bg-gray-100 p-1 rounded-xl text-xs font-semibold text-gray-600">
                    <button 
                        wire:click="filterByStatus('semua')" 
                        class="px-3 py-1.5 rounded-lg transition {{ $filterStatus === 'semua' ? 'bg-white text-gray-800 shadow-sm' : 'hover:text-gray-900' }}"
                    >
                        Semua
                    </button>
                    <button 
                        wire:click="filterByStatus('berjalan')" 
                        class="px-3 py-1.5 rounded-lg transition {{ $filterStatus === 'berjalan' ? 'bg-white text-emerald-700 shadow-sm' : 'hover:text-gray-900' }}"
                    >
                        Aktif
                    </button>
                    <button 
                        wire:click="filterByStatus('selesai')" 
                        class="px-3 py-1.5 rounded-lg transition {{ $filterStatus === 'selesai' ? 'bg-white text-gray-700 shadow-sm' : 'hover:text-gray-900' }}"
                    >
                        Selesai
                    </button>
                </div>

                @if (Route::has('koordinator.sesi.buka'))
                    <a href="{{ route('koordinator.sesi.buka') }}" wire:navigate class="px-4 py-2 bg-[#214332] hover:bg-emerald-900 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                        <span>+</span> Buka Sesi
                    </a>
                @endif
            </div>
        </div>

        <!-- List Item Card -->
        <div class="space-y-3">
            @forelse ($sesi as $s)
                @php
                    $target = (int) ($s->kuota_minimum ?? 10);
                    $terkumpul = (int) ($s->peserta_count ?? 0);
                    
                    $namaPetani = $s->produk?->petani?->name ?? 'Petani Mitra';
                    $namaProduk = $s->produk?->nama_komoditas ?? $s->produk?->nama ?? 'Komoditas Tanpa Nama';
                    $status = strtolower($s->status ?? 'berjalan');
                @endphp

                <div class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-white rounded-xl border border-gray-100 hover:border-gray-200 transition gap-4">
                    <!-- Informasi Produk -->
                    <div class="min-w-[200px]">
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-900 text-base capitalize">{{ $namaProduk }}</h3>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">Petani: <strong class="text-gray-600">{{ $namaPetani }}</strong></p>
                    </div>

                    <!-- Progress Dots & Angka Kuota Dinamis -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1 flex-wrap">
                            @for ($i = 1; $i <= $target; $i++)
                                <span class="w-2.5 h-2.5 rounded-full {{ $i <= $terkumpul ? 'bg-emerald-600' : 'bg-gray-200' }}"></span>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500 font-medium whitespace-nowrap min-w-[90px]">
                            {{ $terkumpul }}/{{ $target }} keluarga
                        </span>
                    </div>

                    <!-- Status Badge & Tombol Kelola -->
                    <div class="flex items-center gap-3 self-end md:self-auto">
                        <!-- Indikator Badge Status Sesi -->
                        @if($status === 'selesai')
                            <span class="px-3 py-1 font-bold rounded-lg text-[10px] uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">
                                Selesai
                            </span>
                        @elseif($status === 'berjalan')
                            <span class="px-3 py-1 font-bold rounded-lg text-[10px] uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Berjalan
                            </span>
                        @else
                            <span class="px-3 py-1 font-bold rounded-lg text-[10px] uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                                {{ str_replace('_', ' ', $status) }}
                            </span>
                        @endif

                        <!-- Tombol Kelola -->
                        @if (Route::has('koordinator.sesi.show'))
                            <a href="{{ route('koordinator.sesi.show', $s->id) }}" 
                               wire:navigate
                               class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition inline-block">
                                Kelola
                            </a>
                        @elseif (Route::has('koordinator.sesi.detail'))
                            <a href="{{ route('koordinator.sesi.detail', $s->id) }}" 
                               wire:navigate
                               class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition inline-block">
                                Kelola
                            </a>
                        @else
                            <button type="button" 
                                    wire:click="kelola({{ $s->id }})" 
                                    class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                                Kelola
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-gray-400 text-xs">
                    Belum ada sesi group buying pada kategori ini.
                </div>
            @endforelse
        </div>
    </div>

    @if (method_exists($sesi, 'links'))
        <div class="mt-4">
            {{ $sesi->links() }}
        </div>
    @endif
</div>