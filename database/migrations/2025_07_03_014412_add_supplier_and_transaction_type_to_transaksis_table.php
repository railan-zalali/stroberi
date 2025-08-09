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
        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('user_id')->constrained()->nullOnDelete()->comment('Supplier terkait transaksi');
            $table->string('tipe_transaksi')->nullable()->after('kategori')->comment('Tipe transaksi: pinjaman, pembayaran, dll');
            $table->boolean('is_pinjaman')->default(false)->after('tipe_transaksi')->comment('Apakah transaksi ini adalah pinjaman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'tipe_transaksi', 'is_pinjaman']);
        });
    }
};
