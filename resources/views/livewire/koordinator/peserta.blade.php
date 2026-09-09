<div>
    <x-slot:header>Peserta</x-slot:header>
    <x-slot:subheader>Daftar keluarga yang aktif mengikuti sesi group buying di wilayah binaan</x-slot:subheader>

    {{-- Notifikasi Sukses --}}
    @if (session()->has('sukses'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
            {{ session('sukses') }}
        </div>
    @endif

    {{-- Baris Filter & Search --}}
    <div class="mb-4 flex flex-col sm:flex-row gap-3 justify-between items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="w-full sm:w-1/2">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Cari nama keluarga atau komoditas..." 
                class="w-full rounded-xl border-gray-300 focus:border-[#3b5e4c] focus:ring-[#3b5e4c] text-sm py-2 px-3"
            >
        </div>
        <div class="w-full sm:w-auto flex items-center gap-2">
            <label class="text-xs font-bold text-gray-500 uppercase whitespace-nowrap">Filter Status:</label>
            <select wire:model.live="status" class="w-full sm:w-auto rounded-xl border-gray-300 focus:border-[#3b5e4c] focus:ring-[#3b5e4c] text-sm py-2 px-3">
                <option value="">Semua Status</option>
                <option value="menunggu_kuota">Menunggu Kuota</option>
                <option value="dikonfirmasi">Dikonfirmasi</option>
                <option value="siap_diambil">Siap Diambil</option>
                <option value="selesai">Selesai</option>
                <option value="batal">Batal</option>
            </select>
        </div>
    </div>

    {{-- Tabel Peserta --}}
    <div class="pk-card p-0 overflow-hidden border border-gray-200 rounded-xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3.5">Keluarga</th>
                        <th class="px-5 py-3.5">Komoditas</th>
                        <th class="px-5 py-3.5">Jumlah</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($peserta as $p)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3.5 font-medium text-gray-900">{{ $p->konsumen->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-700">{{ $p->sesi->produk->nama_komoditas ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-gray-700 font-medium">{{ $p->jumlah_pesanan }} {{ $p->sesi->produk->satuan ?? 'unit' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $badgeClass = match($p->status) {
                                        'dikonfirmasi' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'siap_diambil' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'selesai' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        'batal' => 'bg-red-100 text-red-800 border-red-200',
                                        default => 'bg-amber-100 text-amber-800 border-amber-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if ($p->status === 'menunggu_kuota')
                                        <button 
                                            wire:click="ubahStatus({{ $p->id }}, 'dikonfirmasi')" 
                                            class="px-3 py-1 bg-[#3b5e4c] hover:bg-[#2d493b] text-white text-xs font-semibold rounded-lg transition"
                                        >
                                            Konfirmasi
                                        </button>
                                    @elseif ($p->status === 'dikonfirmasi')
                                        <button 
                                            wire:click="ubahStatus({{ $p->id }}, 'siap_diambil')" 
                                            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition"
                                        >
                                            Siap Diambil
                                        </button>
                                    @elseif ($p->status === 'siap_diambil')
                                        <button 
                                            wire:click="ubahStatus({{ $p->id }}, 'selesai')" 
                                            class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition"
                                        >
                                            Tandai Selesai
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 font-medium">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-gray-400">
                                Tidak ada data peserta yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $peserta->links() }}</div>
</div>