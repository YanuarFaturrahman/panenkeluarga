<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Verifikasi extends Component
{
    public string $tab = 'pending'; // 'pending', 'terverifikasi', 'ditolak'

    public function setujui(int $userId): void
    {
        User::whereKey($userId)->update(['status_verifikasi' => 'terverifikasi']);
        session()->flash('sukses', 'Akun berhasil diverifikasi.');
    }

    public function tolak(int $userId): void
    {
        User::whereKey($userId)->update(['status_verifikasi' => 'ditolak']);
        session()->flash('sukses', 'Akun pendaftaran ditolak.');
    }

    public function render()
    {
        // Menggunakan relasi 'village' dari Laravolt beserta eager loading district, city, dan province
        $users = User::with(['village.district.city.province'])
            ->whereIn('peran', ['petani', 'koordinator'])
            ->where('status_verifikasi', $this->tab)
            ->latest()
            ->get();

        $jumlahPending = User::whereIn('peran', ['petani', 'koordinator'])
            ->where('status_verifikasi', 'pending')
            ->count();

        return view('livewire.admin.verifikasi', compact('users', 'jumlahPending'))
            ->layout('layouts.app')
            ->title('Verifikasi Akun — PanenKeluarga');
    }
}