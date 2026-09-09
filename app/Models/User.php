<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'peran', 
        'nomor_hp',
        'wilayah_id', 
        'status_verifikasi', 
        'foto_profil',
        'dokumen', // <-- TAMBAHKAN BARIS INI
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    // Relasi
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'petani_id');
    }

    public function sesiDikelola()
    {
        return $this->hasMany(SesiGroupBuying::class, 'koordinator_id');
    }

    public function pesertaSesi()
    {
        return $this->hasMany(PesertaSesi::class, 'konsumen_id');
    }

    // Helper cek peran
    public function isPetani(): bool { return $this->peran === 'petani'; }
    public function isKoordinator(): bool { return $this->peran === 'koordinator'; }
    public function isKonsumen(): bool { return $this->peran === 'konsumen'; }
    public function isAdmin(): bool { return $this->peran === 'admin'; }
}