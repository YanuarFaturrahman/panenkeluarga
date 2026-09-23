<div>
    <x-slot:header>Tambah Produk Baru</x-slot:header>
    <x-slot:subheader>Lengkapi detail hasil panen Anda</x-slot:subheader>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 pk-card">
            <p class="font-semibold text-lg mb-4">Detail Produk</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Nama Komoditas</label>
                    <input type="text" wire:model.live.debounce.150ms="nama_komoditas" class="pk-input" placeholder="Contoh: Beras Organik / Telur Ayam Kampung">
                    @error('nama_komoditas') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kategori</label>
                        <select wire:model.live="kategori" class="pk-input">
                            <option value="sayur">Sayur</option>
                            <option value="buah">Buah</option>
                            <option value="protein">Protein</option>
                            <option value="karbohidrat">Karbohidrat</option>
                        </select>
                        @error('kategori') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Satuan</label>
                        <input type="text" wire:model.live.debounce.150ms="satuan" class="pk-input" placeholder="butir / kg / ikat">
                        @error('satuan') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Harga per Satuan</label>
                        <input type="number" wire:model.live.debounce.150ms="harga" class="pk-input" placeholder="2000">
                        @error('harga') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Estimasi Stok Panen</label>
                        <input type="number" wire:model.live.debounce.150ms="estimasi_stok" class="pk-input" placeholder="100">
                        @error('estimasi_stok') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Estimasi Tanggal Panen</label>
                    <input type="date" wire:model.live="estimasi_tanggal_panen" class="pk-input">
                    @error('estimasi_tanggal_panen') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                    <textarea wire:model.live.debounce.150ms="deskripsi" rows="3" class="pk-input" placeholder="Jelaskan detail komoditas..."></textarea>
                    @error('deskripsi') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Foto Produk</label>
                    <div class="border-2 border-dashed border-pk-green/40 bg-pk-green/5 rounded-xl p-8 text-center">
                        <input type="file" wire:model="foto" class="mx-auto">
                        <p class="text-sm text-gray-500 mt-1">PNG/JPG, maks. 5MB</p>
                        @error('foto') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button 
                        type="button" 
                        wire:click="simpan('draft')" 
                        wire:loading.attr="disabled"
                        class="pk-btn-secondary cursor-pointer"
                    >
                        <span wire:loading.remove wire:target="simpan('draft')">Simpan Draf</span>
                        <span wire:loading wire:target="simpan('draft')">Menyimpan...</span>
                    </button>
                    
                    <button 
                        type="button" 
                        wire:click="simpan('aktif')" 
                        wire:loading.attr="disabled"
                        class="pk-btn-primary cursor-pointer"
                    >
                        <span wire:loading.remove wire:target="simpan('aktif')">Terbitkan Produk</span>
                        <span wire:loading wire:target="simpan('aktif')">Menerbitkan...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Kartu Pratinjau Produk Realtime --}}
        <div class="pk-card h-fit">
            <p class="font-semibold mb-3">Pratinjau Kartu Produk</p>
            <div class="rounded-xl overflow-hidden border border-gray-100 shadow-sm bg-white">
                <div class="h-32 bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white text-4xl relative">
                    @php
                        $hasValidPhoto = false;
                        if ($foto && method_exists($foto, 'temporaryUrl')) {
                            try {
                                $photoUrl = $foto->temporaryUrl();
                                $hasValidPhoto = true;
                            } catch (\Throwable $e) {
                                $hasValidPhoto = false;
                            }
                        }
                    @endphp

                    @if ($hasValidPhoto)
                        <img src="{{ $photoUrl }}" class="w-full h-full object-cover">
                    @else
                        @if(($kategori ?? '') === 'buah')
                            🍅
                        @elseif(($kategori ?? '') === 'protein')
                            🥚
                        @elseif(($kategori ?? '') === 'karbohidrat')
                            🌾
                        @else
                            🥬
                        @endif
                    @endif
                </div>
                <div class="p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs px-2 py-0.5 rounded font-medium bg-emerald-100 text-emerald-700 capitalize">
                            {{ $kategori ?: 'sayur' }}
                        </span>
                        <span class="text-xs text-gray-400">
                            Stok: {{ $estimasi_stok !== '' && $estimasi_stok !== null ? $estimasi_stok : '0' }} {{ $satuan ?: 'satuan' }}
                        </span>
                    </div>
                    <p class="font-semibold text-gray-800 text-base">
                        {{ $nama_komoditas ?: 'Nama Komoditas' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ auth()->user()->name ?? 'Petani' }}
                    </p>
                    <p class="font-bold text-emerald-700 text-lg pt-1 border-t border-gray-50">
                        Rp{{ is_numeric($harga) ? number_format((float)$harga, 0, ',', '.') : '0' }} 
                        <span class="text-xs text-gray-500 font-normal">/ {{ $satuan ?: 'satuan' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>