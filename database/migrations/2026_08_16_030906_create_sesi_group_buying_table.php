<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_group_buying', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('koordinator_id')->constrained('users')->cascadeOnDelete();
            
            // Cukup simpan kolom wilayah_id (tanpa ->foreign())
            $table->char('wilayah_id', 10)->index();

            $table->foreignId('titik_pengambilan_id')
                  ->nullable()
                  ->constrained('titik_pengambilan')
                  ->nullOnDelete();
                
            $table->unsignedInteger('kuota_minimum');
            $table->unsignedInteger('jumlah_terkumpul')->default(0);
            $table->unsignedInteger('harga_satuan');
            $table->dateTime('tenggat_waktu');
            $table->enum('status', ['berjalan', 'kuota_tercapai', 'selesai', 'dibatalkan'])
                  ->default('berjalan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_group_buying');
    }
};