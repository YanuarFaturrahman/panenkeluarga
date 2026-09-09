<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubsidiNutrisi extends Model
{
    protected $table = 'subsidi_nutrisi';
    protected $fillable = [
        'wilayah_id', 'transaksi_id', 'jumlah_dialokasikan',
        'jumlah_disalurkan', 'jumlah_anak_penerima', 'status', 'catatan',
    ];

    public function wilayah() { return $this->belongsTo(Wilayah::class); }
    public function transaksi() { return $this->belongsTo(Transaksi::class); }
}