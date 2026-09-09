<div class="min-h-screen w-full bg-[#f1f3e9] grid grid-cols-1 lg:grid-cols-12">
    <!-- Banner Kiri (5 Kolom) -->
    <div class="lg:col-span-5 bg-[#214332] text-white p-8 lg:p-16 flex flex-col justify-between relative overflow-hidden min-h-[300px] lg:min-h-screen">
        <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="flex items-center space-x-3 z-10">
            <span class="text-xl">🌱</span>
            <span class="text-xl font-serif font-bold tracking-tight">PanenKeluarga</span>
        </div>

        <div class="my-auto py-12 z-10">
            <h1 class="text-3xl lg:text-4xl font-serif font-semibold leading-tight mb-4">
                Satu akun, tiga peran yang saling terhubung.
            </h1>
            <p class="text-emerald-100/80 text-sm leading-relaxed">
                Pilih peran sesuai posisi Anda — platform akan menyesuaikan dashboard dan fitur secara otomatis.
            </p>
        </div>

        <div class="text-xs text-emerald-200/50 z-10">
            © 2026 PanenKeluarga — Marketplace Agrotech Mikro
        </div>
    </div>

    <!-- Form Kanan (7 Kolom) -->
    <div class="lg:col-span-7 bg-[#f1f3e9] p-8 lg:p-16 flex items-center justify-center">
        <div class="w-full max-w-xl space-y-6">
            <div>
                <h2 class="text-3xl font-serif font-bold text-[#214332]">Buat akun baru</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" wire:navigate class="font-bold text-[#214332] hover:underline">Masuk di sini</a>
                </p>
            </div>

            <!-- Pesan Error Global jika ada kesalahan validasi -->
            @if ($errors->any())
                <div class="p-3 bg-red-100 border border-red-300 text-red-700 rounded-xl text-xs font-medium">
                    Mohon periksa kembali form di bawah, ada data yang belum lengkap atau sesuai.
                </div>
            @endif

            <form wire:submit="register" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-[#214332] mb-2">Saya mendaftar sebagai</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" wire:click="$set('peran', 'konsumen')" class="py-2.5 px-2 text-xs font-medium rounded-full border transition flex items-center justify-center space-x-1.5 {{ $peran === 'konsumen' ? 'bg-[#214332] text-white border-[#214332]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                            <span>👥</span> <span>Konsumen</span>
                        </button>
                        <button type="button" wire:click="$set('peran', 'petani')" class="py-2.5 px-2 text-xs font-medium rounded-full border transition flex items-center justify-center space-x-1.5 {{ $peran === 'petani' ? 'bg-[#214332] text-white border-[#214332]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                            <span>🌱</span> <span>Petani</span>
                        </button>
                        <button type="button" wire:click="$set('peran', 'koordinator')" class="py-2.5 px-2 text-xs font-medium rounded-full border transition flex items-center justify-center space-x-1.5 {{ $peran === 'koordinator' ? 'bg-[#214332] text-white border-[#214332]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
                            <span>📍</span> <span>Koordinator</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#214332] mb-1">Nama Lengkap</label>
                        <input wire:model="name" type="text" placeholder="Rina Nurhaliza" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm focus:ring-2 focus:ring-[#214332] focus:border-[#214332] outline-none shadow-sm">
                        @error('name') <span class="mt-1 block text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#214332] mb-1">Nomor HP atau Email</label>
                        <input wire:model="identitas" type="text" placeholder="koordinator@panenkeluarga.id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm focus:ring-2 focus:ring-[#214332] focus:border-[#214332] outline-none shadow-sm">
                        @error('identitas') <span class="mt-1 block text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Input Dropdown Wilayah Domisili (Kecamatan Cibogo, Kab. Subang) -->
                <div>
                    <label class="block text-xs font-bold text-[#214332] mb-1">Wilayah Domisili (Kec. Cibogo, Kab. Subang)</label>
                    <select wire:model="wilayah_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm focus:ring-2 focus:ring-[#214332] focus:border-[#214332] outline-none shadow-sm text-gray-700">
                        <option value="">-- Pilih Wilayah Domisili --</option>
                        
                        @if(isset($this->wilayahList) && count($this->wilayahList) > 0)
                            @foreach ($this->wilayahList as $wilayah)
                                <option value="{{ $wilayah->id }}">RT {{ $wilayah->nama_rt }} / RW {{ $wilayah->nama_rw }} — Desa {{ $wilayah->kelurahan ?? $wilayah->desa }}</option>
                            @endforeach
                        @else
                            <!-- DATA DUMMY KECAMATAN CIBOGO, SUBANG -->
                            <optgroup label="Desa Cibogo">
                                <option value="1">RT 01 / RW 01 — Desa Cibogo, Kec. Cibogo</option>
                                <option value="2">RT 02 / RW 01 — Desa Cibogo, Kec. Cibogo</option>
                                <option value="3">RT 03 / RW 02 — Desa Cibogo, Kec. Cibogo</option>
                                <option value="4">RT 04 / RW 02 — Desa Cibogo, Kec. Cibogo</option>
                            </optgroup>
                            <optgroup label="Desa Padaasih">
                                <option value="5">RT 01 / RW 01 — Desa Padaasih, Kec. Cibogo</option>
                                <option value="6">RT 02 / RW 01 — Desa Padaasih, Kec. Cibogo</option>
                                <option value="7">RT 05 / RW 02 — Desa Padaasih, Kec. Cibogo</option>
                            </optgroup>
                            <optgroup label="Desa Cisaga">
                                <option value="8">RT 01 / RW 01 — Desa Cisaga, Kec. Cibogo</option>
                                <option value="9">RT 03 / RW 02 — Desa Cisaga, Kec. Cibogo</option>
                            </optgroup>
                            <optgroup label="Desa Majasari">
                                <option value="10">RT 02 / RW 01 — Desa Majasari, Kec. Cibogo</option>
                                <option value="11">RT 04 / RW 02 — Desa Majasari, Kec. Cibogo</option>
                            </optgroup>
                            <optgroup label="Desa Sadawarna">
                                <option value="12">RT 01 / RW 01 — Desa Sadawarna, Kec. Cibogo</option>
                                <option value="13">RT 02 / RW 02 — Desa Sadawarna, Kec. Cibogo</option>
                            </optgroup>
                        @endif
                    </select>
                    @error('wilayah_id') <span class="mt-1 block text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                </div>

                <!-- Input Upload Berkas Dokumen khusus Petani & Koordinator -->
                @if(in_array($peran, ['petani', 'koordinator']))
                    <div class="p-3.5 bg-white rounded-xl border border-dashed border-[#214332]/30 space-y-1">
                        <label class="block text-xs font-bold text-[#214332]">
                            Upload Dokumen Verifikasi 
                            <span class="text-red-500">*</span>
                            <span class="font-normal text-gray-500">
                                ({{ $peran === 'petani' ? 'KTP + Foto Lahan' : 'KTP + Surat RT' }})
                            </span>
                        </label>
                        <input type="file" wire:model="dokumen" accept=".pdf,.png,.jpg,.jpeg" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#214332]/10 file:text-[#214332] hover:file:bg-[#214332]/20">
                        
                        <div wire:loading wire:target="dokumen" class="text-xs text-amber-600 font-medium">
                            Mengunggah berkas...
                        </div>

                        @error('dokumen') <span class="block text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-bold text-[#214332] mb-1">Kata Sandi</label>
                    <input wire:model="password" type="password" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm focus:ring-2 focus:ring-[#214332] focus:border-[#214332] outline-none shadow-sm">
                    @error('password') <span class="mt-1 block text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-start space-x-2 pt-1">
                    <input type="checkbox" id="terms" class="mt-0.5 rounded border-gray-300 text-[#214332] focus:ring-[#214332]">
                    <label for="terms" class="text-xs text-gray-500 leading-tight cursor-pointer">
                        Saya menyetujui Syarat Layanan dan Kebijakan Privasi PanenKeluarga
                    </label>
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full py-3.5 bg-[#538253] hover:bg-[#436a43] disabled:opacity-50 text-white font-medium rounded-xl shadow-sm transition text-sm flex items-center justify-center space-x-2">
                    <span wire:loading.remove wire:target="register">Buat Akun</span>
                    <span wire:loading wire:target="register" class="flex items-center space-x-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memproses Pendaftaran...</span>
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>