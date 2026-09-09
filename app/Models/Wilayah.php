<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';
    protected $fillable = ['nama_rt', 'nama_rw', 'kelurahan', 'kecamatan'];

    public function getNamaLengkapAttribute(): string
    {
        return "RT {$this->nama_rt} / RW {$this->nama_rw} — Kel. {$this->kelurahan}";
    }

    public function warga() { return $this->hasMany(User::class); }
    public function sesiGroupBuying() { return $this->hasMany(SesiGroupBuying::class); }
    public function titikPengambilan() { return $this->hasMany(TitikPengambilan::class); }
}