<div class="space-y-6">
    <x-slot:header>Group Buying Saya</x-slot:header>
    <x-slot:subheader>Riwayat sesi belanja bersama yang pernah Anda ikuti</x-slot:subheader>

    <div class="pk-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#f7f8f5] text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3">Komoditas</th>
                    <th class="px-5 py-3">Jumlah</th>
                    <th class="px-5 py-3">Subtotal</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($riwayat as $r)
                    <tr>
                        <td class="px-5 py-3 font-medium text-[#214332]">{{ $r->sesi->produk->nama_komoditas ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $r->jumlah_pesanan ?? 0 }} {{ $r->sesi->produk->satuan ?? 'pcs' }}</td>
                        <td class="px-5 py-3">Rp{{ number_format($r->subtotal ?? 0, 0, ',', '.') }}</td>
                        <td class="px-5 py-3"><span class="pk-badge bg-gray-100 text-gray-700">{{ ucfirst(str_replace('_', ' ', $r->status)) }}</span></td>
                        <td class="px-5 py-3 text-right"><a href="{{ route('produk.show', $r->sesi_id) }}" wire:navigate class="font-semibold text-[#538253]">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">Anda belum mengikuti sesi group buying apapun.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $riwayat->links() }}</div>
</div>