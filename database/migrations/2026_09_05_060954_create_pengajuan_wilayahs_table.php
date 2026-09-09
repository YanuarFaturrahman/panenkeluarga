<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_wilayahs', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (Koordinator yang mengajukan)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Data Wilayah yang diajukan
            $table->string('rt', 10);
            $table->string('rw', 10);
            $table->string('kelurahan');
            $table->string('kecamatan');

            // Status Pengajuan: pending, approved, atau rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Catatan opsional dari admin (misal alasan jika ditolak)
            $table->text('catatan_admin')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_wilayahs');
    }
};