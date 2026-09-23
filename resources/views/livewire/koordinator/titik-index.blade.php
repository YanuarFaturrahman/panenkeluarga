<div class="space-y-6">
    {{-- Slot Header disamakan styling-nya dengan menu lain --}}
    <x-slot:header>
        <h2 class="font-serif text-2xl font-bold text-gray-800 leading-tight">
            Titik Pengambilan
        </h2>
    </x-slot:header>

    {{-- Slot Subheader --}}
    <x-slot:subheader>
        Kelola lokasi pengambilan pesanan beserta jam operasional di wilayah binaan Anda
    </x-slot:subheader>

    {{-- Tombol Aksi di Area Konten Utama --}}
    <div class="flex justify-end">
        <button wire:click="$toggle('formTerbuka')" 
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition duration-200 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $formTerbuka ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4' }}"></path>
            </svg>
            <span>{{ $formTerbuka ? 'Batal' : 'Tambah Titik Pengambilan' }}</span>
        </button>
    </div>

    {{-- Form Tambah Titik Pengambilan --}}
    @if ($formTerbuka)
        <div class="bg-white rounded-2xl p-6 border border-emerald-100 shadow-md transition-all">
            <div class="flex items-center gap-2 mb-5 pb-3 border-b border-gray-100">
                <div class="p-2 bg-emerald-50 text-emerald-700 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Tambah Lokasi Baru</h3>
                    <p class="text-xs text-gray-500">Lengkapi detail lokasi agar memudahkan konsumen saat mengambil pesanan</p>
                </div>
            </div>

            <form wire:submit.prevent="simpan" class="space-y-4 max-w-2xl">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Nama Lokasi / Tempat</label>
                    <input type="text" 
                           wire:model="nama_lokasi" 
                           placeholder="Contoh: Pos Ronda RT 05 / Kedai Ibu Ani" 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 text-sm text-gray-800 placeholder-gray-400 outline-none transition">
                    @error('nama_lokasi') 
                        <p class="text-xs text-red-600 font-medium mt-1 flex items-center gap-1">
                            ⚠️ <span>{{ $message }}</span>
                        </p> 
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                    <textarea wire:model="alamat" 
                              rows="2" 
                              placeholder="Masukkan nama jalan, nomor rumah, RT/RW, dan patokan lokasi" 
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 text-sm text-gray-800 placeholder-gray-400 outline-none transition resize-none"></textarea>
                    @error('alamat') 
                        <p class="text-xs text-red-600 font-medium mt-1 flex items-center gap-1">
                            ⚠️ <span>{{ $message }}</span>
                        </p> 
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Jam Operasional Pengambilan</label>
                    <input type="text" 
                           wire:model="jam_operasional" 
                           placeholder="Contoh: 16.00 - 19.00 WIB atau Setiap Sabtu 08.00 - 12.00 WIB" 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 text-sm text-gray-800 placeholder-gray-400 outline-none transition">
                    @error('jam_operasional') 
                        <p class="text-xs text-red-600 font-medium mt-1 flex items-center gap-1">
                            ⚠️ <span>{{ $message }}</span>
                        </p> 
                    @enderror
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <button type="submit" 
                            class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition duration-200 cursor-pointer">
                        Simpan Titik Pengambilan
                    </button>
                    <button type="button" 
                            wire:click="$set('formTerbuka', false)" 
                            class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition duration-200 cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Daftar Kartu Titik Pengambilan --}}
    @if ($titik->isEmpty())
        <div class="bg-white rounded-2xl p-10 text-center border border-gray-100 shadow-sm">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="text-base font-semibold text-gray-800">Belum Ada Titik Pengambilan</h3>
            <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">Klik tombol "Tambah Titik Pengambilan" di atas untuk menambahkan lokasi tempat penyerahan hasil panen ke konsumen.</p>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($titik as $t)
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="p-2 bg-emerald-100 text-emerald-800 rounded-xl group-hover:bg-emerald-700 group-hover:text-white transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-800 text-base leading-snug">{{ $t->nama_lokasi }}</h3>
                            </div>
                        </div>

                        <div class="space-y-2 pt-1">
                            <div class="flex items-start gap-2 text-xs text-gray-600">
                                <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span class="leading-relaxed">{{ $t->alamat }}</span>
                            </div>

                            @if($t->jam_operasional)
                                <div class="flex items-center gap-2 text-xs text-emerald-700 bg-emerald-50 px-2.5 py-1.5 rounded-lg w-fit font-medium">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $t->jam_operasional }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>