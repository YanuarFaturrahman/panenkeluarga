<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanWilayah extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'pengajuan_wilayahs';

    /**
     * Field yang boleh diisi (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'status',
        'catatan_admin',
    ];

    /**
     * Relasi ke Model User (Koordinator yang mengajukan).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}