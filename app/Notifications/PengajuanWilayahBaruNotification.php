<?php

namespace App\Notifications;

use App\Models\PengajuanWilayah;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanWilayahBaruNotification extends Notification
{
    use Queueable;

    public PengajuanWilayah $pengajuan;

    public function __construct(PengajuanWilayah $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function via(object $notifiable): array
    {
        // Menyimpan notifikasi ke dalam database
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'pengajuan_id' => $this->pengajuan->id,
            'title'        => 'Pengajuan Wilayah Baru',
            'message'      => "{$this->pengajuan->user->name} mengajukan wilayah RT {$this->pengajuan->rt} / RW {$this->pengajuan->rw} — Kel. {$this->pengajuan->kelurahan}",
            'url'          => route('admin.pengajuan-wilayah'),
        ];
    }
}