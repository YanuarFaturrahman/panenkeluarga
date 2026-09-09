<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // Ubah ke 'produk' (tanpa s) sesuai tabel yang ada di database kamu
    protected $table = 'produk';

    protected $fillable = [
        'petani_id',
        'nama_komoditas',
        'kategori',
        'satuan',
        'harga',
        'estimasi_stok',
        'estimasi_tanggal_panen',
        'deskripsi',
        'foto',
        'status',
    ];

    protected $casts = [
        'estimasi_tanggal_panen' => 'date',
    ];

    protected static function booted()
    {
        static::deleting(function ($produk) {
            $produk->sesiGroupBuying()->delete();
            $produk->ulasan()->delete();
        });
    }

    public function petani()
    {
        return $this->belongsTo(User::class, 'petani_id');
    }

    public function sesiGroupBuying()
    {
        return $this->hasMany(SesiGroupBuying::class, 'produk_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'produk_id');
    }

    public function getRataRatingAttribute(): float
    {
        return round($this->ulasan()->avg('rating') ?? 0, 1);
    }
}