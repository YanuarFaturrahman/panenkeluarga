<div>
    <x-slot name="header">Transaksi</x-slot>
    <x-slot name="subheader">Rekap seluruh transaksi group buying di platform beserta status dan surplus subsidi</x-slot>

    <div class="pk-card p-0 overflow-hidden bg-white rounded-xl shadow-sm border border-gray-100">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-medium">
                <tr>
                    <th class="px-5 py-3">Kode</th>
                    <th class="px-5 py-3">Komoditas</th>
                    <th class="px-5 py-3">Konsumen</th>
                    <th class="px-5 py-3">Total</th>
                    <th class="px-5 py-3">Subsidi</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($transaksi as $t)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $t->kode_transaksi ?? '-' }}</td>
                        <td class="px-5 py-3 font-medium">{{ $t->pesertaSesi?->sesi?->produk?->nama_komoditas ?? 'Komoditas Tidak Ditemukan' }}</td>
                        <td class="px-5 py-3">{{ $t->pesertaSesi?->konsumen?->name ?? 'Pengguna' }}</td>
                        <td class="px-5 py-3 font-semibold">Rp{{ number_format($t->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-pk-orange font-medium">Rp{{ number_format($t->alokasi_subsidi ?? 0, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 inline-block">
                                {{ ucfirst($t->status_pembayaran ?? 'Selesai') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-400">Belum ada data transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transaksi->links() }}
    </div>
</div>