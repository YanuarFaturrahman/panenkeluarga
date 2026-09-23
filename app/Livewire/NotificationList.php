<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Schema;

class NotificationList extends Component
{
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function terimaPencairan($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if (!$notification) return;

        $data = $notification->data;
        $petaniId = $data['petani_id'] ?? null;

        if ($petaniId && Schema::hasColumn('transaksi', 'status_pencairan')) {
            // Update status transaksi milik petani terkait menjadi 'dicairkan'
            Transaksi::whereHas('pesertaSesi.sesi.produk', fn ($q) => $q->where('petani_id', $petaniId))
                ->where('status_pembayaran', 'lunas')
                ->where('status_pencairan', 'menunggu')
                ->update(['status_pencairan' => 'dicairkan']);
        }

        // Update data array di notifikasi
        $data['status'] = 'disetujui';
        $notification->update(['data' => $data]);
        $notification->markAsRead();
    }

    public function tolakPencairan($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if (!$notification) return;

        $data = $notification->data;
        $data['status'] = 'ditolak';
        $notification->update(['data' => $data]);
        $notification->markAsRead();
    }

    public function render()
    {
        return view('livewire.notification-list');
    }
}