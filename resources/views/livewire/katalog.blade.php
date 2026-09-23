<div class="space-y-6">
    <x-slot:header>Katalog Produk</x-slot:header>
    <x-slot:subheader>
        Daftar komoditas dari petani mitra di wilayah Desa {{ auth()->user()->village?->name ?? auth()->user()->wilayah?->nama ?? 'Anda' }}
    </x-slot:subheader>

    {{-- Panel Filter --}}
    <div class="pk-card p-5 mb-6 bg-white rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row">
            <input type="text" 
                   wire:model.live.debounce.400ms="cari" 
                   placeholder="Cari komoditas..." 
                   class="pk-input w-full md:max-w-xs rounded-xl border-gray-300 focus:border-[#538253] focus:ring-[#538253] text-sm">
            
            <select wire:model.live="kategori" 
                    class="pk-input w-full md:max-w-xs rounded-xl border-gray-300 focus:border-[#538253] focus:ring-[#538253] text-sm">
                <option value="">Semua Kategori</option>
                <option value="sayur">Sayur</option>
                <option value="buah">Buah</option>
                <option value="protein">Protein</option>
                <option value="rempah">Rempah</option>
            </select>
        </div>
    </div>

    {{-- Grid Sesi Produk --}}
    <div class="grid md:grid-cols-3 gap-5 mb-6">
        @forelse ($sesi as $item)
            <livewire:komponen.kartu-sesi :sesi="$item" :key="$item->id" />
        @empty
            <div class="pk-card col-span-3 p-8 text-center bg-white rounded-2xl border border-gray-200 text-gray-500">
                <p class="font-bold text-lg text-gray-700">Belum Ada Sesi Aktif</p>
                <p class="text-sm mt-1">Tidak ada produk atau sesi group buying aktif untuk wilayah Desa {{ auth()->user()->village?->name ?? auth()->user()->wilayah?->nama ?? 'Anda' }}.</p>
            </div>
        @endforelse
    </div>

    {{-- Navigasi Pagination --}}
    <div class="mt-4">
        {{ $sesi->links() }}
    </div>
</div>