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
        Schema::create('strawberis', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['segar', 'beku']);
            $table->decimal('jumlah', 8, 2); // dalam kg
            $table->decimal('harga_beli', 12, 2);
            $table->decimal('harga_jual', 12, 2);
            $table->date('tanggal_masuk');
            $table->date('tanggal_kadaluarsa');
            $table->foreignId('supplier_id')->constrained();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strawberies');
    }
};
