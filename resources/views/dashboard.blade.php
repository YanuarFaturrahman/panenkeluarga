<div class="space-y-6">
    <x-slot:header>
        Ada apa saja di Desa {{ auth()->user()->wilayah?->kelurahan ?? auth()->user()->village?->name ?? 'Anda' }} hari ini?
    </x-slot:header>

    <x-slot:subheader>
        Selamat siang, {{ explode(' ', auth()->user()->name ?? 'User')[0] }} 👋
    </x-slot:subheader>

    <div class="rounded-3xl bg-[#214332] px-6 py-5 text-white shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-emerald-200/80">PanenKeluarga</p>
                <h2 class="mt-2 font-serif text-2xl font-semibold">Beli kebutuhan rumah tangga langsung dari petani.</h2>
            </div>
            <a href="{{ route('katalog') }}" wire:navigate class="pk-btn-secondary bg-white text-[#214332] hover:bg-[#eef3ee]">Jelajahi katalog</a>
        </div>
    </div>

    <div class="mb-6">
        <input type="text"
               placeholder="Cari sayur, buah, atau protein segar..."
               class="pk-input max-w-2xl cursor-pointer"
               onclick="window.location='{{ route('katalog') }}'"
               readonly>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="pk-stat">
            <p class="text-sm text-gray-500">Sesi aktif</p>
            <p class="pk-stat-value">{{ $sesiAktif->count() }}</p>
        </div>
        <div class="pk-stat">
            <p class="text-sm text-gray-500">Surplus bulanan</p>
            <p class="pk-stat-value">Rp{{ number_format($totalSubsidiBulanIni ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="pk-stat">
            <p class="text-sm text-gray-500">Desa Anda</p>
            <p class="pk-stat-value">Desa {{ auth()->user()->wilayah?->kelurahan ?? auth()->user()->village?->name ?? '-' }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4 pt-2">
        <h2 class="font-serif text-2xl font-semibold">Group Buying Aktif</h2>
        <a href="{{ route('katalog') }}" wire:navigate class="text-sm font-semibold text-[#538253]">Lihat semua →</a>
    </div>

    <div class="grid md:grid-cols-3 gap-5 mb-8">
        @forelse ($sesiAktif as $sesi)
            <livewire:komponen.kartu-sesi :sesi="$sesi" :key="$sesi->id" />
        @empty
            <div class="pk-card col-span-3 p-6 text-gray-500">Belum ada sesi group buying aktif saat ini.</div>
        @endforelse
    </div>

    <div class="pk-card bg-[#fff2ee] border-[#f3d7ce] p-5">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#e07a5f] text-2xl text-white">🌱</div>
                <div>
                    <p class="font-semibold text-[#214332]">Program Subsidi Nutrisi Anak</p>
                    <p class="text-sm text-gray-600">Rp{{ number_format($totalSubsidiBulanIni ?? 0, 0, ',', '.') }} tersalurkan bulan ini</p>
                </div>
            </div>
            <a href="{{ route('subsidi.transparansi') }}" wire:navigate class="pk-btn-secondary">Lihat Transparansi</a>
        </div>
    </div>
</div>