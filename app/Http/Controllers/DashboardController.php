<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Strawberi;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current month data
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $pemasukan = Transaksi::where('jenis', 'pemasukan')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $pengeluaran = Transaksi::where('jenis', 'pengeluaran')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $laba = $pemasukan - $pengeluaran;

        // Get stock
        $stokSegar = Strawberi::where('jenis', 'segar')
            ->where('tanggal_kadaluarsa', '>=', Carbon::now())
            ->sum('jumlah');

        $stokBeku = Strawberi::where('jenis', 'beku')
            ->where('tanggal_kadaluarsa', '>=', Carbon::now())
            ->sum('jumlah');

        // Recent transactions
        $recentTransaksis = Transaksi::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Expiring stock
        $expiringStrawberi = Strawberi::where('tanggal_kadaluarsa', '<=', Carbon::now()->addDays(7))
            ->where('tanggal_kadaluarsa', '>=', Carbon::now())
            ->orderBy('tanggal_kadaluarsa')
            ->get();

        return view('dashboard', compact(
            'pemasukan',
            'pengeluaran',
            'laba',
            'stokSegar',
            'stokBeku',
            'recentTransaksis',
            'expiringStrawberi'
        ));
    }
}
