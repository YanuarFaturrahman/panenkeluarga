<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

#[Layout('layouts.guest')]
class Register extends Component
{
    use WithFileUploads;

    public string $peran = 'konsumen';
    public string $name = '';
    public string $identitas = ''; // Nomor HP atau Email
    public string $password = '';
    public $dokumen = null;

    // Properti Pilihan Wilayah
    public string $province_code = '';
    public string $city_code = '';
    public string $district_code = '';
    public string $village_code = '';

    // Data List Dropdown
    public $cities = [];
    public $districts = [];
    public $villages = [];

    // Trigger otomatis ketika Provinsi dipilih
    public function updatedProvinceCode($value)
    {
        $this->cities = City::where('province_code', $value)->orderBy('name')->pluck('name', 'code');
        $this->city_code = '';
        $this->district_code = '';
        $this->village_code = '';
        $this->districts = [];
        $this->villages = [];
    }

    // Trigger otomatis ketika Kabupaten/Kota dipilih
    public function updatedCityCode($value)
    {
        $this->districts = District::where('city_code', $value)->orderBy('name')->pluck('name', 'code');
        $this->district_code = '';
        $this->village_code = '';
        $this->villages = [];
    }

    // Trigger otomatis ketika Kecamatan dipilih
    public function updatedDistrictCode($value)
    {
        $this->villages = Village::where('district_code', $value)->orderBy('name')->pluck('name', 'code');
        $this->village_code = '';
    }

    // Ambil daftar Provinsi
    #[Computed]
    public function provinces()
    {
        return Province::orderBy('name')->pluck('name', 'code');
    }

    public function register()
    {
        $this->peran = strtolower($this->peran);

        $rules = [
            'peran'         => ['required', 'in:konsumen,petani,koordinator'],
            'name'          => ['required', 'string', 'max:255'],
            'identitas'     => ['required', 'string', 'max:255'],
            'province_code' => ['required'],
            'city_code'     => ['required'],
            'district_code' => ['required'],
            'village_code'  => ['required', 'exists:indonesia_villages,code'],
            'password'      => ['required', 'string', 'min:8'],
        ];

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

        $dokumenPath = null;
        if ($this->dokumen) {
            $dokumenPath = $this->dokumen->store('dokumen-verifikasi', 'public');
        }

        $statusVerifikasi = in_array($this->peran, ['petani', 'koordinator']) ? 'pending' : 'terverifikasi';

        $user = User::create([
            'peran'             => $this->peran,
            'name'              => $this->name,
            'nomor_hp'          => $nomorHp,
            'email'             => $email,
            'village_code'      => $this->village_code, // Cukup simpan village_code saja
            'password'          => Hash::make($this->password),
            'dokumen'           => $dokumenPath,
            'status_verifikasi' => $statusVerifikasi,
        ]);

        if ($statusVerifikasi === 'pending') {
            session()->flash('sukses', 'Pendaftaran berhasil! Akun Anda sedang menunggu verifikasi oleh Admin.');
            return redirect()->route('login');
        }

        Auth::login($user);

        return redirect()->route('beranda');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->title('Buat Akun Baru — PanenKeluarga');
    }
}