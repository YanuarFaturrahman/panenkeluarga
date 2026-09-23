<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanPencairanNotification extends Notification
{
    use Queueable;

    public function __construct(
        public array $dataPencairan
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pencairan',
            'title' => 'Pengajuan Pencairan Dana',
            'message' => "{$this->dataPencairan['petani_nama']} mengajukan pencairan Rp" . number_format((float)$this->dataPencairan['nominal'], 0, ',', '.') . " via {$this->dataPencairan['metode']} ({$this->dataPencairan['nomor_rekening']} a.n {$this->dataPencairan['nama_pemilik']}).",
            'petani_id' => $this->dataPencairan['petani_id'],
            'nominal' => $this->dataPencairan['nominal'],
            'status' => 'pending', // pending, disetujui, ditolak
        ];
    }
}