<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanWilayahDisetujuiNotification extends Notification
{
    use Queueable;

    public $namaWilayah;
    public $pesan;

    public function __construct($namaWilayah, $pesan = null)
    {
        $this->namaWilayah = $namaWilayah;
        $this->pesan = $pesan ?? "Pengajuan wilayah {$namaWilayah} telah disetujui oleh Admin.";
    }

    public function via($notifiable)
    {
        return ['database']; // Wajib 'database' agar masuk ke tabel notifications
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Persetujuan Wilayah',
            'pesan' => $this->pesan,
            'nama_wilayah' => $this->namaWilayah,
            'status' => 'disetujui',
            'url' => route('admin.verifikasi'), // sesuaikan dengan route tujuan jika ada
        ];
    }
}