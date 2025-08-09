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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->decimal('total_pinjaman', 12, 2)->default(0)->comment('Total pinjaman dari supplier');
            $table->decimal('total_pembayaran', 12, 2)->default(0)->comment('Total pembayaran ke supplier');
            $table->string('keterangan')->nullable();
            $table->string('status')->default('aktif')->comment('aktif, nonaktif');
            $table->string('foto')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};
