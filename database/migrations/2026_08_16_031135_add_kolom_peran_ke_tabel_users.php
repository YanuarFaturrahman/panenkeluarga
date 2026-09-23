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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom peran jika belum ada
            if (!Schema::hasColumn('users', 'peran')) {
                $table->string('peran')->default('pembeli')->after('email');
            }

            // Menambahkan kolom nomor_hp
            if (!Schema::hasColumn('users', 'nomor_hp')) {
                $table->string('nomor_hp')->nullable()->after('peran');
            }

            // Menambahkan kolom village_code / wilayah_id jika belum ada
            if (!Schema::hasColumn('users', 'village_code')) {
                $table->char('village_code', 10)->nullable()->index()->after('nomor_hp');
            }

            // Menambahkan status_verifikasi jika dibutuhkan oleh UserSeeder
            if (!Schema::hasColumn('users', 'status_verifikasi')) {
                $table->string('status_verifikasi')->default('terverifikasi')->after('village_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['peran', 'nomor_hp', 'village_code', 'status_verifikasi']);
        });
    }
};