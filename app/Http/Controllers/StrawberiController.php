<?php

namespace App\Http\Controllers;

use App\Models\Strawberi;
use App\Models\Supplier;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->sum('jumlah');

        $stokBeku = Strawberi::where('jenis', 'beku')
            ->where('tanggal_kadaluarsa', '>=', now())
            ->sum('jumlah');

        // Hitung stok yang hampir kadaluarsa
        $kadaluarsa = Strawberi::where('tanggal_kadaluarsa', '>=', now())
            ->where('tanggal_kadaluarsa', '<=', now()->addDays(7))
            ->sum('jumlah');

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
        ]);

        // Simpan data strawberi
        $strawberi = Strawberi::create($request->all());

        // Buat transaksi pengeluaran otomatis
        $supplier = Supplier::find($request->supplier_id);
        Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => $request->harga_beli * $request->jumlah,
            'tanggal' => $request->tanggal_masuk,
            'kategori' => 'Pembelian Strawberi',
            'keterangan' => "Pembelian {$request->jumlah} kg strawberi {$request->jenis} dari {$supplier->nama}",
            'user_id' => Auth::id(),
        ]);

        // Update total pinjaman supplier jika diinginkan
        if ($request->has('tambah_pinjaman') && $request->tambah_pinjaman) {
            $supplier->total_pinjaman += ($request->harga_beli * $request->jumlah);
            $supplier->save();
        }

        return redirect()->route('strawberi.index')
            ->with('success', 'Stok strawberi dan transaksi pembelian berhasil ditambahkan');
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
}
