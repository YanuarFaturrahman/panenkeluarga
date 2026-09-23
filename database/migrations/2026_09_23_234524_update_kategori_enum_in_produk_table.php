<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Untuk MySQL / MariaDB (Laragon/XAMPP)
        DB::statement("ALTER TABLE produk MODIFY COLUMN kategori ENUM('sayur', 'buah', 'protein', 'karbohidrat') NOT NULL DEFAULT 'sayur'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE produk MODIFY COLUMN kategori ENUM('sayur', 'buah', 'protein') NOT NULL DEFAULT 'sayur'");
    }
};