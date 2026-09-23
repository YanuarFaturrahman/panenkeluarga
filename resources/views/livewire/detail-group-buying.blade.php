<x-slot:header>{{ $sesi->produk->nama_komoditas }}</x-slot:header>
<x-slot:subheader>
    {{ $sesi->produk->petani->name ?? $sesi->koordinator->name ?? 'Petani' }} · 
    {{ $sesi->wilayah?->nama_lengkap ?? $sesi->wilayah?->name ?? $sesi->wilayah?->nama ?? 'Wilayah' }}
</x-slot:subheader>

<div wire:poll.5s class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="pk-card">
            <p class="text-2xl font-bold mb-1">Rp{{ number_format($sesi->harga_satuan, 0, ',', '.') }} / {{ $sesi->produk->satuan }}</p>
            <p class="text-gray-600">{{ $sesi->produk->deskripsi }}</p>

            <div class="mt-5">
                @php
                    $jumlahKeluarga = $sesi->peserta->count();
                    $targetKeluarga = $sesi->kuota_minimum > 0 ? $sesi->kuota_minimum : 1;
                    $persentaseKeluarga = min(100, round(($jumlahKeluarga / $targetKeluarga) * 100));
                @endphp
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-semibold">Progres Kuota Peserta</span>
                    <span>{{ $jumlahKeluarga }} / {{ $sesi->kuota_minimum }} keluarga ({{ $persentaseKeluarga }}%)</span>
                </div>
                <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full bg-pk-green transition-all" style="width: {{ $persentaseKeluarga }}%"></div>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-3 text-sm">
                <span class="pk-badge bg-pk-orange/15 text-pk-orange">⏰ {{ $sesi->sisa_waktu }}</span>
                <span class="pk-badge bg-emerald-100 text-emerald-800">📦 Sisa Stok: {{ $sesi->stok_sisa }} {{ $sesi->produk->satuan }}</span>
                <span class="pk-badge
                    @class([
                        'bg-blue-100 text-blue-700' => $sesi->status === 'berjalan',
                        'bg-green-100 text-green-700' => $sesi->status === 'kuota_tercapai',
                        'bg-gray-100 text-gray-600' => $sesi->status === 'selesai',
                    ])">
                    {{ ['berjalan' => 'Sedang Berjalan', 'kuota_tercapai' => 'Kuota Tercapai', 'selesai' => 'Selesai'][$sesi->status] ?? $sesi->status }}
                </span>
            </div>
        </div>

        <div class="pk-card">
            <p class="font-semibold mb-3">Daftar Peserta ({{ $sesi->peserta->count() }})</p>
            <div class="divide-y divide-gray-100">
                @forelse ($sesi->peserta as $p)
                    <div class="py-2 flex justify-between text-sm">
                        <span>{{ $p->konsumen->name }}</span>
                        <span class="text-gray-500">{{ $p->jumlah_pesanan }} {{ $sesi->produk->satuan }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-2">Belum ada peserta.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="pk-card h-fit">
        @if (session('sukses'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm p-3">{{ session('sukses') }}</div>
        @endif
        @error('umum') <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-pk-red text-sm p-3">{{ $message }}</div> @enderror

        @if ($sudahGabung)
            <p class="text-center text-pk-green font-semibold py-6">✓ Anda sudah bergabung di sesi ini</p>
        @elseif ($sesi->status !== 'berjalan')
            <p class="text-center text-gray-500 py-6">Sesi ini sudah tidak menerima peserta baru.</p>
        @elseif ($sesi->stok_sisa <= 0)
            <p class="text-center text-red-500 font-semibold py-6">Maaf, stok komoditas telah habis.</p>
        @else
            @php
                $maxBatasInView = min(20, $sesi->stok_sisa);
            @endphp
            <form wire:submit="gabungGroupBuying" class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-semibold">Jumlah Pesanan ({{ $sesi->produk->satuan }})</label>
                        <span class="text-xs text-gray-500">Maks. {{ $maxBatasInView }}</span>
                    </div>
                    <input 
                        type="number" 
                        min="1" 
                        max="{{ $maxBatasInView }}" 
                        wire:model.live="jumlahPesanan" 
                        class="pk-input"
                    >
                    @error('jumlahPesanan') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-between text-sm border-t pt-3">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-bold">Rp{{ number_format(($jumlahPesanan ?: 1) * $sesi->harga_satuan, 0, ',', '.') }}</span>
                </div>
                <button type="submit" class="pk-btn-primary w-full" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="gabungGroupBuying">Gabung Group Buying</span>
                    <span wire:loading wire:target="gabungGroupBuying">Memproses...</span>
                </button>
            </form>
        @endif

        @if ($sesi->titikPengambilan)
            <div class="mt-5 pt-5 border-t border-gray-100 text-sm">
                <p class="font-semibold mb-1">📍 Titik Pengambilan</p>
                <p class="text-gray-600">{{ $sesi->titikPengambilan->nama_lokasi }}</p>
                <p class="text-gray-500">{{ $sesi->titikPengambilan->alamat }}</p>
                <p class="text-gray-500">{{ $sesi->titikPengambilan->jam_operasional }}</p>
            </div>
        @endif
    </div>
</div>