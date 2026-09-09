<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'PanenKeluarga') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- CDN Tailwind CSS + Custom Colors -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pk-dark': '#214332',
                        'pk-cream': '#f1f3e9',
                        'pk-orange': '#e07a5f',
                        'pk-green': '#538253',
                    }
                }
            }
        }
    </script>

    {{-- Mencegah elemen Alpine kedip (flicker) sebelum di-load --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-pk-cream text-pk-dark min-h-screen">
    <div class="flex min-h-screen">
        {{-- Sidebar (Desktop) --}}
        <aside class="hidden lg:flex lg:w-64 flex-col bg-pk-dark text-white px-5 py-6 flex-shrink-0">
            <div class="flex items-center gap-2 font-serif text-lg font-bold px-2 mb-8">
                <span class="text-xl">🌱</span> PanenKeluarga
            </div>

            <nav class="flex-1 space-y-1">
                @php $peran = strtolower(auth()->user()?->peran ?? auth()->user()?->role ?? ''); @endphp

                @if ($peran === 'petani')
                    <a href="{{ Route::has('petani.dashboard') ? route('petani.dashboard') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Dashboard</a>
                    <a href="{{ Route::has('petani.produk.index') ? route('petani.produk.index') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Produk Saya</a>
                    <a href="{{ Route::has('petani.pesanan') ? route('petani.pesanan') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Pesanan</a>
                    <a href="{{ Route::has('petani.pendapatan') ? route('petani.pendapatan') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Pendapatan</a>
                
                @elseif ($peran === 'koordinator')
                    <a href="{{ Route::has('koordinator.dashboard') ? route('koordinator.dashboard') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Dashboard</a>
                    <a href="{{ Route::has('koordinator.sesi.index') ? route('koordinator.sesi.index') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Sesi Group Buying</a>
                    <a href="{{ Route::has('koordinator.titik.index') ? route('koordinator.titik.index') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Titik Pengambilan</a>
                    <a href="{{ Route::has('koordinator.peserta') ? route('koordinator.peserta') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Peserta</a>
                
                @elseif ($peran === 'admin')
                    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Dashboard</a>
                    <a href="{{ Route::has('admin.verifikasi') ? route('admin.verifikasi') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Verifikasi Akun</a>
                    <a href="{{ Route::has('admin.transaksi') ? route('admin.transaksi') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Transaksi</a>
                    <a href="{{ Route::has('admin.subsidi') ? route('admin.subsidi') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Subsidi Nutrisi</a>
                    <a href="{{ Route::has('admin.laporan') ? route('admin.laporan') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Laporan</a>
                
                @else
                    <a href="{{ Route::has('beranda') ? route('beranda') : url('/') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-white/10 text-sm font-semibold">Beranda</a>
                    <a href="{{ Route::has('katalog') ? route('katalog') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Katalog</a>
                    @auth
                        <a href="{{ Route::has('group-buying-saya') ? route('group-buying-saya') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Group Buying Saya</a>
                    @endauth
                    <a href="{{ Route::has('subsidi.transparansi') ? route('subsidi.transparansi') : '#' }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Program Subsidi</a>
                @endif
            </nav>

            <div class="border-t border-white/10 pt-4 mt-4">
                @auth
                    <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}" class="w-full mb-4">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 text-sm text-white/70 hover:text-white px-2 py-1 transition w-full text-left">
                            <span>↩</span>
                            <span>Keluar</span>
                        </button>
                    </form>

                    <div class="flex items-center gap-3 px-2">
                        <div class="w-9 h-9 rounded-full bg-pk-orange flex items-center justify-center font-semibold text-sm text-white uppercase flex-shrink-0">
                            {{ collect(explode(' ', auth()->user()->name ?? 'U'))->map(fn($w) => $w[0] ?? '')->take(2)->implode('') }}
                        </div>
                        <div class="text-sm truncate">
                            <p class="font-semibold text-white truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                            <p class="text-white/60 capitalize text-xs">{{ auth()->user()->peran ?? 'Konsumen' }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col gap-2 px-2">
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="w-full text-center py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition">Masuk</a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="w-full text-center py-2 bg-pk-orange hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition">Daftar</a>
                    </div>
                @endauth
            </div>
        </aside>

        {{-- Panel Konten Utama --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white px-4 md:px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-40">
                <div>
                    {{-- Logo sederhana khusus Tampilan Mobile --}}
                    <div class="lg:hidden text-xs text-pk-green font-bold flex items-center gap-1 mb-0.5">
                        <span>🌱</span> PanenKeluarga
                    </div>
                    <h1 class="font-serif text-lg md:text-xl font-bold text-pk-dark leading-tight">{{ $header ?? 'Beranda' }}</h1>
                    @isset($subheader) <p class="text-xs text-gray-500 hidden sm:block">{{ $subheader }}</p> @endisset
                </div>
                
                <div class="flex items-center gap-2 md:gap-3 text-gray-500">
                    <livewire:notification-list />
                    <button class="p-2 hover:bg-gray-100 rounded-full transition">🔍</button>
                </div>
            </header>
            
            <main class="p-4 md:p-6 flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>