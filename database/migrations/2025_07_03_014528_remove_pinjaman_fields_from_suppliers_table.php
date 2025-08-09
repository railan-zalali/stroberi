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
        Schema::table('suppliers', function (Blueprint $table) {
            // Simpan data pinjaman sebelum menghapus kolom
            // Ini akan dilakukan di seeder atau command terpisah
            
            // Hapus kolom total_pinjaman dan total_pembayaran
            $table->dropColumn(['total_pinjaman', 'total_pembayaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Kembalikan kolom total_pinjaman dan total_pembayaran
            $table->decimal('total_pinjaman', 12, 2)->default(0)->comment('Total pinjaman dari supplier');
            $table->decimal('total_pembayaran', 12, 2)->default(0)->comment('Total pembayaran ke supplier');
        });
    }
};
