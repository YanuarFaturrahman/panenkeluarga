<?php

namespace App\Http\Controllers;

class HalamanVerifikasiController extends Controller
{
    public function __invoke()
    {
        return view('menunggu-verifikasi');
    }
}