<div class="space-y-6">
    <x-slot:header>Program Subsidi</x-slot:header>
    <x-slot:subheader>Transparansi alokasi dan penyaluran surplus transaksi untuk gizi anak</x-slot:subheader>

    <div class="grid md:grid-cols-2 gap-5 mb-6">
        <div class="rounded-2xl bg-[#214332] p-5 text-white shadow-sm">
            <p class="text-sm text-emerald-100/70">Total Surplus Terkumpul</p>
            <p class="mt-3 font-serif text-3xl font-bold">Rp{{ number_format($totalTerkumpul, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-emerald-100/75">2% dari setiap transaksi dialokasikan otomatis oleh sistem.</p>
        </div>
        <div class="pk-card p-5">
            <p class="text-sm text-gray-500">Total Anak Penerima Manfaat</p>
            <p class="mt-3 font-serif text-3xl font-bold text-[#214332]">{{ $totalAnakPenerima }}</p>
            <p class="mt-2 text-sm text-gray-500">Angka manfaat yang tercatat dari program nutrisi komunitas.</p>
        </div>
    </div>

    <div class="pk-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#f7f8f5] text-left text-gray-600">
                <tr>
                    <th class="px-5 py-3">Wilayah</th>
                    <th class="px-5 py-3">Dialokasikan</th>
                    <th class="px-5 py-3">Disalurkan</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($riwayat as $r)
                    <tr>
                        <td class="px-5 py-3">{{ $r->wilayah?->kelurahan ?? 'Wilayah' }}</td>
                        <td class="px-5 py-3">Rp{{ number_format($r->jumlah_dialokasikan ?? 0, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">Rp{{ number_format($r->jumlah_disalurkan ?? 0, 0, ',', '.') }}</td>
                        <td class="px-5 py-3"><span class="pk-badge bg-gray-100 text-gray-700">{{ ucfirst($r->status ?? 'aktif') }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $riwayat->links() }}</div>
</div>