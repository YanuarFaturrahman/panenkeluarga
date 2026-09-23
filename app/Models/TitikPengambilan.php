<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Indonesia\Models\Village;

class TitikPengambilan extends Model
{
    use HasFactory;

    protected $table = 'titik_pengambilan';

    protected $fillable = [
        'nama_lokasi',
        'alamat',
        'jam_operasional',
        'koordinator_id',
        'wilayah_id',
    ];

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    // Relasi ke tabel Desa Laravolt
    public function village()
    {
        return $this->belongsTo(Village::class, 'wilayah_id', 'code');
    }
}