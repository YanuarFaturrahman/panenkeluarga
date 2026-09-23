<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory;

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
        'harga' => 'integer',
        'estimasi_stok' => 'integer',
        'estimasi_tanggal_panen' => 'date',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Produk $produk) {
            $produk->sesiGroupBuying()->delete();
            $produk->ulasan()->delete();
        });
    }

    public function petani(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petani_id');
    }

    public function sesiGroupBuying(): HasMany
    {
        return $this->hasMany(SesiGroupBuying::class, 'produk_id');
    }

    public function ulasan(): HasMany
    {
        return $this->hasMany(Ulasan::class, 'produk_id');
    }

    public function getRataRatingAttribute(): float
    {
        return round((float) ($this->ulasan()->avg('rating') ?? 0), 1);
    }
}