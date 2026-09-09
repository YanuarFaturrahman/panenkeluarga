<div>
    {{-- Header Slot --}}
    <x-slot:header>
        Ada apa saja di RT {{ auth()->user()->wilayah?->nama_rt }} / RW {{ auth()->user()->wilayah?->nama_rw }} hari ini?
    </x-slot:header>

    <x-slot:subheader>
        Selamat siang, {{ explode(' ', auth()->user()->name)[0] }} 👋
    </x-slot:subheader>

    {{-- Input Pencarian --}}
    <div class="mb-6">
        <input type="text" 
               placeholder="Cari sayur, buah, atau protein segar..."
               class="pk-input max-w-xl cursor-pointer" 
               onclick="window.location='{{ route('katalog') }}'" 
               readonly>
    </div>

    {{-- Judul Section --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-serif text-lg font-bold">Group Buying Aktif di Wilayah Anda</h2>
        <a href="{{ route('katalog') }}" wire:navigate class="text-sm font-semibold text-pk-green">Lihat semua →</a>
    </div>

    {{-- Grid Sesi Group Buying --}}
    <div class="grid md:grid-cols-3 gap-5 mb-8">
        @forelse ($sesiAktif as $sesi)
            <livewire:komponen.kartu-sesi :sesi="$sesi" :key="$sesi->id" />
        @empty
            <p class="text-gray-500 col-span-3">Belum ada sesi group buying aktif di wilayah Anda.</p>
        @endforelse
    </div>

    {{-- Kartu Subsidi Nutrisi --}}
    <div class="pk-card bg-pk-orange/10 border border-pk-orange/30 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-pk-orange flex items-center justify-center text-white">🌱</div>
            <div>
                <p class="font-semibold">Program Subsidi Nutrisi Anak</p>
                <p class="text-sm text-gray-600">Rp{{ number_format($totalSubsidiBulanIni, 0, ',', '.') }} tersalurkan bulan ini</p>
            </div>
        </div>
        <a href="{{ route('subsidi.transparansi') }}" wire:navigate class="pk-btn-secondary">Lihat Transparansi</a>
    </div>
</div>