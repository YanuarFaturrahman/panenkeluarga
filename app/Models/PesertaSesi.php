<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaSesi extends Model
{
    use HasFactory;

    protected $table = 'peserta_sesi';

    protected $fillable = [
        'sesi_id',
        'konsumen_id',
        'jumlah_pesanan',
        'subtotal',
        'status',
    ];

    public function sesi()
    {
        return $this->belongsTo(SesiGroupBuying::class, 'sesi_id');
    }

    public function konsumen()
    {
        return $this->belongsTo(User::class, 'konsumen_id');
    }

    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'peserta_sesi_id');
    }

    public function ulasan()
    {
        return $this->hasOne(Ulasan::class, 'peserta_sesi_id');
    }
}