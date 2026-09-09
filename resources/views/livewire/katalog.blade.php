<div class="space-y-6">
    <x-slot:header>Katalog Produk</x-slot:header>
    <x-slot:subheader>Daftar komoditas dari petani mitra di wilayah Anda</x-slot:subheader>

    <div class="pk-card p-5 mb-6">
        <div class="flex flex-col gap-3 md:flex-row">
            <input type="text" wire:model.live.debounce.400ms="cari" placeholder="Cari komoditas..." class="pk-input md:max-w-xs">
            <select wire:model.live="kategori" class="pk-input md:max-w-xs">
                <option value="">Semua Kategori</option>
                <option value="sayur">Sayur</option>
                <option value="buah">Buah</option>
                <option value="protein">Protein</option>
            </select>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-5 mb-6">
        @forelse ($sesi as $item)
            <livewire:komponen.kartu-sesi :sesi="$item" :key="$item->id" />
        @empty
            <div class="pk-card col-span-3 p-6 text-gray-500">Tidak ada produk yang cocok dengan pencarian.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $sesi->links() }}</div>
</div>