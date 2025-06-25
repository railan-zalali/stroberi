<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Transaksi;
use App\Models\Strawberi;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Exports\LaporanKeuanganExport;
use App\Exports\LaporanStokExport;
use App\Exports\LaporanSupplierExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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

        // Generate PDF report
        $transaksis = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        $pdf = Pdf::loadView('laporan.pdf', compact('bulan', 'tahun', 'transaksis', 'totalPemasukan', 'totalPengeluaran', 'laba'));
        // $pdf = Pdf::loadView('laporan.pdf', [
        //     'bulan' => $bulan,
        //     'tahun' => $tahun,
        //     'transaksis' => $transaksis,
        //     'totalPemasukan' => $totalPemasukan,
        //     'totalPengeluaran' => $totalPengeluaran,
        //     'laba' => $laba,
        // ]);

        $fileName = "laporan_keuangan_{$bulan}_{$tahun}.pdf";
        $filePath = "reports/{$fileName}";

        Storage::disk('public')->put($filePath, $pdf->output());

        // Create report
        $laporan = new Laporan([
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_pemasukan' => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'laba' => $laba,
            'file_path' => $filePath,
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

        // Get category totals for pie chart
        $pemasukanKategori = Transaksi::where('jenis', 'pemasukan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, sum(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        $pengeluaranKategori = Transaksi::where('jenis', 'pengeluaran')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, sum(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        // Get daily totals for line chart
        $dailyData = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('tanggal, jenis, sum(jumlah) as total')
            ->groupBy('tanggal', 'jenis')
            ->get()
            ->groupBy('tanggal');

        // Format data for charts
        $chartData = [
            'pemasukanKategori' => $pemasukanKategori->pluck('total', 'kategori'),
            'pengeluaranKategori' => $pengeluaranKategori->pluck('total', 'kategori'),
            'harian' => $this->formatDailyData($dailyData, $startDate, $endDate),
        ];

        return view('laporan.show', compact('laporan', 'transaksis', 'chartData'));
    }

    public function destroy(Laporan $laporan)
    {
        if ($laporan->file_path) {
            Storage::disk('public')->delete($laporan->file_path);
        }

        $laporan->delete();

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dihapus');
    }

    public function downloadPdf(Laporan $laporan)
    {
        if ($laporan->file_path) {
            $file = Storage::disk('public')->path($laporan->file_path);
            return response()->download($file);
        }

        return back()->with('error', 'File tidak ditemukan');
    }

    public function keuangan()
    {
        // Default to current month
        $bulan = request('bulan', Carbon::now()->format('m'));
        $tahun = request('tahun', Carbon::now()->year);

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        // Get monthly data for the current year
        $bulananData = Transaksi::whereYear('tanggal', $tahun)
            ->selectRaw('MONTH(tanggal) as bulan, jenis, sum(jumlah) as total')
            ->groupBy('bulan', 'jenis')
            ->get()
            ->groupBy('bulan');

        // Format data for chart
        $monthlyChart = $this->formatMonthlyData($bulananData, $tahun);

        // Get category totals for the selected month
        $pemasukanKategori = Transaksi::where('jenis', 'pemasukan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, sum(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        $pengeluaranKategori = Transaksi::where('jenis', 'pengeluaran')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, sum(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        // Get summary data for the selected month
        $totalPemasukan = Transaksi::where('jenis', 'pemasukan')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $totalPengeluaran = Transaksi::where('jenis', 'pengeluaran')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $laba = $totalPemasukan - $totalPengeluaran;

        // Get transactions for this period
        $transaksis = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('laporan.keuangan', compact(
            'bulan',
            'tahun',
            'transaksis',
            'totalPemasukan',
            'totalPengeluaran',
            'laba',
            'pemasukanKategori',
            'pengeluaranKategori',
            'monthlyChart'
        ));
    }

    public function stok()
    {
        // Filter parameters
        $jenis = request('jenis');
        $status = request('status');
        $supplier_id = request('supplier_id');

        // Build query
        $query = Strawberi::with('supplier');

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        if ($status) {
            if ($status == 'kadaluarsa') {
                $query->where('tanggal_kadaluarsa', '<', now());
            } elseif ($status == 'hampir_kadaluarsa') {
                $query->where('tanggal_kadaluarsa', '>=', now())
                    ->where('tanggal_kadaluarsa', '<=', now()->addDays(7));
            } elseif ($status == 'baik') {
                $query->where('tanggal_kadaluarsa', '>', now()->addDays(7));
            }
        }

        if ($supplier_id) {
            $query->where('supplier_id', $supplier_id);
        }

        $strawberis = $query->orderBy('tanggal_masuk', 'desc')->paginate(10);

        // Get summary data
        $stokSegar = Strawberi::where('jenis', 'segar')
            ->where('tanggal_kadaluarsa', '>=', now())
            ->sum('jumlah');

        $stokBeku = Strawberi::where('jenis', 'beku')
            ->where('tanggal_kadaluarsa', '>=', now())
            ->sum('jumlah');

        $stokKadaluarsa = Strawberi::where('tanggal_kadaluarsa', '<', now())
            ->sum('jumlah');

        $stokHampirKadaluarsa = Strawberi::where('tanggal_kadaluarsa', '>=', now())
            ->where('tanggal_kadaluarsa', '<=', now()->addDays(7))
            ->sum('jumlah');

        // Get supplier data for filter
        $suppliers = Supplier::orderBy('nama')->get();

        // Get monthly stok data for chart
        $monthlyStokData = Strawberi::selectRaw('YEAR(tanggal_masuk) as tahun, MONTH(tanggal_masuk) as bulan, jenis, SUM(jumlah) as total')
            ->whereYear('tanggal_masuk', '>=', now()->subYear()->year)
            ->groupBy('tahun', 'bulan', 'jenis')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::createFromDate($item->tahun, $item->bulan, 1)->format('Y-m');
            });

        // Format data for chart
        $stokChart = $this->formatStokData($monthlyStokData);

        return view('laporan.stok', compact(
            'strawberis',
            'stokSegar',
            'stokBeku',
            'stokKadaluarsa',
            'stokHampirKadaluarsa',
            'suppliers',
            'stokChart'
        ));
    }

    public function supplier()
    {
        // Get all suppliers with their stats
        $suppliers = Supplier::withCount(['strawberis as total_kg' => function ($query) {
            $query->select(DB::raw('SUM(jumlah)'));
        }])
            ->withCount(['strawberis as total_nilai' => function ($query) {
                $query->select(DB::raw('SUM(jumlah * harga_beli)'));
            }])
            ->paginate(10);

        // Get top suppliers by volume
        $topSuppliersByVolume = Supplier::withCount(['strawberis as total_kg' => function ($query) {
            $query->select(DB::raw('SUM(jumlah)'));
        }])
            ->orderBy('total_kg', 'desc')
            ->limit(5)
            ->get();

        // Get top suppliers by value
        $topSuppliersByValue = Supplier::withCount(['strawberis as total_nilai' => function ($query) {
            $query->select(DB::raw('SUM(jumlah * harga_beli)'));
        }])
            ->orderBy('total_nilai', 'desc')
            ->limit(5)
            ->get();

        // Get suppliers with outstanding debt
        $suppliersWithDebt = Supplier::whereRaw('total_pinjaman > total_pembayaran')
            ->orderByRaw('(total_pinjaman - total_pembayaran) DESC')
            ->limit(5)
            ->get();

        return view('laporan.supplier', compact(
            'suppliers',
            'topSuppliersByVolume',
            'topSuppliersByValue',
            'suppliersWithDebt'
        ));
    }

    public function exportKeuangan(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->format('m');
        $tahun = $request->tahun ?? Carbon::now()->year;

        $fileName = "laporan_keuangan_{$bulan}_{$tahun}.xlsx";

        return Excel::download(new LaporanKeuanganExport($bulan, $tahun), $fileName);
    }

    public function exportStok(Request $request)
    {
        $jenis = $request->jenis;
        $status = $request->status;
        $supplier_id = $request->supplier_id;

        $fileName = "laporan_stok_" . date('Y-m-d') . ".xlsx";

        return Excel::download(new LaporanStokExport($jenis, $status, $supplier_id), $fileName);
    }

    public function exportSupplier()
    {
        $fileName = "laporan_supplier_" . date('Y-m-d') . ".xlsx";

        return Excel::download(new LaporanSupplierExport(), $fileName);
    }

    private function formatDailyData($dailyData, $startDate, $endDate)
    {
        $result = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dayData = $dailyData->get($dateString, collect([]));

            $pemasukan = $dayData->where('jenis', 'pemasukan')->sum('total');
            $pengeluaran = $dayData->where('jenis', 'pengeluaran')->sum('total');

            $result[] = [
                'tanggal' => $currentDate->format('d/m'),
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'laba' => $pemasukan - $pengeluaran
            ];

            $currentDate->addDay();
        }

        return $result;
    }

    private function formatMonthlyData($bulananData, $tahun)
    {
        $result = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthData = $bulananData->get($i, collect([]));

            $pemasukan = $monthData->where('jenis', 'pemasukan')->sum('total');
            $pengeluaran = $monthData->where('jenis', 'pengeluaran')->sum('total');

            $result[] = [
                'bulan' => Carbon::createFromDate($tahun, $i, 1)->format('M'),
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'laba' => $pemasukan - $pengeluaran
            ];
        }

        return $result;
    }

    private function formatStokData($monthlyStokData)
    {
        $result = [];

        foreach ($monthlyStokData as $month => $data) {
            $segar = $data->where('jenis', 'segar')->sum('total');
            $beku = $data->where('jenis', 'beku')->sum('total');

            $result[] = [
                'bulan' => Carbon::parse($month)->format('M Y'),
                'segar' => $segar,
                'beku' => $beku,
                'total' => $segar + $beku
            ];
        }

        return $result;
    }
}
