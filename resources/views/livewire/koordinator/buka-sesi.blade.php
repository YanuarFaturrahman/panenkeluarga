<div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">
    {{-- Notifikasi Sukses --}}
    @if (session()->has('sukses'))
        <div class="p-4 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded-xl text-sm font-medium">
            {{ session('sukses') }}
        </div>
    @endif

    {{-- Form Buka Sesi Baru (2 Kolom dengan Border Tegas) --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <h2 class="font-serif text-xl font-bold text-gray-800 mb-6 pb-3 border-b border-gray-100">
            Buka Sesi Group Buying Baru
        </h2>
        
        <form wire:submit.prevent="simpan" class="space-y-5">
            {{-- Grid 2 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                {{-- 1. Pilih Produk --}}
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Pilih Produk</label>
                    <select wire:model.live="produk_id" class="w-full rounded-xl border border-gray-300 focus:border-[#3b5e4c] focus:ring-[#3b5e4c] text-sm py-2.5 px-3 bg-gray-50/30">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($this->produkList as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_komoditas ?? $item->nama }}</option>
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
                            <option value="{{ $titik->id }}">{{ $titik->nama_lokasi ?? $titik->alamat }}</option>
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

                {{-- 4. Harga Satuan --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Harga Satuan (Rp)</label>
                    <input type="number" wire:model="harga_satuan" min="0" placeholder="Masukkan harga" class="w-full rounded-xl border border-gray-300 focus:border-[#3b5e4c] focus:ring-[#3b5e4c] text-sm py-2.5 px-3 bg-gray-50/30">
                    @error('harga_satuan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- 5. Tenggat Waktu (Span 2 Kolom) --}}
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

    {{-- Daftar Sesi Group Buying --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <h3 class="font-serif text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">
            Daftar Sesi Group Buying
        </h3>

        <div class="space-y-3">
            @forelse ($sesi ?? [] as $s)
                @php
                    $target = $s->kuota_minimum ?? 10;
                    $terkumpul = $s->jumlah_terkumpul ?? $s->peserta_count ?? 0;
                    $namaPetani = $s->produk?->petani?->name ?? 'Pak Slamet';
                    $namaProduk = $s->produk?->nama_komoditas ?? $s->produk?->nama ?? 'Komoditas Tanpa Nama';
                    $sudahKadaluarsa = $s->tenggat_waktu && $s->tenggat_waktu->isPast();
                    $kuotaTercapai = $terkumpul >= $target;
                @endphp

                <div class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-white rounded-xl border border-gray-200 hover:border-gray-300 transition gap-4">
                    <div class="min-w-[200px]">
                        <h4 class="font-bold text-gray-900 text-base">{{ $namaProduk }}</h4>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $namaPetani }}</p>
                    </div>

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

                    <div class="flex items-center gap-3 self-end md:self-auto">
                        @if ($sudahKadaluarsa || $kuotaTercapai)
                            @if ($kuotaTercapai)
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-lg text-xs whitespace-nowrap">
                                    Kuota Terpenuhi
                                </span>
                                <a href="{{ route('koordinator.sesi.index') }}" class="px-4 py-1.5 bg-[#3b5e4c] hover:bg-[#2d493b] text-white text-xs font-semibold rounded-lg transition">
                                    Proses Pesanan
                                </a>
                            @else
                                <span class="px-3 py-1 bg-red-50 text-red-600 font-semibold rounded-lg text-xs whitespace-nowrap">
                                    Berakhir
                                </span>
                                <a href="{{ route('koordinator.sesi.index') }}" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                                    Kelola
                                </a>
                            @endif
                        @else
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 font-semibold rounded-lg text-xs whitespace-nowrap">
                                {{ $s->sisa_waktu }}
                            </span>
                            <a href="{{ route('koordinator.sesi.index') }}" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                                Kelola
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-gray-400 text-sm">
                    Belum ada sesi group buying yang dibuka.
                </div>
            @endforelse
        </div>
    </div>
</div>