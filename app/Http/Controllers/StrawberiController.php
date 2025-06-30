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

        // Hitung total stok
        $stokSegar = Strawberi::where('jenis', 'segar')
            ->where('tanggal_kadaluarsa', '>=', now())
            ->get()
            ->sum(function ($strawberi) {
                return $strawberi->stok_tersisa;
            });

        $stokBeku = Strawberi::where('jenis', 'beku')
            ->where('tanggal_kadaluarsa', '>=', now())
            ->get()
            ->sum(function ($strawberi) {
                return $strawberi->stok_tersisa;
            });

        // Hitung stok yang hampir kadaluarsa
        $kadaluarsa = Strawberi::where('tanggal_kadaluarsa', '>=', now())
            ->where('tanggal_kadaluarsa', '<=', now()->addDays(7))
            ->get()
            ->sum(function ($strawberi) {
                return $strawberi->stok_tersisa;
            });

        // Ambil daftar supplier untuk filter
        $suppliers = Supplier::where('status', 'aktif')->orderBy('nama')->get();

        return view('strawberi.index', compact(
            'strawberis',
            'stokSegar',
            'stokBeku',
            'kadaluarsa',
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
            'jumlah' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after:tanggal_masuk',
            'supplier_id' => 'required|exists:suppliers,id',
            'keterangan' => 'nullable|string',
            'buat_transaksi' => 'boolean',
            'tambah_pinjaman' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Simpan data strawberi
            $strawberi = Strawberi::create([
                'jenis' => $request->jenis,
                'jumlah' => $request->jumlah,
                'stok_awal' => $request->jumlah,
                'stok_terjual' => 0,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
                'supplier_id' => $request->supplier_id,
                'keterangan' => $request->keterangan,
            ]);

            $supplier = Supplier::find($request->supplier_id);
            $totalHarga = $request->harga_beli * $request->jumlah;

            // Buat transaksi pengeluaran otomatis jika diminta
            if ($request->has('buat_transaksi') && $request->buat_transaksi) {
                Transaksi::create([
                    'jenis' => 'pengeluaran',
                    'jumlah' => $totalHarga,
                    'tanggal' => $request->tanggal_masuk,
                    'kategori' => 'Pembelian Strawberi',
                    'keterangan' => "Pembelian {$request->jumlah} kg strawberi {$request->jenis} dari {$supplier->nama}",
                    'user_id' => Auth::id(),
                ]);
            }

            // Update total pinjaman supplier jika diminta
            if ($request->has('tambah_pinjaman') && $request->tambah_pinjaman) {
                $supplier->total_pinjaman += $totalHarga;
                $supplier->save();
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
            'jumlah' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after:tanggal_masuk',
            'supplier_id' => 'required|exists:suppliers,id',
            'keterangan' => 'nullable|string',
        ]);

        $strawberi->update([
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
            'supplier_id' => $request->supplier_id,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('strawberi.index')
            ->with('success', 'Stok strawberi berhasil diperbarui');
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
            'tanggal_jual' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update stok terjual
            $strawberi->stok_terjual += $request->jumlah_jual;
            $strawberi->save();

            // Buat transaksi pemasukan
            Transaksi::create([
                'jenis' => 'pemasukan',
                'jumlah' => $request->jumlah_jual * $request->harga_jual,
                'tanggal' => $request->tanggal_jual,
                'kategori' => 'Penjualan Strawberi',
                'keterangan' => "Penjualan {$request->jumlah_jual} kg strawberi {$strawberi->jenis} - {$request->keterangan}",
                'user_id' => Auth::id(),
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
}
