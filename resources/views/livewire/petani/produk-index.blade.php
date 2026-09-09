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
            <div wire:key="produk-card-{{ $p->id }}" class="pk-card flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between mb-1">
                        <p class="font-semibold text-base">{{ $p->nama_komoditas }}</p>
                        <span class="text-xs px-2 py-0.5 rounded font-medium {{ $p->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mb-2">{{ ucfirst($p->kategori) }} · {{ $p->estimasi_stok }} {{ $p->satuan }}</p>
                    <p class="font-bold text-emerald-700">Rp{{ number_format($p->harga, 0, ',', '.') }} / {{ $p->satuan }}</p>
                </div>

                <div class="mt-4 flex items-center justify-between pt-2 border-t border-gray-100">
                    <button 
                        wire:click="toggleStatus({{ $p->id }})"
                        wire:loading.attr="disabled"
                        title="Klik untuk mengubah status produk"
                        class="pk-badge cursor-pointer transition hover:opacity-80 {{ $p->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}"
                    >
                        Ubah ke {{ $p->status === 'aktif' ? 'Draft' : 'Aktif' }}
                    </button>

                    <button 
                        type="button"
                        wire:click="hapus({{ $p->id }})"
                        wire:loading.attr="disabled"
                        onclick="return confirm('Yakin ingin menghapus produk ini?') || event.stopImmediatePropagation()"
                        class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-10 bg-white rounded-xl border border-dashed border-gray-300">
                <p class="text-gray-500 text-sm">Belum ada produk yang ditambahkan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $produk->links() }}</div>
</div>