<?php

use App\Http\Middleware\EnsureAccountIsVerified;
use App\Livewire\Admin;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\MenungguVerifikasi;
use App\Livewire\Auth\Register;
use App\Livewire\Beranda;
use App\Livewire\DetailGroupBuying;
use App\Livewire\GroupBuyingSaya;
use App\Livewire\Katalog;
use App\Livewire\Koordinator;
use App\Livewire\Petani;
use App\Livewire\Subsidi\Transparansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', Beranda::class)->name('beranda');
Route::get('/katalog', Katalog::class)->name('katalog');
Route::get('/produk/{sesi}', DetailGroupBuying::class)->name('produk.show');
Route::get('/subsidi/transparansi', Transparansi::class)->name('subsidi.transparansi');

/*
|--------------------------------------------------------------------------
| Guest Routes (Sebelum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    if (class_exists(Login::class)) {
        Route::get('/login', Login::class)->name('login');
    }

    if (class_exists(Register::class)) {
        Route::get('/register', Register::class)->name('register');
    }
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Route Logout (Aksi POST untuk memproses keluar dari sesi)
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

    // 1. Route Fallback Dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $peran = strtolower($user->peran ?? $user->role ?? '');

        // Jika user berstatus pending, langsung arahkan ke menunggu-verifikasi
        if (in_array($peran, ['petani', 'koordinator']) && $user->status_verifikasi === 'pending') {
            return redirect()->route('menunggu-verifikasi');
        }

        return match ($peran) {
            'admin'       => redirect()->route('admin.dashboard'),
            'koordinator' => redirect()->route('koordinator.dashboard'),
            'petani'      => redirect()->route('petani.dashboard'),
            default       => redirect()->route('beranda'),
        };
    })->name('dashboard');

    // 2. Halaman Verifikasi Status User (Livewire Component)
    Route::get('/menunggu-verifikasi', MenungguVerifikasi::class)->name('menunggu-verifikasi');

    // 3. Group Konsumen
    Route::middleware(['peran:konsumen'])->group(function () {
        Route::get('/group-buying-saya', GroupBuyingSaya::class)->name('group-buying-saya');
    });

    // 4. Group Petani (Proteksi Peran & Status Verifikasi)
    Route::middleware(['peran:petani', EnsureAccountIsVerified::class])->prefix('petani')->name('petani.')->group(function () {
        Route::get('/dashboard', Petani\Dashboard::class)->name('dashboard');
        Route::get('/produk', Petani\ProdukIndex::class)->name('produk.index');
        Route::get('/produk/tambah', Petani\TambahProduk::class)->name('produk.tambah');
        Route::get('/pesanan', Petani\Pesanan::class)->name('pesanan');
        Route::get('/pendapatan', Petani\Pendapatan::class)->name('pendapatan');
    });

    // 5. Group Koordinator RT/RW (Proteksi Peran & Status Verifikasi)
    Route::middleware(['peran:koordinator', EnsureAccountIsVerified::class])->prefix('koordinator')->name('koordinator.')->group(function () {
        Route::get('/dashboard', Koordinator\Dashboard::class)->name('dashboard');
        
        // Halaman Utama Sesi Group Buying (Menampilkan Form Buka Sesi & Daftar Sesi)
        Route::get('/sesi', Koordinator\BukaSesi::class)->name('sesi.index');
        
        // Alias route 'sesi.buka' agar tombol/link lama di dashboard tidak error
        Route::get('/sesi/buka', Koordinator\BukaSesi::class)->name('sesi.buka');
        
        Route::get('/titik-pengambilan', Koordinator\TitikIndex::class)->name('titik.index');
        Route::get('/peserta', Koordinator\Peserta::class)->name('peserta');
    });

    // 6. Group Admin
    Route::middleware(['peran:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', Admin\Dashboard::class)->name('dashboard');
        Route::get('/verifikasi', Admin\Verifikasi::class)->name('verifikasi');
        Route::get('/transaksi', Admin\Transaksi::class)->name('transaksi');
        Route::get('/subsidi', Admin\Subsidi::class)->name('subsidi');
        Route::get('/laporan', Admin\Laporan::class)->name('laporan');
    });

    // 7. Profile & Pengaturan Akun
    Route::view('profile', 'profile')->name('profile');
});

/*
|--------------------------------------------------------------------------
| Auth Routes Bawaan
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}