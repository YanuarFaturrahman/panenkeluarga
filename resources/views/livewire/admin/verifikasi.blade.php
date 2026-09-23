<div>
    {{-- Slot Header disamakan styling-nya dengan menu lain --}}
    <x-slot:header>
        <h2 class="font-serif text-2xl font-bold text-gray-800 leading-tight">
            Verifikasi Akun
        </h2>
    </x-slot:header>

    {{-- Slot Subheader --}}
    <x-slot:subheader>
        Tinjau pendaftaran petani dan koordinator baru
    </x-slot:subheader>

    @if (session('sukses'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm p-3 font-medium">
            {{ session('sukses') }}
        </div>
    @endif

    <!-- Tab Switcher -->
    <div class="flex border-b border-gray-200 mb-6 gap-6">
        <button wire:click="$set('tab', 'pending')" class="pb-2.5 text-sm font-semibold border-b-2 transition cursor-pointer {{ $tab === 'pending' ? 'border-[#214332] text-[#214332]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Menunggu Verifikasi ({{ $jumlahPending }})
        </button>
        <button wire:click="$set('tab', 'terverifikasi')" class="pb-2.5 text-sm font-semibold border-b-2 transition cursor-pointer {{ $tab === 'terverifikasi' ? 'border-[#214332] text-[#214332]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Terverifikasi
        </button>
        <button wire:click="$set('tab', 'ditolak')" class="pb-2.5 text-sm font-semibold border-b-2 transition cursor-pointer {{ $tab === 'ditolak' ? 'border-[#214332] text-[#214332]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Ditolak
        </button>
    </div>

    <!-- Tabel Verifikasi -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-semibold border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3.5">Nama</th>
                    <th class="px-5 py-3.5">Peran</th>
                    <th class="px-5 py-3.5">Wilayah Domisili</th>
                    <th class="px-5 py-3.5">Tanggal Daftar</th>
                    <th class="px-5 py-3.5">Dokumen</th>
                    <th class="px-5 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $u)
                    <tr wire:key="user-{{ $u->id }}" class="hover:bg-gray-50/50">
                        <td class="px-5 py-4 font-semibold text-gray-800">{{ $u->name }}</td>
                        <td class="px-5 py-4 capitalize">
                            <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-[#214332]/10 text-[#214332]">
                                {{ $u->peran }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-600 text-xs">
                            @if ($u->village)
                                <span class="font-medium text-gray-800">Desa/Kel. {{ $u->village->name }}</span><br>
                                <span class="text-gray-500">Kec. {{ $u->village->district->name ?? '-' }}, {{ $u->village->district->city->name ?? '-' }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-gray-500 text-xs">{{ $u->created_at?->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-4">
                            @if($u->dokumen)
                                <a href="{{ asset('storage/' . $u->dokumen) }}" target="_blank" class="text-xs font-medium text-[#214332] hover:underline inline-flex items-center gap-1">
                                    📄 {{ $u->peran === 'petani' ? 'KTP + Foto Lahan' : 'KTP + Surat RT' }}
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($tab === 'pending')
                                <div class="flex justify-center gap-2">
                                    <button wire:click="setujui({{ $u->id }})" class="px-3 py-1.5 bg-[#214332] hover:bg-[#183225] text-white rounded-lg text-xs font-medium shadow-sm transition cursor-pointer">
                                        Setujui
                                    </button>
                                    <button wire:click="tolak({{ $u->id }})" class="px-3 py-1.5 border border-red-300 text-red-600 hover:bg-red-50 rounded-lg text-xs font-medium transition cursor-pointer">
                                        Tolak
                                    </button>
                                </div>
                            @else
                                <span class="text-xs font-semibold uppercase tracking-wider {{ $tab === 'terverifikasi' ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $u->status_verifikasi }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                            Tidak ada akun yang {{ $tab === 'pending' ? 'menunggu verifikasi' : $tab }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>