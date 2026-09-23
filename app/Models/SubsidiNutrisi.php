<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravolt\Indonesia\Models\Village;

class SubsidiNutrisi extends Model
{
    use HasFactory;

    protected $table = 'subsidi_nutrisi';

    protected $fillable = [
        'wilayah_id',
        'transaksi_id',
        'jumlah_dialokasikan',
        'jumlah_disalurkan',
        'jumlah_anak_penerima',
        'status',
        'catatan',
    ];

    protected $casts = [
        'jumlah_dialokasikan'  => 'integer',
        'jumlah_disalurkan'    => 'integer',
        'jumlah_anak_penerima' => 'integer',
    ];

    /**
     * Relasi ke model Village Laravolt via 'code' (bukan 'id')
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'wilayah_id', 'code');
    }

    /**
     * Relasi ke model Transaksi
     */
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}