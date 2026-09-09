<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitikPengambilan extends Model
{
    protected $table = 'titik_pengambilan';
    protected $fillable = ['wilayah_id', 'koordinator_id', 'nama_lokasi', 'alamat', 'jam_operasional'];

    public function wilayah() { return $this->belongsTo(Wilayah::class); }
    public function koordinator() { return $this->belongsTo(User::class, 'koordinator_id'); }
    public function sesiGroupBuying() { return $this->hasMany(SesiGroupBuying::class); }
}