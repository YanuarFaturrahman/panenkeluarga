<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('titik_pengambilan', function (Blueprint $table) {
            $table->id();
            
            // Menggunakan string(10) untuk menyimpan kode desa dari Laravolt Indonesia
            $table->string('wilayah_id', 10);
            $table->foreign('wilayah_id')->references('code')->on('indonesia_villages')->cascadeOnDelete();

            $table->foreignId('koordinator_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_lokasi');
            $table->string('alamat');
            $table->string('jam_operasional', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titik_pengambilan');
    }
};