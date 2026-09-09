<?php
namespace App\Notifications;

use App\Models\PesertaSesi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusPesananUpdated extends Notification
{
    use Queueable;

    public $peserta;
    public $pesan;

    public function __construct(PesertaSesi $peserta, string $pesan)
    {
        $this->peserta = $peserta;
        $this->pesan = $pesan;
    }

    public function via($notifiable): array
    {
        return ['database']; // Menyimpan notifikasi ke dalam database
    }

    public function toArray($notifiable): array
    {
        return [
            'pesan' => $this->pesan,
            'peserta_id' => $this->peserta->id,
            'komoditas' => $this->peserta->sesi->produk->nama_komoditas ?? 'Komoditas',
            'status' => $this->peserta->status,
        ];
    }
}
