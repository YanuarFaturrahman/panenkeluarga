<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $table = 'ulasan';
    protected $fillable = ['peserta_sesi_id', 'konsumen_id', 'produk_id', 'rating', 'komentar'];

    public function konsumen() { return $this->belongsTo(User::class, 'konsumen_id'); }
    public function produk() { return $this->belongsTo(Produk::class); }
}