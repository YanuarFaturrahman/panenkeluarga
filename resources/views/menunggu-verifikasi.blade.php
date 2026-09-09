<x-layouts.guest>
    <div class="min-h-screen flex items-center justify-center bg-pk-cream px-6">
        <div class="max-w-md text-center bg-white rounded-2xl shadow p-10">
            <div class="w-14 h-14 mx-auto rounded-full bg-pk-orange/20 flex items-center justify-center text-2xl">⏳</div>
            <h1 class="font-serif text-2xl font-bold text-pk-dark mt-4">Akun Anda sedang diverifikasi</h1>
            <p class="text-gray-600 mt-2">Tim Admin PanenKeluarga akan meninjau data {{ auth()->user()->peran }} Anda dalam 1x24 jam. Anda akan mendapat notifikasi setelah akun disetujui.</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button class="pk-btn-secondary">Keluar</button>
            </form>
        </div>
    </div>
</x-layouts.guest>