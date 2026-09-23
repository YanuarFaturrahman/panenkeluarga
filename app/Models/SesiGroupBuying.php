<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class SesiGroupBuying extends Model
{
    use HasFactory;

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

    /**
     * Accessor untuk menghitung SISA STOK secara dinamis.
     * Mengurangi stok induk produk dengan jumlah_terkumpul pada sesi ini.
     */
    public function getStokSisaAttribute(): int
    {
        $stokAwal = $this->produk->stok ?? $this->produk->estimasi_stok ?? 0;
        $sisa = $stokAwal - ($this->jumlah_terkumpul ?? 0);

        return max(0, $sisa);
    }

    /**
     * Accessor pendukung untuk alias stok sisa
     */
    public function getEstimasiStokAttribute(): int
    {
        return $this->stok_sisa;
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Relasi ke Koordinator (User)
     */
    public function koordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    /**
     * Alias relasi user agar dipanggil $sesi->user atau with('user') tidak error.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'koordinator_id');
    }

    /**
     * Relasi Wilayah dengan fallback aman untuk berbagai package laravel-indonesia
     */
    public function wilayah(): BelongsTo
    {
        if (class_exists(\Laravolt\Indonesia\Models\Village::class)) {
            return $this->belongsTo(\Laravolt\Indonesia\Models\Village::class, 'wilayah_id', 'code');
        }
        
        if (class_exists(\App\Models\Village::class)) {
            return $this->belongsTo(\App\Models\Village::class, 'wilayah_id', 'code');
        }

        return $this->belongsTo(User::class, 'wilayah_id', 'village_code');
    }

    public function titikPengambilan(): BelongsTo
    {
        return $this->belongsTo(TitikPengambilan::class, 'titik_pengambilan_id');
    }

    public function peserta(): HasMany
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
     */
    /**
 * Cek apakah kuota minimum peserta (keluarga) sudah terpenuhi.
 */
public function cekDanKonfirmasiKuota(): void
{
    // Hitung jumlah transaksi / peserta unik yang sudah berpartisipasi
    // Menggunakan relasi peserta()
    $jumlahKeluarga = $this->peserta()->count();

    // Kuota tercapai HANYA jika jumlah keluarga/peserta >= kuota_minimum
    if ($jumlahKeluarga >= $this->kuota_minimum && $this->status === 'berjalan') {
        $this->update([
            'status' => 'kuota_tercapai'
        ]);
    }
}
}