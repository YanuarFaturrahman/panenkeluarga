<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\Wilayah;
use Livewire\Livewire;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response
        ->assertOk();
});

test('consumer can register with valid wilayah', function () {
    $wilayah = Wilayah::create([
        'nama_rt' => '05',
        'nama_rw' => '03',
        'kelurahan' => 'Cigadung',
        'kecamatan' => 'Subang',
    ]);

    Livewire::test(Register::class)
        ->set('peran', 'konsumen')
        ->set('name', 'Test Konsumen')
        ->set('nomor_hp', '081200000001')
        ->set('email', 'konsumen.test@example.com')
        ->set('wilayah_id', $wilayah->id)
        ->set('password', 'password123')
        ->call('register')
        ->assertRedirect(route('beranda', absolute: false));

    $this->assertDatabaseHas('users', [
        'email' => '081200000001@panenkeluarga.id',
        'peran' => 'konsumen',
        'wilayah_id' => $wilayah->id,
        'status_verifikasi' => 'terverifikasi',
    ]);
});

test('new users can register', function () {
    $wilayah = Wilayah::create([
        'nama_rt' => '04',
        'nama_rw' => '02',
        'kelurahan' => 'Cibogo',
        'kecamatan' => 'Subang',
    ]);

    $component = Livewire::test(Register::class)
        ->set('peran', 'konsumen')
        ->set('name', 'Test User')
        ->set('nomor_hp', '081200000002')
        ->set('email', 'test@example.com')
        ->set('wilayah_id', $wilayah->id)
        ->set('password', 'password123');

    $component->call('register');

    $component->assertRedirect(route('beranda', absolute: false));

    $this->assertAuthenticated();
});
