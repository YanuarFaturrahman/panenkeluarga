<div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">
    {{-- Notifikasi Sukses --}}
    @if (session()->has('sukses'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-xl text-sm font-medium">
            {{ session('sukses') }}
        </div>
    @endif

    {{-- Form Buka Sesi Baru --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between mb-6 pb-3 border-b border-gray-100">
            <h2 class="font-serif text-xl font-bold text-gray-800">
                Buka Sesi Group Buying Baru
            </h2>
            @if (Route::has('koordinator.sesi.index'))
                <a href="{{ route('koordinator.sesi.index') }}" wire:navigate class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1 font-medium">
                    &larr; Kembali ke Daftar Sesi
                </a>
            @endif
        </div>
        
        <form wire:submit.prevent="simpan" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                {{-- 1. Pilih Produk --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Pilih Produk</label>
                    <select wire:model.live="produk_id" class="w-full rounded-xl border border-gray-300 focus:border-[#3b5e4c] focus:ring-[#3b5e4c] text-sm py-2.5 px-3 bg-gray-50/30">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($this->produkList as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->nama_komoditas ?? $item->nama }}, Petani: {{ $item->petani?->name ?? 'Anonim' }}
                            </option>
                        @endforeach
                    </select>
                    @error('produk_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- 2. Pilih Titik Pengambilan --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Titik Pengambilan</label>
                    <select wire:model="titik_pengambilan_id" class="w-full rounded-xl border border-gray-300 focus:border-[#3b5e4c] focus:ring-[#3b5e4c] text-sm py-2.5 px-3 bg-gray-50/30">
                        <option value="">-- Pilih Titik Pengambilan --</option>
                        @foreach ($this->titikList as $titik)
                            <option value="{{ $titik->id }}">{{ $titik->nama_lokasi ?? $titik->nama_titik ?? $titik->alamat }}</option>
                        @endforeach
                    </select>
                    @error('titik_pengambilan_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- 3. Kuota Minimum --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Kuota Minimum (Keluarga)</label>
                    <input type="number" wire:model="kuota_minimum" min="1" placeholder="10" class="w-full rounded-xl border border-gray-300 focus:border-[#3b5e4c] focus:ring-[#3b5e4c] text-sm py-2.5 px-3 bg-gray-50/30">
                    @error('kuota_minimum') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- 4. Harga Satuan (Readonly / Terkunci) --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Harga Satuan (Rp) <span class="text-[10px] text-gray-400 font-normal lowercase">(otomatis dari petani)</span></label>
                    <input type="number" wire:model="harga_satuan" readonly disabled placeholder="Pilih produk dahulu" class="w-full rounded-xl border border-gray-200 bg-gray-100 text-gray-500 text-sm py-2.5 px-3 cursor-not-allowed select-none">
                    @error('harga_satuan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- 5. Tenggat Waktu --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Tenggat Waktu</label>
                    <input type="datetime-local" wire:model="tenggat_waktu" class="w-full rounded-xl border border-gray-300 focus:border-[#3b5e4c] focus:ring-[#3b5e4c] text-sm py-2.5 px-3 bg-gray-50/30">
                    @error('tenggat_waktu') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

            </div>

            {{-- Tombol Submit --}}
            <div class="pt-3 flex justify-end">
                <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-[#3b5e4c] hover:bg-[#2d493b] text-white font-bold rounded-xl text-sm transition shadow-sm">
                    + Buka Sesi Baru
                </button>
            </div>
        </form>
    </div>
</div>