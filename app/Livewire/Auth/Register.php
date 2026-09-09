<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')]
class Register extends Component
{
    use WithFileUploads;

    public string $peran = 'konsumen';
    public string $name = '';
    public string $identitas = ''; // Nomor HP atau Email
    public string $wilayah_id = '';
    public string $password = '';
    public $dokumen = null; // File Lampiran Verifikasi

    #[Computed]
    public function wilayahList()
    {
        return Wilayah::select('id', 'nama_rt', 'nama_rw', 'kelurahan', 'kecamatan')
            ->orderBy('kelurahan')
            ->orderBy('nama_rw')
            ->orderBy('nama_rt')
            ->get();
    }

    public function register()
    {
        $this->peran = strtolower($this->peran);

        $rules = [
            'peran'      => ['required', 'in:konsumen,petani,koordinator'],
            'name'       => ['required', 'string', 'max:255'],
            'identitas'  => ['required', 'string', 'max:255'],
            'wilayah_id' => ['required', 'exists:wilayah,id'],
            'password'   => ['required', 'string', 'min:8'],
        ];

        // Validasi wajib upload dokumen khusus Petani & Koordinator
        if (in_array($this->peran, ['petani', 'koordinator'])) {
            $rules['dokumen'] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'];
        }

        $this->validate($rules);

        $isEmail = filter_var($this->identitas, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $email = $this->identitas;
            $nomorHp = null;

            if (User::where('email', $email)->exists()) {
                $this->addError('identitas', 'Email ini sudah terdaftar.');
                return;
            }
        } else {
            $phoneDigits = preg_replace('/\D+/', '', $this->identitas);

            if (strlen($phoneDigits) < 9) {
                $this->addError('identitas', 'Nomor HP atau Email tidak valid.');
                return;
            }

            $nomorHp = $phoneDigits;
            $email = $phoneDigits . '@panenkeluarga.id';

            if (User::where('nomor_hp', $nomorHp)->orWhere('email', $email)->exists()) {
                $this->addError('identitas', 'Nomor HP ini sudah terdaftar.');
                return;
            }
        }

        // Simpan File Dokumen ke Disk 'public'
        $dokumenPath = null;
        if ($this->dokumen) {
            $dokumenPath = $this->dokumen->store('dokumen-verifikasi', 'public');
        }

        // Tentukan Status Verifikasi
        $statusVerifikasi = in_array($this->peran, ['petani', 'koordinator']) ? 'pending' : 'terverifikasi';

        $user = User::create([
            'peran'             => $this->peran,
            'name'              => $this->name,
            'nomor_hp'          => $nomorHp,
            'email'             => $email,
            'wilayah_id'        => $this->wilayah_id,
            'password'          => Hash::make($this->password),
            'dokumen'           => $dokumenPath,
            'status_verifikasi' => $statusVerifikasi,
        ]);

        // Jika Petani atau Koordinator: Redirect ke Login dengan notifikasi
        if ($statusVerifikasi === 'pending') {
            session()->flash('sukses', 'Pendaftaran berhasil! Akun Anda sedang menunggu verifikasi oleh Admin.');
            return redirect()->route('login');
        }

        // Jika Konsumen: Auto-Login
        Auth::login($user);

        return redirect()->route('beranda');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->title('Buat Akun Baru — PanenKeluarga');
    }
}