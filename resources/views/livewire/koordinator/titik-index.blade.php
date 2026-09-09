<div>
    <x-slot:header>Titik Pengambilan</x-slot:header>
    <x-slot:subheader>Kelola lokasi pengambilan pesanan beserta jam operasional di wilayah binaan</x-slot:subheader>

    <button wire:click="$toggle('formTerbuka')" class="pk-btn-primary mb-6">+ Tambah Titik Pengambilan</button>

    @if ($formTerbuka)
        <div class="pk-card max-w-lg mb-6 space-y-3">
            <input type="text" wire:model="nama_lokasi" placeholder="Nama Lokasi" class="pk-input">
            @error('nama_lokasi') <span class="pk-error">{{ $message }}</span> @enderror
            <input type="text" wire:model="alamat" placeholder="Alamat lengkap" class="pk-input">
            @error('alamat') <span class="pk-error">{{ $message }}</span> @enderror
            <input type="text" wire:model="jam_operasional" placeholder="Jam operasional, contoh: 16.00 - 19.00 WIB" class="pk-input">
            <button wire:click="simpan" class="pk-btn-primary">Simpan</button>
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-5">
        @foreach ($titik as $t)
            <div class="pk-card">
                <p class="font-semibold">📍 {{ $t->nama_lokasi }}</p>
                <p class="text-sm text-gray-500">{{ $t->alamat }}</p>
                <p class="text-sm text-gray-500">{{ $t->jam_operasional }}</p>
            </div>
        @endforeach
    </div>
</div>