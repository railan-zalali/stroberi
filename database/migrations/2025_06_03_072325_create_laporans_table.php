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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('bulan')->comment('Nama bulan laporan');
            $table->integer('tahun')->comment('Tahun laporan');
            $table->decimal('total_pemasukan', 12, 2)->comment('Total pemasukan dalam periode');
            $table->decimal('total_pengeluaran', 12, 2)->comment('Total pengeluaran dalam periode');
            $table->decimal('laba', 12, 2)->comment('Laba bersih (pemasukan - pengeluaran)');
            $table->string('file_path')->nullable()->comment('Path ke file PDF laporan');
            $table->foreignId('user_id')->constrained()->comment('User yang membuat laporan');
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
        Schema::dropIfExists('laporans');
    }
};
