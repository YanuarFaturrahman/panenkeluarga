<div>
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-serif text-xl font-bold text-pk-dark">Sesi Group Buying Aktif</h2>
            @if (Route::has('koordinator.sesi.buka'))
                <a href="{{ route('koordinator.sesi.buka') }}" wire:navigate class="px-4 py-2 bg-[#3b5e4c] hover:bg-pk-dark text-white rounded-xl text-sm font-semibold transition flex items-center gap-1">
                    <span>+</span> Buka Sesi Baru
                </a>
            @endif
        </div>

        <!-- List Item Card -->
        <div class="space-y-4">
            @forelse ($sesi as $s)
                @php
                    $target = $s->kuota_minimum ?? 10;
                    $terkumpul = $s->jumlah_terkumpul ?? $s->peserta_count ?? 0;
                    $namaPetani = $s->produk?->petani?->name ?? 'Petani Tani Mitra';
                    $namaProduk = $s->produk?->nama_komoditas ?? $s->produk?->nama ?? 'Komoditas Tanpa Nama';
                @endphp

                <div class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-white rounded-xl border border-gray-100 hover:border-gray-200 transition gap-4">
                    <!-- Informasi Produk -->
                    <div class="min-w-[200px]">
                        <h3 class="font-bold text-gray-900 text-base">{{ $namaProduk }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $namaPetani }}</p>
                    </div>

                    <!-- Progress Dots & Angka Kuota -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= $target; $i++)
                                <span class="w-2.5 h-2.5 rounded-full {{ $i <= $terkumpul ? 'bg-[#538253]' : 'bg-gray-200' }}"></span>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500 font-medium whitespace-nowrap min-w-[80px]">
                            {{ $terkumpul }}/{{ $target }} keluarga
                        </span>
                    </div>

                    <!-- Badge Waktu & Tombol Aksi -->
                    <div class="flex items-center gap-3 self-end md:self-auto">
                        <span class="px-3 py-1 font-semibold rounded-lg text-xs whitespace-nowrap {{ $s->tenggat_waktu && $s->tenggat_waktu->isPast() ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-700' }}">
                            {{-- Menggunakan Accessor sisa_waktu secara Dinamis --}}
                            {{ $s->sisa_waktu }}
                        </span>
                        <a href="{{ Route::has('koordinator.sesi.show') ? route('koordinator.sesi.show', $s->id) : '#' }}" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                            Kelola
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-gray-400 text-sm">
                    Belum ada sesi group buying yang aktif.
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