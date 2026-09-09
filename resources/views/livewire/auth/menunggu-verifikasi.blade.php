<div class="min-h-screen w-full bg-[#f1f3e9] flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Overlay Background -->
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-10"></div>

    <!-- Pop-Up Modal Card -->
    <div class="relative z-20 bg-white rounded-2xl shadow-xl border border-gray-100 max-w-md w-full p-6 text-center space-y-5 animate-in fade-in zoom-in duration-200">
        
        <!-- Icon Badge -->
        <div class="w-16 h-16 bg-amber-50 border border-amber-200 text-amber-600 rounded-full flex items-center justify-center mx-auto text-3xl shadow-sm">
            ⏳
        </div>

        <!-- Text Header -->
        <div class="space-y-2">
            <h2 class="text-2xl font-serif font-bold text-[#214332]">
                Akun Menunggu Verifikasi
            </h2>
            <p class="text-xs text-gray-600 leading-relaxed">
                Halo <span class="font-bold text-gray-800">{{ auth()->user()->name }}</span>, pendaftaran Anda sebagai 
                <span class="font-semibold text-[#214332] capitalize">{{ auth()->user()->peran }}</span> sedang ditinjau oleh Admin PanenKeluarga.
            </p>
        </div>

        <!-- Info Box -->
        <div class="bg-[#f1f3e9] p-4 rounded-xl border border-[#214332]/10 text-left text-xs space-y-2 text-gray-600">
            <div class="flex items-start gap-2">
                <span>📌</span>
                <span>Proses verifikasi dokumen membutuhkan waktu maksimal 1x24 jam.</span>
            </div>
            <div class="flex items-start gap-2">
                <span>📧</span>
                <span>Akses fitur dashboard akan terbuka secara otomatis setelah disetujui.</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-2 flex flex-col gap-2">
            <button onclick="window.location.reload()" class="w-full py-3 bg-[#214332] hover:bg-[#183225] text-white text-xs font-semibold rounded-xl shadow-sm transition">
                🔄 Cek Status Verifikasi
            </button>

            <button wire:click="logout" class="w-full py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs font-medium rounded-xl transition">
                Keluar / Kembali ke Login
            </button>
        </div>
    </div>
</div>