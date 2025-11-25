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
        Schema::table('strawberis', function (Blueprint $table) {
            $table->decimal('stok_terkunci', 10, 2)->default(0)->after('stok_adjustment')->comment('Stok yang sedang dalam proses penjualan');
            $table->boolean('is_locked')->default(false)->after('is_posted')->comment('Apakah stok sedang dikunci untuk transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('strawberis', function (Blueprint $table) {
            $table->dropColumn(['stok_terkunci', 'is_locked']);
        });
    }
};