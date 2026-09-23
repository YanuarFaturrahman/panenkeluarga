<div>
    <x-slot:header>Dashboard</x-slot:header>
    <x-slot:subheader>Ringkasan operasional platform</x-slot:subheader>

    <!-- Kartu Statistik (4 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <!-- Card 1: Total Petani Mitra -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 mb-1">Total Petani Mitra</p>
            <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ number_format($totalPetani) }}</h3>
        </div>

        <!-- Card 2: Total Koordinator -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 mb-1">Total Koordinator</p>
            <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ number_format($totalKoordinator) }}</h3>
        </div>

        <!-- Card 3: Transaksi Bulan Ini -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 mb-1">Transaksi Bulan Ini</p>
            <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ number_format($transaksiBulanIni) }}</h3>
        </div>

        <!-- Card 4: Surplus Terkumpul -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 mb-1">Surplus Terkumpul</p>
            <h3 class="text-3xl font-bold text-gray-800 mb-2">Rp{{ number_format($surplusTerkumpul, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Layout Utama: Grafik + Aktivitas Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Tren Transaksi 12 Minggu Terakhir (8 Kolom) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif font-bold text-lg text-gray-800">Tren Transaksi 12 Minggu Terakhir</h3>
                <span class="text-xs text-gray-400 font-medium">(Jumlah Transaksi)</span>
            </div>

            @php
                $maxCount = max(array_merge($trenMingguan, [1]));
                $yMax = max(4, ceil($maxCount / 2) * 2); 
            @endphp

            <div class="relative pt-4">
                <!-- Area Chart dengan Sumbu Y -->
                <div class="flex h-52">
                    <!-- Sumbu Y (Label Nilai) -->
                    <div class="flex flex-col justify-between text-[11px] font-medium text-gray-400 pr-3 select-none text-right w-8">
                        <span>{{ $yMax }}</span>
                        <span>{{ round($yMax * 0.75) }}</span>
                        <span>{{ round($yMax * 0.5) }}</span>
                        <span>{{ round($yMax * 0.25) }}</span>
                        <span>0</span>
                    </div>

                    <!-- Chart Body + Garis Grid -->
                    <div class="relative flex-1 flex items-end justify-between gap-1.5 border-b border-l border-gray-200 pl-2 pt-2">
                        <!-- Garis Grid Horizontal -->
                        <div class="absolute inset-x-0 top-0 border-b border-dashed border-gray-100"></div>
                        <div class="absolute inset-x-0 top-1/4 border-b border-dashed border-gray-100"></div>
                        <div class="absolute inset-x-0 top-2/4 border-b border-dashed border-gray-100"></div>
                        <div class="absolute inset-x-0 top-3/4 border-b border-dashed border-gray-100"></div>

                        <!-- Bar Chart -->
                        @foreach($trenMingguan as $index => $count)
                            @php
                                $heightPercent = $yMax > 0 ? ($count / $yMax) * 100 : 0;
                            @endphp
                            <div class="relative flex-1 h-full flex items-end justify-center group z-10">
                                <!-- Tooltip Angka saat Hover -->
                                <div class="absolute -top-8 hidden group-hover:flex flex-col items-center z-20">
                                    <span class="bg-gray-800 text-white text-[10px] font-bold py-1 px-2 rounded shadow-md whitespace-nowrap">
                                        {{ $count }} transaksi
                                    </span>
                                    <div class="w-2 h-2 bg-gray-800 rotate-45 -mt-1"></div>
                                </div>

                                <!-- Batang Grafik -->
                                <div class="w-full bg-[#538253] group-hover:bg-[#214332] rounded-t-sm transition-all duration-200 min-h-[4px]" 
                                     style="height: {{ max($heightPercent, 2) }}%;">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sumbu X (Label Minggu) -->
                <div class="flex pl-8 mt-2">
                    <div class="flex-1 flex justify-between gap-1.5 pl-2 text-center">
                        @foreach($trenMingguan as $index => $count)
                            <span class="flex-1 text-[10px] text-gray-400 font-medium">
                                @if($index === 11)
                                    <strong class="text-gray-700 font-bold">M Ini</strong>
                                @else
                                    M{{ $index + 1 }}
                                @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru (4 Kolom) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="font-serif font-bold text-lg text-gray-800 mb-4">Aktivitas Terbaru</h3>
            
            <div class="space-y-4">
                @forelse ($aktivitasTerbaru as $a)
                    <div class="flex items-start space-x-2.5">
                        <span class="w-2 h-2 mt-1.5 rounded-full bg-amber-500 shrink-0"></span>
                        <div>
                            <p class="text-xs font-bold text-gray-800 leading-tight">{{ $a['judul'] }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $a['sub'] }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $a['waktu'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-4 text-center">Belum ada aktivitas terbaru.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>