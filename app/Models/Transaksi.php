<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory;

    public const PERSEN_SUBSIDI = 2;

    protected $table = 'transaksi';

    protected $guarded = [];

    /**
     * Relasi ke model User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model PesertaSesi
     */
    public function pesertaSesi(): BelongsTo
    {
        return $this->belongsTo(PesertaSesi::class, 'peserta_sesi_id');
    }
}