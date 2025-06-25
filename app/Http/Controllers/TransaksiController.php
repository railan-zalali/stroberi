<?php

namespace App\Http\Controllers;

use App\Exports\TransaksiExport;
use App\Models\Strawberi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function create()
    {
        return view('transaksi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'kategori' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $transaksi = new Transaksi([
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'user_id' => Auth::id(),
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
        ]);

        $transaksi->update([
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
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
    public function createFromStrawberi($strawberiId)
    {
        $strawberi = Strawberi::findOrFail($strawberiId);

        return view('transaksi.create', [
            'prefilledJenis' => 'pengeluaran',
            'prefilledJumlah' => $strawberi->harga_beli * $strawberi->jumlah,
            'prefilledKategori' => 'Pembelian Strawberi',
            'prefilledKeterangan' => "Pembelian {$strawberi->jumlah} kg strawberi {$strawberi->jenis} dari {$strawberi->supplier->nama}",
            'strawberiId' => $strawberi->id
        ]);
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
     * Export transaksi to CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportCsv(Request $request)
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

        $fileName .= Carbon::parse($tanggalMulai)->format('d-m-Y') . '_sampai_' . Carbon::parse($tanggalAkhir)->format('d-m-Y') . '.csv';

        return Excel::download(new TransaksiExport($tanggalMulai, $tanggalAkhir, $jenis, $kategori), $fileName, \Maatwebsite\Excel\Excel::CSV);
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

        $fileName = 'transaksi_';

        if ($jenis) {
            $fileName .= strtolower($jenis) . '_';
        }

        if ($kategori) {
            $fileName .= strtolower(str_replace(' ', '_', $kategori)) . '_';
        }

        $fileName .= Carbon::parse($tanggalMulai)->format('d-m-Y') . '_sampai_' . Carbon::parse($tanggalAkhir)->format('d-m-Y') . '.pdf';

        return Excel::download(new TransaksiExport($tanggalMulai, $tanggalAkhir, $jenis, $kategori), $fileName, \Maatwebsite\Excel\Excel::DOMPDF);
    }
}
