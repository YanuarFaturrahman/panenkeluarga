<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_sesi_id')->constrained('peserta_sesi')->cascadeOnDelete();
            $table->string('kode_transaksi', 30)->unique();
            $table->unsignedInteger('jumlah_bayar');
            $table->unsignedInteger('alokasi_subsidi')->default(0); // otomatis dihitung
            $table->string('metode_bayar', 50)->default('transfer_manual');
            $table->enum('status_pembayaran', ['menunggu', 'lunas', 'gagal'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};