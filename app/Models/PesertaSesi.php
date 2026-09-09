<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaSesi extends Model
{
    protected $table = 'peserta_sesi';
    protected $fillable = ['sesi_id', 'konsumen_id', 'jumlah_pesanan', 'subtotal', 'status'];

    public function sesi() { return $this->belongsTo(SesiGroupBuying::class, 'sesi_id'); }
    public function konsumen() { return $this->belongsTo(User::class, 'konsumen_id'); }
    public function transaksi() { return $this->hasOne(Transaksi::class); }
    public function ulasan() { return $this->hasOne(Ulasan::class); }
}