<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['pemasukan', 'pengeluaran'])->comment('Jenis transaksi');
            $table->decimal('jumlah', 12, 2)->comment('Jumlah uang transaksi');
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->comment('User yang membuat transaksi');
            $table->string('kategori')->nullable()->comment('Kategori transaksi: Penjualan, Pembelian, dll');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
