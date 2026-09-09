<div class="relative" x-data="{ open: false }">
    {{-- Tombol Lonceng --}}
    <button @click="open = !open" class="relative p-2 hover:bg-gray-100 rounded-full focus:outline-none transition">
        <span class="text-lg">🔔</span>
        @if (auth()->check() && auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>

    {{-- Dropdown Menu --}}
    <div 
        x-show="open" 
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="p-3.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-xs text-pk-dark uppercase tracking-wider">Notifikasi</h3>
            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                <button wire:click="markAllAsRead" class="text-[11px] text-emerald-700 font-semibold hover:underline focus:outline-none">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
            @if(auth()->check() && auth()->user()->notifications->count() > 0)
                @foreach(auth()->user()->notifications->take(10) as $notification)
                    @php
                        $data = $notification->data;
                        $title = $data['title'] ?? $data['judul'] ?? null;
                        $message = $data['message'] ?? $data['pesan'] ?? 'Notifikasi baru';
                        $url = $data['url'] ?? $data['link'] ?? null;
                        $status = $data['status'] ?? null;
                    @endphp

                    <div 
                        wire:click="markAsRead('{{ $notification->id }}')" 
                        class="p-3.5 cursor-pointer hover:bg-gray-50 transition relative flex gap-3 items-start {{ $notification->read_at ? 'opacity-60 bg-white' : 'bg-emerald-50/40 font-semibold' }}"
                    >
                        {{-- Icon Status Barang / General --}}
                        <div class="mt-0.5 flex-shrink-0 text-base">
                            @if(str_contains(strtolower($message), 'dikirim') || $status === 'dikirim')
                                🚚
                            @elseif(str_contains(strtolower($message), 'selesai') || $status === 'selesai')
                                ✅
                            @elseif(str_contains(strtolower($message), 'batal') || $status === 'dibatalkan')
                                ❌
                            @elseif(str_contains(strtolower($message), 'pesanan') || isset($data['pesanan_id']))
                                📦
                            @else
                                📢
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Judul Notifikasi (Jika ada) --}}
                            @if($title)
                                <p class="text-xs font-bold text-pk-dark truncate">
                                    {{ $title }}
                                </p>
                            @endif

                            {{-- Pesan Notifikasi --}}
                            <p class="text-xs text-gray-800 leading-relaxed break-words">
                                {{ $message }}
                            </p>

                            {{-- Info Link / Status Tambahan --}}
                            @if($url)
                                <a href="{{ $url }}" class="inline-block mt-1 text-[11px] text-emerald-600 hover:underline font-normal">
                                    Lihat Detail &rarr;
                                </a>
                            @endif

                            <span class="text-[10px] text-gray-400 mt-1 block font-normal">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-6 text-center text-xs text-gray-400">
                    Belum ada notifikasi masuk.
                </div>
            @endif
        </div>
    </div>
</div>