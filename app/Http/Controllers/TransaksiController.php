<?php

namespace App\Http\Controllers;

use App\Exports\TransaksiExport;
use App\Models\Strawberi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        // Set default filter untuk bulan ini
        $tanggalMulai = $request->tanggal_mulai ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $tanggalAkhir = $request->tanggal_akhir ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        // Buat query dasar
        $query = Transaksi::query();

        // Filter berdasarkan tanggal
        $query->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);

        // Filter berdasarkan jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Hitung total pemasukan dan pengeluaran
        $totalPemasukan = (clone $query)->where('jenis', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = (clone $query)->where('jenis', 'pengeluaran')->sum('jumlah');

        // Ambil data transaksi
        $transaksis = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Ambil kategori unik untuk filter
        $kategoris = Transaksi::distinct()->pluck('kategori')
            ->filter(function ($value) {
                return !is_null($value);
            })
            ->toArray();

        return view('transaksi.index', compact(
            'transaksis',
            'totalPemasukan',
            'totalPengeluaran',
            'kategoris'
        ));
    }

    public function create(Request $request)
    {
        // Ambil supplier_name dari query parameter jika ada
        $supplierName = $request->query('supplier_name');

        return view('transaksi.create', compact('supplierName'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'kategori' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'tipe_transaksi' => 'nullable|string',
            'is_pinjaman' => 'nullable|boolean',
            'supplier_name' => 'nullable|string|max:255',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        // Set kategori default jika kosong
        $kategori = $request->kategori;
        if (empty($kategori)) {
            $kategori = $request->jenis == 'pemasukan' ? 'Penjualan' : 'Operasional';
        }

        // Tidak menggunakan logika pinjaman pada form manual

        // Handle file upload for bukti_pembayaran
        $buktiPembayaranPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        }

        $transaksi = new Transaksi([
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'kategori' => $kategori,
            'keterangan' => $request->keterangan,
            'tipe_transaksi' => $request->tipe_transaksi ?? 'lainnya',
            'is_pinjaman' => false,
            'user_id' => Auth::id(),
            'supplier_name' => null,
            'bukti_pembayaran' => $buktiPembayaranPath,
        ]);

        $transaksi->save();

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function show(Transaksi $transaksi)
    {
        // Ambil transaksi terkait (dengan kategori yang sama)
        $transaksiTerkait = Transaksi::where('id', '!=', $transaksi->id)
            ->where('kategori', $transaksi->kategori)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        return view('transaksi.show', compact('transaksi', 'transaksiTerkait'));
    }

    public function edit(Transaksi $transaksi)
    {
        return view('transaksi.edit', compact('transaksi'));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'kategori' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'tipe_transaksi' => 'nullable|string',
            'is_pinjaman' => 'nullable|boolean',
            'supplier_name' => 'nullable|string|max:255',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        // Validasi khusus pinjaman: butuh nama supplier
        if (($request->is_pinjaman || $request->tipe_transaksi == 'pinjaman') && empty($request->supplier_name)) {
            return redirect()->back()
                ->with('error', 'Nama supplier harus diisi untuk transaksi pinjaman')
                ->withInput();
        }

        // Handle file upload for bukti_pembayaran
        $buktiPembayaranPath = $transaksi->bukti_pembayaran; // Keep existing file if no new file uploaded
        if ($request->hasFile('bukti_pembayaran')) {
            // Delete old file if exists
            if ($transaksi->bukti_pembayaran && \Storage::disk('public')->exists($transaksi->bukti_pembayaran)) {
                \Storage::disk('public')->delete($transaksi->bukti_pembayaran);
            }
            $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        }

        $transaksi->update([
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'tipe_transaksi' => $request->tipe_transaksi ?? $transaksi->tipe_transaksi,
            'is_pinjaman' => $request->has('is_pinjaman') ? (bool)$request->is_pinjaman : ($transaksi->is_pinjaman ?? false),
            'supplier_name' => $request->has('supplier_name') ? $request->supplier_name : $transaksi->supplier_name,
            'bukti_pembayaran' => $buktiPembayaranPath,
        ]);

        return redirect()->route('transaksi.show', $transaksi)
            ->with('success', 'Transaksi berhasil diperbarui');
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();
        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }

    public function export(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $tanggalAkhir = $request->tanggal_akhir ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        $jenis = $request->jenis;
        $kategori = $request->kategori;

        $fileName = 'transaksi_';

        if ($jenis) {
            $fileName .= strtolower($jenis) . '_';
        }

        if ($kategori) {
            $fileName .= strtolower(str_replace(' ', '_', $kategori)) . '_';
        }

        $fileName .= Carbon::parse($tanggalMulai)->format('d-m-Y') . '_sampai_' . Carbon::parse($tanggalAkhir)->format('d-m-Y') . '.xlsx';

        return Excel::download(new TransaksiExport($tanggalMulai, $tanggalAkhir, $jenis, $kategori), $fileName);
    }


    /**
     * Export transaksi to PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPdf(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $tanggalAkhir = $request->tanggal_akhir ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        $jenis = $request->jenis;
        $kategori = $request->kategori;

        $query = Transaksi::with('user')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);

        if (!empty($jenis)) {
            $query->where('jenis', $jenis);
        }

        if (!empty($kategori)) {
            $query->where('kategori', $kategori);
        }

        $transaksis = $query->orderBy('tanggal', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $fileName = 'transaksi_' . Carbon::parse($tanggalMulai)->format('d-m-Y') . '_sampai_' . Carbon::parse($tanggalAkhir)->format('d-m-Y') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('transaksi.pdf', [
            'transaksis' => $transaksis,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
            'jenis' => $jenis,
            'kategori' => $kategori,
        ]);

        return $pdf->download($fileName);
    }


}
