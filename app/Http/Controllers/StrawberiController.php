<?php

namespace App\Http\Controllers;

use App\Models\Strawberi;
use App\Models\Supplier;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StrawberiController extends Controller
{
    public function index(Request $request)
    {
        $query = Strawberi::with('supplier')->orderBy('tanggal_masuk', 'desc');

        // Filter berdasarkan supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter berdasarkan jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        
        // Filter berdasarkan grade
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        // Filter berdasarkan status kadaluarsa
        if ($request->filled('status')) {
            if ($request->status == 'kadaluarsa') {
                $query->where('tanggal_kadaluarsa', '<', now());
            } elseif ($request->status == 'hampir_kadaluarsa') {
                $query->where('tanggal_kadaluarsa', '>=', now())
                    ->where('tanggal_kadaluarsa', '<=', now()->addDays(7));
            } elseif ($request->status == 'baik') {
                $query->where('tanggal_kadaluarsa', '>', now()->addDays(7));
            }
        }

        $strawberis = $query->paginate(10)->withQueryString();

        // Hitung total stok berdasarkan jenis dan grade
        $stokSegarA = $this->hitungStokBerdasarkanJenisGrade('segar', 'a');
        $stokSegarB = $this->hitungStokBerdasarkanJenisGrade('segar', 'b');
        $stokSegarC = $this->hitungStokBerdasarkanJenisGrade('segar', 'c');
        $stokBekuA = $this->hitungStokBerdasarkanJenisGrade('beku', 'a');
        $stokBekuB = $this->hitungStokBerdasarkanJenisGrade('beku', 'b');
        $stokBekuC = $this->hitungStokBerdasarkanJenisGrade('beku', 'c');
        
        // Total stok berdasarkan grade (semua jenis)
        $stokGradeA = $stokSegarA + $stokBekuA;
        $stokGradeB = $stokSegarB + $stokBekuB;
        $stokGradeC = $stokSegarC + $stokBekuC;
        
        // Total stok segar dan beku (semua grade)
        $stokSegar = $stokSegarA + $stokSegarB + $stokSegarC;
        $stokBeku = $stokBekuA + $stokBekuB + $stokBekuC;

        // Hitung stok yang hampir kadaluarsa
        $kadaluarsa = Strawberi::where('tanggal_kadaluarsa', '>=', now())
            ->where('tanggal_kadaluarsa', '<=', now()->addDays(7))
            ->get()
            ->sum(function ($strawberi) {
                return $strawberi->stok_tersisa;
            });

        // Ambil daftar supplier untuk filter
        $suppliers = Supplier::where('status', 'aktif')->orderBy('nama')->get();

        // Hitung nilai stok berdasarkan harga beli
        $nilaiStok = Strawberi::where('tanggal_kadaluarsa', '>=', now())
            ->get()
            ->sum(function ($strawberi) {
                return $strawberi->stok_tersisa * $strawberi->harga_beli;
            });
            
        return view('strawberi.index', compact(
            'strawberis',
            'stokSegar',
            'stokBeku',
            'stokSegarA',
            'stokSegarB',
            'stokSegarC',
            'stokBekuA',
            'stokBekuB',
            'stokBekuC',
            'stokGradeA',
            'stokGradeB',
            'stokGradeC',
            'kadaluarsa',
            'nilaiStok',
            'suppliers'
        ));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::where('status', 'aktif')->orderBy('nama')->get();
        $selectedSupplierId = $request->supplier_id;

        return view('strawberi.create', compact('suppliers', 'selectedSupplierId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:segar,beku',
            'grade' => 'required|in:a,b,c',
            'jumlah' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'keterangan' => 'nullable|string',
            'buat_transaksi' => 'boolean',
            'tambah_pinjaman' => 'boolean',
            'metode_pembayaran' => 'nullable|string|in:tunai,kredit',
        ]);

        // Jika pembayaran kredit atau ingin menambah pinjaman, supplier wajib diisi
        if (($request->metode_pembayaran === 'kredit') || ($request->has('tambah_pinjaman') && $request->tambah_pinjaman)) {
            $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
            ]);
        }

        DB::beginTransaction();
        try {
            // Simpan data strawberi
            $strawberi = Strawberi::create([
                'jenis' => $request->jenis,
                'grade' => $request->grade,
                'jumlah' => $request->jumlah,
                'stok_awal' => $request->jumlah,
                'stok_terjual' => 0,
                'harga_beli' => $request->harga_beli,
                'tanggal_masuk' => $request->tanggal_masuk,
                'supplier_id' => $request->supplier_id,
                'keterangan' => $request->keterangan,
            ]);

            $supplier = $request->supplier_id ? Supplier::find($request->supplier_id) : null;
            $totalHarga = $request->harga_beli * $request->jumlah;
            
            // Tentukan metode pembayaran (default: tunai)
            $metodePembayaran = $request->metode_pembayaran ?? 'tunai';
            
            // Jika metode pembayaran adalah kredit, tambahkan ke pinjaman supplier
            if ($metodePembayaran === 'kredit') {
                // Buat catatan transaksi dengan kategori khusus untuk kredit
                Transaksi::create([
                    'jenis' => 'pengeluaran',
                    'jumlah' => $totalHarga,
                    'tanggal' => $request->tanggal_masuk,
                    'kategori' => 'Pembelian Strawberi (Kredit)',
                    'keterangan' => "Pembelian kredit {$request->jumlah} kg strawberi {$request->jenis} grade {$request->grade}" . ($supplier ? " dari {$supplier->nama}" : ''),
                    'user_id' => Auth::id(),
                    'supplier_id' => $supplier ? $supplier->id : null,
                    'supplier_name' => $supplier ? $supplier->nama : null,
                    'tipe_transaksi' => 'pinjaman',
                    'is_pinjaman' => true,
                ]);
            } 
            // Jika metode pembayaran tunai dan diminta buat transaksi
            elseif ($metodePembayaran === 'tunai' && ($request->has('buat_transaksi') && $request->buat_transaksi)) {
                Transaksi::create([
                    'jenis' => 'pengeluaran',
                    'jumlah' => $totalHarga,
                    'tanggal' => $request->tanggal_masuk,
                    'kategori' => 'Pembelian Strawberi (Tunai)',
                    'keterangan' => "Pembelian tunai {$request->jumlah} kg strawberi {$request->jenis} grade {$request->grade}" . ($supplier ? " dari {$supplier->nama}" : ''),
                    'user_id' => Auth::id(),
                    'supplier_id' => $supplier ? $supplier->id : null,
                    'supplier_name' => $supplier ? $supplier->nama : null,
                    'tipe_transaksi' => 'pembelian',
                    'is_pinjaman' => false,
                ]);
            }
            
            // Jika metode pembayaran tunai tapi masih ingin menambahkan ke pinjaman supplier
            // (kasus khusus, misalnya untuk mencatat hutang lain)
            if ($metodePembayaran === 'tunai' && ($request->has('tambah_pinjaman') && $request->tambah_pinjaman)) {
                Transaksi::create([
                    'jenis' => 'pengeluaran',
                    'jumlah' => $totalHarga,
                    'tanggal' => $request->tanggal_masuk,
                    'kategori' => 'Pinjaman Supplier',
                    'keterangan' => "Penambahan pinjaman untuk supplier" . ($supplier ? " {$supplier->nama}" : '') . " (Pembelian Strawberi Tunai)",
                    'user_id' => Auth::id(),
                    'supplier_id' => $supplier ? $supplier->id : null,
                    'supplier_name' => $supplier ? $supplier->nama : null,
                    'tipe_transaksi' => 'pinjaman',
                    'is_pinjaman' => true,
                ]);
            }

            DB::commit();

            return redirect()->route('strawberi.index')
                ->with('success', 'Stok strawberi berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Strawberi $strawberi)
    {
        return view('strawberi.show', compact('strawberi'));
    }

    public function edit(Strawberi $strawberi)
    {
        $suppliers = Supplier::all();
        return view('strawberi.edit', compact('strawberi', 'suppliers'));
    }

    public function update(Request $request, Strawberi $strawberi)
    {
        $request->validate([
            'jenis' => 'required|in:segar,beku',
            'grade' => 'required|in:a,b,c',
            'jumlah' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after:tanggal_masuk',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'keterangan' => 'nullable|string',
        ]);

        $strawberi->update([
            'jenis' => $request->jenis,
            'grade' => $request->grade,
            'jumlah' => $request->jumlah,
            'harga_beli' => $request->harga_beli,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
            'supplier_id' => $request->supplier_id,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('strawberi.index')
            ->with('success', 'Stok strawberi berhasil diperbarui');
    }

    // Form penjualan global (stok gabungan, FEFO)
    public function sellGlobalForm()
    {
        return view('strawberi.sell-global');
    }

    // Proses penjualan global dengan FEFO dan campur grade jika perlu
    public function sellGlobalStore(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:segar,beku',
            'preferensi_grade' => 'nullable|in:a,b,c',
            'jumlah_jual' => 'required|numeric|min:0.01',
            'price_mode' => 'required|in:manual,buy',
            'harga_jual' => 'nullable|numeric|min:0',
            'pembeli' => 'required|string|max:255',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'tanggal_jual' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->price_mode === 'manual') {
            $request->validate([
                'harga_jual' => 'required|numeric|min:0',
            ]);
        }

        DB::beginTransaction();
        try {
            // Handle bukti pembayaran
            $buktiPembayaranPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            }

            $remaining = $request->jumlah_jual;
            $allocated = [];

            // Ambil batch utama sesuai preferensi grade (FEFO)
            $primaryBatches = Strawberi::where('jenis', $request->jenis)
                ->where('tanggal_kadaluarsa', '>=', now())
                ->when($request->preferensi_grade, function ($q) use ($request) {
                    $q->where('grade', $request->preferensi_grade);
                })
                ->orderBy('tanggal_kadaluarsa', 'asc')
                ->orderBy('tanggal_masuk', 'asc')
                ->get();

            // Ambil batch fallback (campur grade) jika stok preferensi tidak cukup
            $fallbackBatches = Strawberi::where('jenis', $request->jenis)
                ->where('tanggal_kadaluarsa', '>=', now())
                ->when($request->preferensi_grade, function ($q) use ($request) {
                    $q->where('grade', '!=', $request->preferensi_grade);
                })
                ->orderBy('tanggal_kadaluarsa', 'asc')
                ->orderBy('tanggal_masuk', 'asc')
                ->get();

            $batches = $primaryBatches->concat($fallbackBatches);

            foreach ($batches as $batch) {
                if ($remaining <= 0) break;
                $available = $batch->stok_tersisa;
                if ($available <= 0) continue;

                $take = min($available, $remaining);
                if ($take <= 0) continue;

                // Catat alokasi
                $allocated[] = [
                    'batch' => $batch,
                    'qty' => $take,
                ];

                // Record movement (sekalian update stok terjual)
                $batch->recordStockMovement('sale', $take, 'Penjualan Global (FEFO)');

                $remaining -= $take;
            }

            $allocatedTotal = collect($allocated)->sum('qty');

            if ($allocatedTotal <= 0) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Stok global tidak mencukupi untuk penjualan')
                    ->withInput();
            }

            // Hitung jumlah uang berdasarkan mode harga
            if ($request->price_mode === 'manual') {
                $jumlahUang = $allocatedTotal * $request->harga_jual;
            } else { // buy: gunakan harga beli batch (weighted)
                $jumlahUang = 0;
                foreach ($allocated as $alloc) {
                    $jumlahUang += $alloc['qty'] * $alloc['batch']->harga_beli;
                }
            }

            // Buat keterangan ringkas
            $detailAlokasi = collect($allocated)->map(function ($alloc) {
                return $alloc['qty'] . 'kg ' . $alloc['batch']->grade . ' (exp ' . $alloc['batch']->tanggal_kadaluarsa->format('d/m') . ')';
            })->implode(', ');

            $keterangan = 'Penjualan Global ' . $request->jenis
                . ($request->preferensi_grade ? " preferensi grade {$request->preferensi_grade}" : '')
                . ' dengan FEFO, alokasi: ' . $detailAlokasi
                . ($request->keterangan ? ' - ' . $request->keterangan : '');

            // Catat transaksi pemasukan
            Transaksi::create([
                'jenis' => 'pemasukan',
                'jumlah' => $jumlahUang,
                'tanggal' => $request->tanggal_jual,
                'kategori' => 'Penjualan Strawberi (Global)',
                'keterangan' => $keterangan,
                'user_id' => Auth::id(),
                'supplier_name' => $request->pembeli,
                'bukti_pembayaran' => $buktiPembayaranPath,
                'tipe_transaksi' => 'penjualan',
                'is_pinjaman' => false,
            ]);

            DB::commit();

            $msg = 'Penjualan global berhasil (' . $allocatedTotal . ' kg dialokasikan).';
            if ($remaining > 0) {
                $msg .= ' Stok tidak cukup, penjualan partial dilakukan.';
            }

            return redirect()->route('strawberi.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Strawberi $strawberi)
    {
        $strawberi->delete();
        return redirect()->route('strawberi.index')
            ->with('success', 'Stok strawberi berhasil dihapus');
    }

    public function sell(Request $request, Strawberi $strawberi)
    {
        $request->validate([
            'jumlah_jual' => "required|numeric|min:0.01|max:{$strawberi->stok_tersisa}",
            'harga_jual' => 'required|numeric|min:0',
            'pembeli' => 'required|string|max:255',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'tanggal_jual' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Handle file upload
            $buktiPembayaranPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            }

            // Update stok terjual
            $strawberi->stok_terjual += $request->jumlah_jual;
            $strawberi->save();

            // Buat transaksi pemasukan
            Transaksi::create([
                'jenis' => 'pemasukan',
                'jumlah' => $request->jumlah_jual * $request->harga_jual,
                'tanggal' => $request->tanggal_jual,
                'kategori' => 'Penjualan Strawberi',
                'keterangan' => "Penjualan {$request->jumlah_jual} kg strawberi {$strawberi->jenis} grade {$strawberi->grade} kepada {$request->pembeli}" . ($request->keterangan ? " - {$request->keterangan}" : ""),
                'user_id' => Auth::id(),
                'supplier_name' => $request->pembeli,
                'bukti_pembayaran' => $buktiPembayaranPath,
                'tipe_transaksi' => 'penjualan',
                'is_pinjaman' => false,
            ]);

            DB::commit();

            return redirect()->route('strawberi.show', $strawberi)
                ->with('success', 'Penjualan strawberi berhasil dicatat');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getStockMovements(Strawberi $strawberi)
    {
        $movements = $strawberi->stockMovements()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($movement) {
                $movement->created_at = $movement->created_at->format('d/m/Y H:i:s');
                return $movement;
            });

        return response()->json($movements);
    }
    
    /**
     * Hitung stok berdasarkan jenis dan grade
     *
     * @param string $jenis
     * @param string $grade
     * @return float
     */
    private function hitungStokBerdasarkanJenisGrade($jenis, $grade)
    {
        return Strawberi::where('jenis', $jenis)
            ->where('grade', $grade)
            ->where('tanggal_kadaluarsa', '>=', now())
            ->get()
            ->sum(function ($strawberi) {
                return $strawberi->stok_tersisa;
            });
    }
}
