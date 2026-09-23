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
        Schema::create('subsidi_nutrisi', function (Blueprint $table) {
            $table->id();

            // Simpan kolom wilayah_id sebagai char(10) dengan indeks
            // Tanpa foreign key constraint fisik agar tidak konflik dengan tabel paket laravolt
            $table->char('wilayah_id', 10)->index();

            $table->foreignId('transaksi_id')
                  ->nullable()
                  ->constrained('transaksi')
                  ->nullOnDelete();

            $table->unsignedInteger('jumlah_dialokasikan')->default(0);
            $table->unsignedInteger('jumlah_disalurkan')->default(0);
            $table->unsignedInteger('jumlah_anak_penerima')->default(0);

            $table->enum('status', ['terkumpul', 'disalurkan'])->default('terkumpul');
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subsidi_nutrisi');
    }
};