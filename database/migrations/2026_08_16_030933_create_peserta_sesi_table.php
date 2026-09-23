<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('sesi_group_buying')->cascadeOnDelete();
            $table->foreignId('konsumen_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('jumlah_pesanan');
            $table->unsignedInteger('subtotal');
            
            // Diubah menggunakan string agar tidak dibatasi oleh enum (mencegah error Data Truncated)
            $table->string('status', 50)->default('menunggu_kuota');
            
            $table->timestamps();

            $table->unique(['sesi_id', 'konsumen_id']); // satu konsumen hanya 1 baris per sesi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_sesi');
    }
};