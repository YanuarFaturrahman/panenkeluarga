<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between h-full">
    <div>
        {{-- Area Foto Produk dengan Kartu / Image Container --}}
        <div class="h-36 bg-emerald-800 flex items-center justify-center text-white text-4xl relative overflow-hidden">
            @php
                // Ambil foto dari produk
                $foto = $sesi->produk->foto ?? $sesi->foto ?? null;
                $kategori = strtolower($sesi->produk->kategori ?? $sesi->kategori ?? '');
            @endphp

            @if ($foto && (Storage::disk('public')->exists($foto) || file_exists(public_path('storage/' . $foto))))
                <img 
                    src="{{ asset('storage/' . $foto) }}" 
                    alt="Foto Produk" 
                    class="w-full h-full object-cover"
                >
            @else
                {{-- Fallback Emoji jika gambar tidak ditemukan --}}
                @if ($kategori === 'buah')
                    🍅
                @elseif ($kategori === 'protein')
                    🥚
                @else
                    🥬
                @endif
            @endif

            {{-- Sisa Waktu Badge --}}
            @if (isset($sesi->sisa_waktu) || isset($sesi->tanggal_selesai))
                <span class="absolute top-3 right-3 bg-red-100 text-red-600 text-xs px-2.5 py-1 rounded-full font-medium shadow-sm">
                    {{ $sesi->sisa_waktu ?? \Carbon\Carbon::parse($sesi->tanggal_selesai)->diffForHumans() }}
                </span>
            @endif
        </div>

        {{-- Detail Produk --}}
        <div class="p-4 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                    {{ $sesi->produk->kategori ?? $sesi->kategori ?? 'Sayur' }}
                </span>
                <span class="text-xs text-gray-400">
                    {{-- Pengecekan kolom stok lengkap --}}
                    Stok: {{ $sesi->produk->estimasi_stok_panen ?? $sesi->produk->stok ?? $sesi->estimasi_stok_panen ?? $sesi->stok ?? 0 }} 
                    {{ $sesi->produk->satuan ?? $sesi->satuan ?? 'satuan' }}
                </span>
            </div>

            <h3 class="font-bold text-gray-800 text-base line-clamp-1">
                {{ $sesi->produk->nama_komoditas ?? $sesi->nama_komoditas ?? 'Komoditas' }}
            </h3>

            <p class="text-xs text-gray-500 line-clamp-1">
                {{ $sesi->produk->petani->name ?? $sesi->produk->user->name ?? $sesi->petani->name ?? $sesi->user->name ?? $sesi->nama_petani ?? 'Petani Mitra' }}
            </p>

            <div class="pt-2 flex items-baseline gap-1">
                <span class="font-bold text-emerald-700 text-lg">
                    Rp{{ number_format(
                        $sesi->harga_per_satuan 
                        ?? $sesi->harga 
                        ?? $sesi->produk->harga_per_satuan 
                        ?? $sesi->produk->harga 
                        ?? 0, 
                        0, ',', '.'
                    ) }}
                </span>
                <span class="text-xs text-gray-500">
                    / {{ $sesi->produk->satuan ?? $sesi->satuan ?? 'satuan' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="p-4 pt-0">
        <button class="w-full py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-medium transition-colors">
            Gabung Group Buying
        </button>
    </div>
</div>