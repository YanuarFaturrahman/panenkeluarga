<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petani_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_komoditas');
            $table->enum('kategori', ['sayur', 'buah', 'protein']);
            $table->string('satuan', 30);
            $table->unsignedInteger('harga');
            $table->unsignedInteger('estimasi_stok');
            $table->date('estimasi_tanggal_panen');
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['draft', 'aktif', 'nonaktif'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};