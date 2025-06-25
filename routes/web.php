<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StrawberiController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
    // Export routes
    Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    Route::get('/transaksi/export-csv', [TransaksiController::class, 'exportCsv'])->name('transaksi.export-csv');
    Route::get('/transaksi/export-pdf', [TransaksiController::class, 'exportPdf'])->name('transaksi.export-pdf');
    Route::get('/transaksi/export/month/{year}/{month}', [TransaksiController::class, 'exportMonth'])->name('transaksi.export.month');
    Route::get('/transaksi/export/year/{year}', [TransaksiController::class, 'exportYear'])->name('transaksi.export.year');

    Route::post('/supplier/{supplier}/update-pembayaran', [SupplierController::class, 'updatePembayaran'])->name('supplier.update-pembayaran');


    // Laporan Routes
    Route::get('/laporan/keuangan', [LaporanController::class, 'keuangan'])->name('laporan.keuangan');
    Route::get('/laporan/stok', [LaporanController::class, 'stok'])->name('laporan.stok');
    Route::get('/laporan/supplier', [LaporanController::class, 'supplier'])->name('laporan.supplier');

    Route::get('/laporan/{laporan}/download-pdf', [LaporanController::class, 'downloadPdf'])->name('laporan.download-pdf');

    Route::get('/laporan/export-keuangan', [LaporanController::class, 'exportKeuangan'])->name('laporan.export-keuangan');
    Route::get('/laporan/export-stok', [LaporanController::class, 'exportStok'])->name('laporan.export-stok');
    Route::get('/laporan/export-supplier', [LaporanController::class, 'exportSupplier'])->name('laporan.export-supplier');
    // Resource routes
    Route::resource('transaksi', TransaksiController::class);
    Route::resource('strawberi', StrawberiController::class);
    Route::resource('supplier', SupplierController::class);
    Route::resource('laporan', LaporanController::class);
});
