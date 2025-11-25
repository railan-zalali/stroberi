<?php

namespace App\Http\Controllers;

use App\Exports\TransaksiExport;
use App\Models\Strawberi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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

        // Sinkronisasi perubahan transaksi pembelian ke batch strawberi terkait
        $isPembelianStrawberi = $transaksi->jenis === 'pengeluaran'
            && in_array($transaksi->kategori, ['Pembelian Strawberi', 'Pembelian Strawberi (Pending)']);

        if ($isPembelianStrawberi && !empty($transaksi->keterangan)) {
            $batchNumber = null;
            if (preg_match('/Batch:\s*([A-Z0-9\-]+)/', $transaksi->keterangan, $m)) {
                $batchNumber = $m[1];
            }
            if ($batchNumber) {
                $batch = Strawberi::where('batch_number', $batchNumber)->first();
                if ($batch) {
                    $kgDasar = (float) ($batch->stok_awal ?: $batch->jumlah);
                    if ($kgDasar > 0) {
                        $batch->harga_beli = (float) $transaksi->jumlah / $kgDasar;
                    }
                    if ($transaksi->tanggal) {
                        $batch->tanggal_masuk = $transaksi->tanggal;
                    }
                    if ($transaksi->supplier_id) {
                        $batch->supplier_id = $transaksi->supplier_id;
                    }
                    $batch->save();
                }
            }
        }

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

    /**
     * Complete stock transaction after payment confirmation
     * This method finalizes stock movements by converting locked stock to sold stock
     */
    public function completeStockTransaction(Transaksi $transaksi)
    {
        // Check if this is a stock sale transaction
        if ($transaksi->jenis !== 'pemasukan' || $transaksi->tipe_transaksi !== 'penjualan') {
            return redirect()->back()
                ->with('error', 'Transaksi ini bukan transaksi penjualan stok');
        }

        // Check if stock transaction is already completed
        $sessionKey = 'stock_sale_' . $transaksi->id;
        if (!session()->has($sessionKey)) {
            return redirect()->back()
                ->with('info', 'Transaksi stok sudah diselesaikan atau tidak memerlukan penyelesaian stok');
        }

        $stockData = session($sessionKey);

        DB::beginTransaction();
        try {
            // Handle individual batch sale (from sell method)
            if (isset($stockData['strawberi_id'])) {
                $strawberi = Strawberi::find($stockData['strawberi_id']);
                if ($strawberi && $strawberi->is_locked) {
                    // Convert locked stock to sold stock
                    $strawberi->stok_terjual += $stockData['jumlah'];
                    $strawberi->stok_terkunci -= $stockData['jumlah'];
                    if ($strawberi->stok_terkunci <= 0) {
                        $strawberi->stok_terkunci = 0;
                        $strawberi->is_locked = false;
                    }
                    $strawberi->save();
                }
            }
            // Handle global sale (from sellGlobalStore method)
            elseif (isset($stockData['allocations'])) {
                foreach ($stockData['allocations'] as $allocation) {
                    $strawberi = Strawberi::find($allocation['strawberi_id']);
                    if ($strawberi && $strawberi->is_locked) {
                        // Convert locked stock to sold stock
                        $strawberi->stok_terjual += $allocation['jumlah'];
                        $strawberi->stok_terkunci -= $allocation['jumlah'];
                        if ($strawberi->stok_terkunci <= 0) {
                            $strawberi->stok_terkunci = 0;
                            $strawberi->is_locked = false;
                        }
                        $strawberi->save();
                    }
                }
            }

            // Remove session data
            session()->forget($sessionKey);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Transaksi stok berhasil diselesaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyelesaikan transaksi stok: ' . $e->getMessage());
        }
    }

    public function finalizeStock(Strawberi $strawberi)
    {
        // Check if there are pending stock allocations for this strawberry
        $sessionKey = 'pending_stock_allocations';
        if (!session()->has($sessionKey) || !isset(session($sessionKey)[$strawberi->id])) {
            return redirect()->back()
                ->with('error', 'Tidak ada alokasi stok yang tertunda untuk stok ini');
        }

        $allocatedAmount = session($sessionKey)[$strawberi->id];

        DB::beginTransaction();
        try {
            // Validate that the allocated amount doesn't exceed available stock
            $availableStock = $strawberi->stok_awal - $strawberi->stok_terjual - $strawberi->stok_rusak - $strawberi->stok_terkunci;

            if ($allocatedAmount > $availableStock) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Jumlah alokasi melebihi stok yang tersedia');
            }

            // Convert locked stock to sold stock
            $strawberi->stok_terjual += $allocatedAmount;
            $strawberi->stok_terkunci = max(0, $strawberi->stok_terkunci - $allocatedAmount);

            // If no more locked stock, unlock the batch
            if ($strawberi->stok_terkunci <= 0) {
                $strawberi->stok_terkunci = 0;
                $strawberi->is_locked = false;
            }

            // Mark as posted since it's now finalized
            $strawberi->is_posted = true;
            $strawberi->save();

            // Remove this allocation from session
            $allocations = session($sessionKey);
            unset($allocations[$strawberi->id]);
            session([$sessionKey => $allocations]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Stok berhasil diselesaikan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menyelesaikan stok: ' . $e->getMessage());
        }
    }
}
