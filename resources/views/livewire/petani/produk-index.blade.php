<div>
    <x-slot:header>Produk Saya</x-slot:header>
    <x-slot:subheader>Kelola hasil panen yang Anda tawarkan</x-slot:subheader>

    {{-- Alert Notifikasi Sukses --}}
    @if (session()->has('sukses'))
        @php
            $pesan = session('sukses');
            $isDraft = str_contains(strtolower($pesan), 'draf') || str_contains(strtolower($pesan), 'draft');
            $isHapus = str_contains(strtolower($pesan), 'hapus');
        @endphp
        <div 
            wire:key="alert-sukses-{{ microtime() }}"
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3500)" 
            x-show="show"
            x-transition.duration.500ms
            class="mb-6 p-4 rounded-xl border flex items-center gap-3 shadow-sm transition {{ $isHapus ? 'bg-red-50 border-red-200 text-red-800' : ($isDraft ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800') }}"
        >
            <div class="flex-shrink-0 text-xl">
                {{ $isHapus ? '🗑️' : ($isDraft ? '📝' : '🚀') }}
            </div>
            <div class="text-sm font-semibold">
                {{ $pesan }}
            </div>
        </div>
    @endif

    {{-- Alert Notifikasi Gagal --}}
    @if (session()->has('gagal'))
        <div 
            wire:key="alert-gagal-{{ microtime() }}"
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 5000)" 
            x-show="show"
            x-transition.duration.500ms
            class="mb-6 p-4 rounded-xl border flex items-center gap-3 shadow-sm bg-red-50 border-red-200 text-red-800"
        >
            <div class="flex-shrink-0 text-xl">⚠️</div>
            <div class="text-sm font-semibold">
                {{ session('gagal') }}
            </div>
        </div>
    @endif

    <a href="{{ route('petani.produk.tambah') }}" wire:navigate class="pk-btn-primary mb-6 inline-flex">+ Tambah Produk</a>

    <div class="grid md:grid-cols-3 gap-5">
        @forelse ($produk as $p)
            <div wire:key="produk-card-{{ $p->id }}" class="pk-card flex flex-col justify-between overflow-hidden p-0 border border-gray-100 bg-white rounded-xl shadow-sm">
                
                {{-- Bagian Foto Produk --}}
                <div class="relative w-full h-44 bg-gray-100 overflow-hidden">
                    @if ($p->foto)
                        <img src="{{ asset('storage/' . $p->foto) }}" alt="{{ $p->nama_komoditas }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                            <svg class="w-10 h-10 mb-1 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs">Tanpa Foto</span>
                        </div>
                    @endif

                    <span class="absolute top-3 right-3 text-xs px-2.5 py-1 rounded-full font-semibold shadow-sm {{ $p->status === 'aktif' ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </div>

                {{-- Informasi Produk --}}
                <div class="p-4 flex-grow flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base mb-1">{{ $p->nama_komoditas }}</h3>
                        <p class="text-xs text-gray-500 mb-2">{{ ucfirst($p->kategori) }} · {{ $p->estimasi_stok }} {{ $p->satuan }}</p>
                        <p class="font-bold text-emerald-700 text-lg">Rp{{ number_format($p->harga, 0, ',', '.') }} <span class="text-xs text-gray-500 font-normal">/ {{ $p->satuan }}</span></p>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-4 flex items-center justify-between pt-3 border-t border-gray-100">
                        <button 
                            wire:click="toggleStatus({{ $p->id }})"
                            wire:loading.attr="disabled"
                            title="Klik untuk mengubah status produk"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $p->status === 'aktif' ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }}"
                        >
                            Ubah ke {{ $p->status === 'aktif' ? 'Draft' : 'Aktif' }}
                        </button>

                        <button 
                            type="button"
                            wire:click="hapus({{ $p->id }})"
                            wire:loading.attr="disabled"
                            onclick="return confirm('Yakin ingin menghapus produk ini?') || event.stopImmediatePropagation()"
                            class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer px-2 py-1"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-500 text-sm font-medium">Belum ada produk yang ditambahkan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $produk->links() }}</div>
</div>