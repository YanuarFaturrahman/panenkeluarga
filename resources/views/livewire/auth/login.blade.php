<div class="min-h-screen w-full bg-[#f1f3e9] grid grid-cols-1 lg:grid-cols-12">
    <!-- Banner Kiri (5 Kolom) -->
    <div class="lg:col-span-5 bg-[#214332] text-white p-8 lg:p-16 flex flex-col justify-between relative overflow-hidden min-h-[300px] lg:min-h-screen">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="flex items-center space-x-3 z-10">
            <span class="text-xl">🌱</span>
            <span class="text-xl font-serif font-bold tracking-tight">PanenKeluarga</span>
        </div>

        <div class="my-auto py-12 z-10">
            <h1 class="text-3xl lg:text-4xl font-serif font-semibold leading-tight mb-4">
                Belanja bersama tetangga, langsung dari petani.
            </h1>
            <p class="text-emerald-100/80 text-sm leading-relaxed">
                Setiap sesi group buying memangkas rantai tengkulak, menaikkan harga jual petani, dan menyalurkan sebagian surplus untuk gizi anak di Desa/Kelurahan Anda.
            </p>
        </div>

        <!-- Statistik Dinamis -->
        <div class="grid grid-cols-3 gap-4 pt-6 border-t border-emerald-800/60 z-10">
            <div>
                <p class="text-xl font-bold font-serif">{{ number_format($totalKeluarga, 0, ',', '.') }}{{ $totalKeluarga > 0 ? '+' : '' }}</p>
                <p class="text-[11px] text-emerald-200/70">Keluarga bergabung</p>
            </div>
            <div>
                <p class="text-xl font-bold font-serif">{{ number_format($totalPetani, 0, ',', '.') }}</p>
                <p class="text-[11px] text-emerald-200/70">Petani mitra</p>
            </div>
            <div>
                <p class="text-xl font-bold font-serif">{{ number_format($totalDesa, 0, ',', '.') }}</p>
                <p class="text-[11px] text-emerald-200/70">Desa/Kelurahan aktif</p>
            </div>
        </div>
    </div>

    <!-- Form Kanan (7 Kolom) -->
    <div class="lg:col-span-7 bg-[#f1f3e9] p-8 lg:p-16 flex items-center justify-center">
        <div class="w-full max-w-xl space-y-6">
            <div>
                <h2 class="text-3xl font-serif font-bold text-[#214332]">Masuk ke akun Anda</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Belum punya akun?
                    <a href="{{ route('register') }}" wire:navigate class="font-bold text-[#214332] hover:underline">Daftar sekarang</a>
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="authenticate" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-[#214332] mb-1">Nomor HP atau Email</label>
                    <input wire:model="login" type="text" required autofocus placeholder="0812-xxxx-xxxx atau email@domain.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm focus:ring-2 focus:ring-[#214332] focus:border-[#214332] outline-none shadow-sm transition">
                    @error('login') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#214332] mb-1">Kata Sandi</label>
                    <input wire:model="password" type="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm focus:ring-2 focus:ring-[#214332] focus:border-[#214332] outline-none shadow-sm transition">
                    @error('password') <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 text-gray-600 cursor-pointer">
                        <input wire:model="remember" type="checkbox" class="rounded border-gray-300 text-[#214332] focus:ring-[#214332]">
                        <span>Ingat saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate class="font-semibold text-[#538253] hover:underline">Lupa kata sandi?</a>
                    @endif
                </div>

                <button type="submit" class="w-full py-3.5 bg-[#538253] hover:bg-[#436a43] text-white font-medium rounded-xl shadow-sm transition flex items-center justify-center space-x-2 text-sm">
                    <span>Masuk</span>
                    <span>→</span>
                </button>
            </form>

            <div class="pt-4 grid grid-cols-2 gap-3">
                <a href="{{ route('login') }}" class="py-2.5 px-3 bg-white border border-gray-200 rounded-full text-center text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Masuk sebagai Petani
                </a>
                <a href="{{ route('login') }}" class="py-2.5 px-3 bg-white border border-gray-200 rounded-full text-center text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Masuk sebagai Koordinator
                </a>
            </div>
        </div>
    </div>
</div>