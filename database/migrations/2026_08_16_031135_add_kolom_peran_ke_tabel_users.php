<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('peran', ['konsumen', 'petani', 'koordinator', 'admin'])
                ->default('konsumen')->after('email');
            $table->string('nomor_hp', 20)->nullable()->after('peran');
            $table->foreignId('wilayah_id')->nullable()->after('nomor_hp')
                ->constrained('wilayah')->nullOnDelete();
            $table->enum('status_verifikasi', ['pending', 'terverifikasi', 'ditolak'])
                ->default('terverifikasi')->after('wilayah_id');
            $table->string('foto_profil')->nullable()->after('status_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['wilayah_id']);
            $table->dropColumn(['peran', 'nomor_hp', 'wilayah_id', 'status_verifikasi', 'foto_profil']);
        });
    }
};