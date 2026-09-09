<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    // Mengubah nama properti menjadi $login agar lebih fleksibel (bisa email atau HP)
    public string $login = ''; 
    public string $password = '';
    public bool $remember = false;

    public function authenticate(): void
    {
        $this->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Cek apakah input berupa email atau nomor HP
        $fieldType = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nomor_hp';

        $credentials = [
            $fieldType => $this->login,
            'password'  => $this->password,
        ];

        if (! Auth::attempt($credentials, $this->remember)) {
            $this->addError('login', __('auth.failed'));
            return;
        }

        session()->regenerate();

        $this->redirect($this->tujuanRedirect(), navigate: true);
    }

    protected function tujuanRedirect(): string
    {
        $user = auth()->user();

        if (in_array($user->peran, ['petani', 'koordinator']) && ! $user->status_verifikasi) {
            return route('menunggu-verifikasi');
        }

        return match ($user->peran) {
            'petani'      => route('petani.dashboard'),
            'koordinator' => route('koordinator.dashboard'),
            'admin'       => route('admin.dashboard'),
            default       => route('beranda'),
        };
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}