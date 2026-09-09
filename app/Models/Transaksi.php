<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    public const PERSEN_SUBSIDI = 2;

    protected $table = 'transaksi';

    protected $guarded = [];

    public function pesertaSesi()
    {
        return $this->belongsTo(PesertaSesi::class, 'peserta_sesi_id');
    }
}