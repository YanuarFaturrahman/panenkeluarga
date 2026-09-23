<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravolt\Indonesia\Models\Village;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'peran', 
        'nomor_hp',
        'village_code',
        'status_verifikasi', 
        'foto_profil',
        'dokumen',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', 
            'password' => 'hashed'
        ];
    }

    // Helper Accessor untuk menjamin ketersediaan $user->wilayah_id
    public function getWilayahIdAttribute()
    {
        return $this->village_code;
    }

    // Relasi ke Data Wilayah Pemerintah (Laravolt Indonesia)
    public function village()
    {
        return $this->belongsTo(Village::class, 'village_code', 'code');
    }

    // Shortcut alias agar bisa dipanggil via $user->desa atau $user->wilayah
    public function desa()
    {
        return $this->village();
    }

    public function wilayah()
    {
        return $this->village();
    }

    // Helper Shortcut Relasi Wilayah
    public function district()
    {
        return $this->village?->district;
    }

    public function city()
    {
        return $this->village?->district?->city;
    }

    public function province()
    {
        return $this->village?->district?->city?->province;
    }

    // Relasi Bisnis PanenKeluarga
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

    // Helper Cek Peran
    public function isPetani(): bool 
    { 
        return $this->peran === 'petani'; 
    }
    
    public function isKoordinator(): bool 
    { 
        return $this->peran === 'koordinator'; 
    }
    
    public function isKonsumen(): bool 
    { 
        return $this->peran === 'konsumen'; 
    }
    
    public function isAdmin(): bool 
    { 
        return $this->peran === 'admin'; 
    }
}