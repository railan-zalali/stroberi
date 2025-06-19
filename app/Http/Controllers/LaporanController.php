<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

class LaporanController extends Controller
{
    public function index()
    {
        $laporans = Laporan::orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(10);
        return view('laporan.index', compact('laporans'));
    }

    public function create()
    {
        $bulan = Carbon::now()->format('F');
        $tahun = Carbon::now()->year;
        return view('laporan.create', compact('bulan', 'tahun'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string',
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Get first and last day of month
        $startDate = Carbon::parse("$tahun-$bulan-01")->startOfMonth();
        $endDate = Carbon::parse("$tahun-$bulan-01")->endOfMonth();

        // Calculate totals
        $totalPemasukan = Transaksi::where('jenis', 'pemasukan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $totalPengeluaran = Transaksi::where('jenis', 'pengeluaran')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $laba = $totalPemasukan - $totalPengeluaran;

        // Create report
        $laporan = new Laporan([
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_pemasukan' => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'laba' => $laba,
            'user_id' => Auth::id(),
        ]);

        $laporan->save();

        return redirect()->route('laporan.show', $laporan->id)
            ->with('success', 'Laporan berhasil dibuat');
    }

    public function show(Laporan $laporan)
    {
        $bulan = $laporan->bulan;
        $tahun = $laporan->tahun;

        // Get first and last day of month
        $startDate = Carbon::parse("$tahun-$bulan-01")->startOfMonth();
        $endDate = Carbon::parse("$tahun-$bulan-01")->endOfMonth();

        // Get transactions for this period
        $transaksis = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('laporan.show', compact('laporan', 'transaksis'));
    }

    public function exportPdf(Laporan $laporan)
    {
        $bulan = $laporan->bulan;
        $tahun = $laporan->tahun;

        // Get first and last day of month
        $startDate = Carbon::parse("$tahun-$bulan-01")->startOfMonth();
        $endDate = Carbon::parse("$tahun-$bulan-01")->endOfMonth();

        // Get transactions for this period
        $transaksis = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        $pdf = PDF::loadView('laporan.pdf', compact('laporan', 'transaksis'));
        return $pdf->download("laporan-$bulan-$tahun.pdf");
    }
}
