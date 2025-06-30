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
            $table->decimal('stok_awal', 10, 2)->default(0)->after('jumlah');
            $table->decimal('stok_terjual', 10, 2)->default(0)->after('stok_awal');
            $table->string('batch_number')->nullable()->after('id');
            $table->decimal('stok_rusak', 10, 2)->default(0)->after('stok_terjual');
            $table->decimal('stok_adjustment', 10, 2)->default(0)->after('stok_rusak');
            $table->text('adjustment_notes')->nullable()->after('keterangan');
            $table->timestamp('last_stock_update')->nullable()->after('adjustment_notes');
        });

        // Create stock movement history table
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('strawberi_id')->constrained()->onDelete('cascade');
            $table->string('type'); // sale, damage, adjustment
            $table->decimal('quantity', 10, 2);
            $table->decimal('stock_before', 10, 2);
            $table->decimal('stock_after', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('strawberis', function (Blueprint $table) {
            $table->dropColumn([
                'stok_awal',
                'stok_terjual',
                'batch_number',
                'stok_rusak',
                'stok_adjustment',
                'adjustment_notes',
                'last_stock_update'
            ]);
        });

        Schema::dropIfExists('stock_movements');
    }
};
