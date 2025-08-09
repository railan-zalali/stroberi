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
            ->get()
            ->sum(function ($strawberi) {
                return $strawberi->stok_tersisa;
            });

        $stokBeku = Strawberi::where('jenis', 'beku')
            ->where('tanggal_kadaluarsa', '>=', Carbon::now())
            ->get()
            ->sum(function ($strawberi) {
                return $strawberi->stok_tersisa;
            });

        // Recent transactions
        $recentTransaksis = Transaksi::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Expiring stock - get items expiring within 7 days
        $expiringStrawberi = Strawberi::where('tanggal_kadaluarsa', '<=', Carbon::now()->addDays(7))
            ->where('tanggal_kadaluarsa', '>=', Carbon::now())
            ->whereRaw('(stok_awal - stok_terjual) > 0') // Hanya yang masih ada stok
            ->orderBy('tanggal_kadaluarsa')
            ->get()
            ->map(function ($strawberi) {
                $daysRemaining = floor(Carbon::now()->diffInDays($strawberi->tanggal_kadaluarsa, false));
                
                // Format days remaining in human-readable way
                if ($daysRemaining < 0) {
                    $strawberi->days_remaining_text = 'Sudah kadaluarsa';
                    $strawberi->is_expired = true;
                } elseif ($daysRemaining == 0) {
                    $strawberi->days_remaining_text = 'Kadaluarsa hari ini';
                    $strawberi->is_expired = false;
                } elseif ($daysRemaining == 1) {
                    $strawberi->days_remaining_text = '1 hari lagi';
                    $strawberi->is_expired = false;
                } else {
                    $strawberi->days_remaining_text = $daysRemaining . ' hari lagi';
                    $strawberi->is_expired = false;
                }
                
                return $strawberi;
            });

        // Monthly financial data for chart (last 6 months)
        $monthlyData = [];
        $monthLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $monthlyPemasukan = Transaksi::where('jenis', 'pemasukan')
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->sum('jumlah');
                
            $monthlyPengeluaran = Transaksi::where('jenis', 'pengeluaran')
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->sum('jumlah');
            
            $monthLabels[] = $date->format('M');
            $monthlyData['pemasukan'][] = $monthlyPemasukan;
            $monthlyData['pengeluaran'][] = $monthlyPengeluaran;
        }

        return view('dashboard', compact(
            'pemasukan',
            'pengeluaran',
            'laba',
            'stokSegar',
            'stokBeku',
            'recentTransaksis',
            'expiringStrawberi',
            'monthlyData',
            'monthLabels'
        ));
    }
}
