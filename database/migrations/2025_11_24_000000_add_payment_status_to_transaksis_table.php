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
            $table->boolean('is_paid')->default(false)->after('is_pinjaman')->comment('Status pembayaran');
            $table->timestamp('paid_at')->nullable()->after('is_paid')->comment('Tanggal pembayaran');
            $table->unsignedBigInteger('paid_by')->nullable()->after('paid_at')->comment('ID user yang melakukan pembayaran');
            
            // Foreign key constraint
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['paid_by']);
            $table->dropColumn(['is_paid', 'paid_at', 'paid_by']);
        });
    }
};