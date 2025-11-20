<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StrawberiController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Strawberi routes
    // Penjualan global (stok gabungan) - definisikan sebelum resource agar tidak tertangkap oleh wildcard {strawberi}
    Route::get('/strawberi/sell-global', [StrawberiController::class, 'sellGlobalForm'])->name('strawberi.sell-global.form');
    Route::post('/strawberi/sell-global', [StrawberiController::class, 'sellGlobalStore'])->name('strawberi.sell-global.store');

    // Resource dan aksi jual per item
    Route::resource('strawberi', StrawberiController::class);
    Route::post('/strawberi/{strawberi}/sell', [StrawberiController::class, 'sell'])->name('strawberi.sell');
    Route::post('/strawberi/{strawberi}/adjust', [StrawberiController::class, 'adjust'])->name('strawberi.adjust');

    // Supplier routes
    Route::resource('supplier', SupplierController::class);
    // Ubah route pembayaran menjadi pengembalian
    Route::post('/supplier/{supplier}/pengembalian', [SupplierController::class, 'updatePengembalian'])->name('supplier.pengembalian');

    // Tambahkan route untuk pinjaman baru
    Route::post('/supplier/{supplier}/pinjaman', [SupplierController::class, 'createPinjaman'])->name('supplier.pinjaman');

    // Selesaikan transaksi kredit supplier (posting stok & pembukuan)
    Route::post('/supplier/{supplier}/selesaikan-transaksi', [SupplierController::class, 'finishTransactions'])->name('supplier.finish');

    // Export routes (only Excel and PDF) — place BEFORE resource to avoid wildcard collision
    Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    Route::get('/transaksi/export/pdf', [TransaksiController::class, 'exportPdf'])->name('transaksi.export.pdf');
    Route::get('/transaksi/export/month/{year}/{month}', [TransaksiController::class, 'exportMonth'])->name('transaksi.export.month');
    Route::get('/transaksi/export/year/{year}', [TransaksiController::class, 'exportYear'])->name('transaksi.export.year');

    // Transaksi routes
    Route::resource('transaksi', TransaksiController::class);

    // Laporan routes
    Route::get('/laporan/keuangan', [LaporanController::class, 'keuangan'])->name('laporan.keuangan');
    Route::get('/laporan/stok', [LaporanController::class, 'stok'])->name('laporan.stok');
    Route::get('/laporan/supplier', [LaporanController::class, 'supplier'])->name('laporan.supplier');
    Route::get('/laporan/{laporan}/download-pdf', [LaporanController::class, 'downloadPdf'])->name('laporan.download-pdf');
    Route::get('/laporan/export/keuangan', [LaporanController::class, 'exportKeuangan'])->name('laporan.export.keuangan');
    Route::get('/laporan/export/stok', [LaporanController::class, 'exportStok'])->name('laporan.export.stok');
    Route::get('/laporan/export/supplier', [LaporanController::class, 'exportSupplier'])->name('laporan.export.supplier');
    Route::resource('laporan', LaporanController::class);
});
