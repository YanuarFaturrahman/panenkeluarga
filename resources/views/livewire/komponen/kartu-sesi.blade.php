<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between h-full">
    <div>
        {{-- Area Foto Produk --}}
        <div class="h-36 bg-emerald-800 flex items-center justify-center text-white text-4xl relative overflow-hidden">
            @php
                // 1. Ambil foto dari produk
                $foto = $sesi->produk->foto ?? $sesi->foto ?? null;
                $kategori = strtolower($sesi->produk->kategori ?? $sesi->kategori ?? '');
                
                // 2. Ambil nilai Stok Dinamis (stok_sisa dari Model atau hitung sisa stok secara langsung)
                $stok = $sesi->stok_sisa 
                    ?? ( ($sesi->produk->stok ?? $sesi->produk->estimasi_stok ?? 0) - ($sesi->jumlah_terkumpul ?? 0) );
                $stok = max(0, $stok);
                
                // 3. Cek Status Waktu
                $isExpired = false;
                if (!empty($sesi->tenggat_waktu)) {
                    $isExpired = \Carbon\Carbon::parse($sesi->tenggat_waktu)->isPast();
                }

                // 4. Cek Status Sesi
                $statusSesi = strtolower($sesi->status ?? 'berjalan');
                
                // Sesi Aktif HANYA jika: status sesuai, stok > 0, dan belum kedaluwarsa
                $isAktif = in_array($statusSesi, ['berjalan', 'aktif', 'open']) 
                           && $stok > 0 
                           && !$isExpired;
            @endphp

            @if ($foto && (Storage::disk('public')->exists($foto) || file_exists(public_path('storage/' . $foto))))
                <img 
                    src="{{ asset('storage/' . $foto) }}" 
                    alt="Foto Produk" 
                    class="w-full h-full object-cover"
                >
            @else
                {{-- Fallback Emoji jika gambar tidak ada --}}
                @if ($kategori === 'buah')
                    🍅
                @elseif ($kategori === 'protein')
                    🥚
                @else
                    🥬
                @endif
            @endif

            {{-- Badge Status / Sisa Waktu --}}
            @if ($isAktif)
                @if (isset($sesi->sisa_waktu))
                    <span class="absolute top-3 right-3 bg-red-100 text-red-600 text-xs px-2.5 py-1 rounded-full font-medium shadow-sm">
                        {{ $sesi->sisa_waktu }}
                    </span>
                @endif
            @else
                <span class="absolute top-3 right-3 bg-red-600 text-white text-xs px-2.5 py-1 rounded-full font-medium shadow-sm">
                    {{ $stok <= 0 ? 'Stok Habis' : 'Berakhir' }}
                </span>
            @endif
        </div>

        {{-- Detail Produk --}}
        <div class="p-4 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                    {{ $sesi->produk->kategori ?? 'Sayur' }}
                </span>
                <span class="text-xs text-gray-400">
                    Stok: {{ $stok }} 
                    {{ $sesi->produk->satuan ?? 'satuan' }}
                </span>
            </div>

            <h3 class="font-bold text-gray-800 text-base line-clamp-1">
                {{ $sesi->produk->nama_komoditas ?? 'Komoditas' }}
            </h3>

            <p class="text-xs text-gray-500 line-clamp-1">
                {{ $sesi->produk->petani->name ?? $sesi->koordinator->name ?? 'Petani Mitra' }}
            </p>

            <div class="pt-2 flex items-baseline gap-1">
                <span class="font-bold text-emerald-700 text-lg">
                    Rp{{ number_format(
                        $sesi->harga_satuan 
                        ?? $sesi->produk->harga 
                        ?? 0, 
                        0, ',', '.'
                    ) }}
                </span>
                <span class="text-xs text-gray-500">
                    / {{ $sesi->produk->satuan ?? 'satuan' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="p-4 pt-0">
        @if ($isAktif)
            {{-- Tombol Aktif / Bisa Diklik --}}
            <a href="{{ route('produk.show', $sesi->id) }}" 
               class="block w-full text-center py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-medium transition-colors shadow-sm cursor-pointer">
                Gabung Group Buying
            </a>
        @else
            {{-- Tombol Non-aktif / Disabled jika Waktu Berakhir atau Stok Habis --}}
            <button disabled 
                    class="w-full py-2.5 px-4 bg-gray-200 text-gray-500 rounded-lg text-sm font-medium cursor-not-allowed">
                @if ($stok <= 0)
                    Stok Habis
                @else
                    Sesi Berakhir
                @endif
            </button>
        @endif
    </div>
</div>