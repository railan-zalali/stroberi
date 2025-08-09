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
        Schema::create('strawberis', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 20)->comment('segar, beku');
            $table->string('grade', 10)->nullable()->comment('A, B, C');
            $table->decimal('jumlah', 8, 2)->comment('dalam kg');
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
     * 
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('strawberis');
    }
};
