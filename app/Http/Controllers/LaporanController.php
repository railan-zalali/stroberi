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
        $tipeTransaksi = $request->input('tipe_transaksi', 'semua');

        // Get first and last day of month
        $startDate = Carbon::parse("$tahun-$bulan-01")->startOfMonth();
        $endDate = Carbon::parse("$tahun-$bulan-01")->endOfMonth();

        // Calculate totals (including loan transactions)
        $totalPemasukan = Transaksi::where('jenis', 'pemasukan')
            ->where(function ($query) {
                $query->where('is_pinjaman', false)
                    ->orWhere(function ($q) {
                        $q->where('is_pinjaman', true)
                            ->where('tipe_transaksi', 'pengembalian'); // Include loan repayments
                    });
            })
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $totalPengeluaran = Transaksi::where('jenis', 'pengeluaran')
            ->where(function ($query) use ($tipeTransaksi) {
                if ($tipeTransaksi === 'biasa') {
                    $query->where('is_pinjaman', false);
                } elseif ($tipeTransaksi === 'pinjaman') {
                    $query->where('is_pinjaman', true)
                        ->where('tipe_transaksi', 'pinjaman');
                } elseif ($tipeTransaksi === 'pengembalian') {
                    $query->where('is_pinjaman', true)
                        ->where('tipe_transaksi', 'pengembalian');
                } else {
                    // Semua transaksi - include both regular and loan transactions
                    $query->where(function ($q) {
                        $q->where('is_pinjaman', false)
                            ->orWhere(function ($subQ) {
                                $subQ->where('is_pinjaman', true)
                                    ->whereIn('tipe_transaksi', ['pinjaman', 'pengembalian']);
                            });
                    });
                }
            })
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $laba = $totalPemasukan - $totalPengeluaran;

        // Generate PDF report (including loan transactions)
        $transaksis = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->where(function ($query) {
                $query->where('is_pinjaman', false)
                    ->orWhere(function ($q) {
                        $q->where('is_pinjaman', true)
                            ->whereIn('tipe_transaksi', ['pinjaman', 'pengembalian']);
                    });
            })
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->orderBy('tanggal', 'asc')
            ->get();

        $kerugianStok = $this->getKerugianStok($startDate, $endDate);

        $pdf = Pdf::loadView('laporan.pdf', compact('bulan', 'tahun', 'transaksis', 'totalPemasukan', 'totalPengeluaran', 'laba', 'kerugianStok'));

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
        $tipeTransaksi = request('tipe_transaksi');

        // Get first and last day of month
        $startDate = Carbon::parse("$tahun-$bulan-01")->startOfMonth();
        $endDate = Carbon::parse("$tahun-$bulan-01")->endOfMonth();

        // Get transactions for this period
        $transaksis = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Get category totals for pie chart
        $pemasukanKategori = Transaksi::where('jenis', 'pemasukan')
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, sum(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        $pengeluaranKategori = Transaksi::where('jenis', 'pengeluaran')
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, sum(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        // Get daily totals for line chart
        $dailyData = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->selectRaw('tanggal, jenis, sum(jumlah) as total')
            ->groupBy('tanggal', 'jenis')
            ->get()
            ->groupBy(fn($item) => $item->tanggal->format('Y-m-d'));

        // Format data for charts
        $chartData = [
            'pemasukanKategori' => $pemasukanKategori->pluck('total', 'kategori'),
            'pengeluaranKategori' => $pengeluaranKategori->pluck('total', 'kategori'),
            'harian' => $this->formatDailyData($dailyData, $startDate, $endDate),
        ];

        $kerugianStok = $this->getKerugianStok($startDate, $endDate);

        return view('laporan.show', compact('laporan', 'transaksis', 'chartData', 'tipeTransaksi', 'kerugianStok'));
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
        if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
            return Storage::disk('public')->download($laporan->file_path);
        }

        return back()->with('error', 'File PDF tidak ditemukan. Silakan hapus dan buat ulang laporan ini.');
    }

    public function keuangan()
    {
        // Default to current month
        $bulan = request('bulan', Carbon::now()->format('m'));
        $tahun = request('tahun', Carbon::now()->year);
        $tipeTransaksi = request('tipe_transaksi');

        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        // Get monthly data for the current year
        $bulananData = Transaksi::whereYear('tanggal', $tahun)
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->selectRaw("MONTH(tanggal) as bulan, jenis, sum(jumlah) as total")
            ->groupBy('bulan', 'jenis')
            ->get()
            ->groupBy('bulan');

        // Format data for chart
        $monthlyChart = $this->formatMonthlyData($bulananData, $tahun);

        // Get category totals for the selected month
        $pemasukanKategori = Transaksi::where('jenis', 'pemasukan')
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, sum(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        $pengeluaranKategori = Transaksi::where('jenis', 'pengeluaran')
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('kategori, sum(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        // Get summary totals — keduanya mengikuti $tipeTransaksi untuk konsistensi
        $totalPemasukan = Transaksi::where('jenis', 'pemasukan')
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $totalPengeluaran = Transaksi::where('jenis', 'pengeluaran')
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        $laba = $totalPemasukan - $totalPengeluaran;

        // Get transactions for this period
        $transaksis = Transaksi::whereBetween('tanggal', [$startDate, $endDate])
            ->where(fn($q) => $this->applyTipeFilter($q, $tipeTransaksi))
            ->where('kategori', '!=', 'Pembelian Strawberi (Pending)')
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
        $query = Strawberi::with('supplier')->where('is_posted', true);

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

        // Get summary data using raw SQL calculations
        $stokSegar = Strawberi::where('is_posted', true)
            ->where('jenis', 'segar')
            ->where('tanggal_kadaluarsa', '>=', now())
            ->selectRaw('SUM(stok_awal - stok_terjual - COALESCE(stok_rusak, 0) + COALESCE(stok_adjustment, 0)) as total')
            ->value('total') ?? 0;

        $stokBeku = Strawberi::where('is_posted', true)
            ->where('jenis', 'beku')
            ->where('tanggal_kadaluarsa', '>=', now())
            ->selectRaw('SUM(stok_awal - stok_terjual - COALESCE(stok_rusak, 0) + COALESCE(stok_adjustment, 0)) as total')
            ->value('total') ?? 0;

        $stokKadaluarsa = Strawberi::where('is_posted', true)
            ->where('tanggal_kadaluarsa', '<', now())
            ->selectRaw('SUM(stok_awal - stok_terjual - COALESCE(stok_rusak, 0) + COALESCE(stok_adjustment, 0)) as total')
            ->value('total') ?? 0;

        $stokHampirKadaluarsa = Strawberi::where('is_posted', true)
            ->where('tanggal_kadaluarsa', '>=', now())
            ->where('tanggal_kadaluarsa', '<=', now()->addDays(7))
            ->selectRaw('SUM(stok_awal - stok_terjual - COALESCE(stok_rusak, 0) + COALESCE(stok_adjustment, 0)) as total')
            ->value('total') ?? 0;

        // Get supplier data for filter
        $suppliers = Supplier::orderBy('nama')->get();

        // Get monthly stok data for chart
        $monthlyStokData = Strawberi::selectRaw("
            YEAR(tanggal_masuk) as tahun,
            MONTH(tanggal_masuk) as bulan,
            jenis,
            SUM(stok_awal - stok_terjual - COALESCE(stok_rusak, 0) + COALESCE(stok_adjustment, 0)) as total
        ")
            ->where('is_posted', true)
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
        // Load all suppliers with strawberis once — used for stats/top lists
        $allSuppliers = Supplier::with(['strawberis'])
            ->get()
            ->map(function ($supplier) {
                $supplier->total_kg = $supplier->strawberis->sum('jumlah');
                $supplier->total_nilai = $supplier->strawberis->sum(fn($s) => $s->jumlah * $s->harga_beli);
                return $supplier;
            });

        // Paginate dari koleksi yang sudah di-map
        $suppliers = new \Illuminate\Pagination\LengthAwarePaginator(
            $allSuppliers->forPage(request('page', 1), 10),
            $allSuppliers->count(),
            10,
            request('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $topSuppliersByVolume = $allSuppliers->sortByDesc('total_kg')->take(5);
        $topSuppliersByValue = $allSuppliers->sortByDesc('total_nilai')->take(5);
        $suppliersWithDebt = $allSuppliers->filter(fn($s) => $s->sisa_pinjaman > 0)->sortByDesc('sisa_pinjaman')->take(5);

        return view('laporan.supplier', compact(
            'suppliers',
            'topSuppliersByVolume',
            'topSuppliersByValue',
            'suppliersWithDebt'
        ));
    }

    public function exportKeuangan(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('m'));
        $tahun = $request->input('tahun', Carbon::now()->year);

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

    /**
     * Hitung kerugian stok (rusak + kadaluarsa) dalam periode tertentu.
     * Batch dianggap "rugi" pada bulan ia masuk (tanggal_masuk dalam range).
     */
    private function getKerugianStok($startDate, $endDate): array
    {
        $batches = Strawberi::where('is_posted', true)
            ->whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->where(fn($q) => $q->where('stok_rusak', '>', 0)
                ->orWhere('tanggal_kadaluarsa', '<', now())
            )
            ->get();

        $detail = [];
        $totalRusakKg = 0;
        $totalNilai = 0;

        foreach ($batches as $b) {
            // stok kadaluarsa = sisa stok yang melewati expired (bukan stok_rusak)
            $stokKadaluarsa = $b->isKadaluarsa()
                ? max(0, $b->stok_awal - $b->stok_terjual - $b->stok_rusak + $b->stok_adjustment)
                : 0;

            $totalQty = $b->stok_rusak + $stokKadaluarsa;
            if ($totalQty <= 0) continue;

            $nilaiRugi = $totalQty * $b->harga_beli;
            $totalRusakKg += $totalQty;
            $totalNilai += $nilaiRugi;

            $detail[] = [
                'batch'           => $b->batch_number,
                'jenis'           => $b->jenis,
                'tgl_masuk'       => $b->tanggal_masuk->format('d/m/Y'),
                'tgl_kadaluarsa'  => $b->tanggal_kadaluarsa->format('d/m/Y'),
                'stok_rusak'      => $b->stok_rusak,
                'stok_kadaluarsa' => $stokKadaluarsa,
                'harga_beli'      => $b->harga_beli,
                'nilai_rugi'      => $nilaiRugi,
            ];
        }

        return [
            'detail'          => $detail,
            'total_rusak_kg'  => $totalRusakKg,
            'total_nilai'     => $totalNilai,
        ];
    }

    /**
     * Terapkan filter tipe transaksi ke query.
     * Satu method menggantikan blok if-elseif yang sama persis di 6 tempat.
     */
    private function applyTipeFilter($query, ?string $tipe): void
    {
        if ($tipe === 'biasa') {
            $query->where('is_pinjaman', false);
        } elseif ($tipe === 'pinjaman') {
            $query->where('is_pinjaman', true)->where('tipe_transaksi', 'pinjaman');
        } elseif ($tipe === 'pengembalian') {
            $query->where('is_pinjaman', true)->where('tipe_transaksi', 'pengembalian');
        } else {
            // Semua transaksi: biasa + pinjaman + pengembalian
            $query->where(
                fn($q) => $q
                    ->where('is_pinjaman', false)
                    ->orWhere(
                        fn($sub) => $sub
                            ->where('is_pinjaman', true)
                            ->whereIn('tipe_transaksi', ['pinjaman', 'pengembalian'])
                    )
            );
        }
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
