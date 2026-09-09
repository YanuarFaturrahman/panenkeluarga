<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SesiGroupBuying extends Model
{
    protected $table = 'sesi_group_buying';

    protected $fillable = [
        'produk_id',
        'koordinator_id',
        'wilayah_id',
        'titik_pengambilan_id',
        'kuota_minimum',
        'jumlah_terkumpul',
        'harga_satuan',
        'tenggat_waktu',
        'status',
    ];

    protected $casts = [
        'tenggat_waktu' => 'datetime',
        'kuota_minimum' => 'integer',
        'jumlah_terkumpul' => 'integer',
        'harga_satuan' => 'integer',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function titikPengambilan()
    {
        return $this->belongsTo(TitikPengambilan::class);
    }

    public function peserta()
    {
        return $this->hasMany(PesertaSesi::class, 'sesi_id');
    }

    public function getPersentaseKuotaAttribute(): int
    {
        if (!$this->kuota_minimum || $this->kuota_minimum <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->jumlah_terkumpul / $this->kuota_minimum) * 100));
    }

    public function getSisaWaktuAttribute(): string
    {
        if (!$this->tenggat_waktu) {
            return 'Belum diatur';
        }

        $now = Carbon::now();

        if ($this->tenggat_waktu->isPast()) {
            return 'Berakhir';
        }

        // Cast ke (int) agar angka desimal tidak tampil di interface
        $diffInDays = (int) $now->diffInDays($this->tenggat_waktu);

        if ($diffInDays >= 1) {
            return $diffInDays . ' hari lagi';
        }

        $diffInHours = (int) $now->diffInHours($this->tenggat_waktu);

        if ($diffInHours >= 1) {
            return $diffInHours . ' jam lagi';
        }

        $diffInMinutes = (int) $now->diffInMinutes($this->tenggat_waktu);

        return max(1, $diffInMinutes) . ' menit lagi';
    }

    /**
     * Cek apakah kuota sudah tercapai, lalu ubah status otomatis.
     * Dipanggil setiap ada peserta baru bergabung (lihat Livewire GabungSesi).
     */
    public function cekDanKonfirmasiKuota(): void
    {
        if ($this->jumlah_terkumpul >= $this->kuota_minimum && $this->status === 'berjalan') {
            $this->update(['status' => 'kuota_tercapai']);
        }
    }
}