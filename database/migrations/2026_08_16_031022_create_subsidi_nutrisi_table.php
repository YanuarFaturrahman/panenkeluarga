<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subsidi_nutrisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->constrained('wilayah')->cascadeOnDelete();
            $table->foreignId('transaksi_id')->nullable()->constrained('transaksi')->nullOnDelete();
            $table->unsignedInteger('jumlah_dialokasikan')->default(0);
            $table->unsignedInteger('jumlah_disalurkan')->default(0);
            $table->unsignedInteger('jumlah_anak_penerima')->default(0);
            $table->enum('status', ['terkumpul', 'disalurkan'])->default('terkumpul');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subsidi_nutrisi');
    }
};