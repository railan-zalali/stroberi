<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Strawberi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Supplier::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('telepon', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $suppliers = $query->orderBy('nama')->paginate(10)->withQueryString();

        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'total_pinjaman' => 'nullable|numeric|min:0',
            'total_pembayaran' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,tidak aktif',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('suppliers', 'public');
            $data['foto'] = $path;
        }

        $supplier = Supplier::create($data);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    public function show(Supplier $supplier)
    {
        // Ambil data strawberi dari supplier ini
        $strawberis = Strawberi::where('supplier_id', $supplier->id)
            ->orderBy('tanggal_masuk', 'desc')
            ->paginate(5);

        // Hitung total kg strawberi yang telah dibeli dari supplier ini
        $totalKg = Strawberi::where('supplier_id', $supplier->id)->sum('jumlah');

        // Hitung total nilai strawberi yang telah dibeli
        $totalNilai = Strawberi::where('supplier_id', $supplier->id)
            ->selectRaw('SUM(jumlah * harga_beli) as total')
            ->first()->total ?? 0;

        // Ambil transaksi terkait supplier ini
        $transaksis = Transaksi::where('keterangan', 'like', "%{$supplier->nama}%")
            ->orderBy('tanggal', 'desc')
            ->paginate(5);

        // Hitung sisa pinjaman
        $sisaPinjaman = $supplier->total_pinjaman - $supplier->total_pembayaran;

        return view('supplier.show', compact(
            'supplier',
            'strawberis',
            'totalKg',
            'totalNilai',
            'transaksis',
            'sisaPinjaman'
        ));
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'total_pinjaman' => 'nullable|numeric|min:0',
            'total_pembayaran' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,tidak aktif',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($supplier->foto) {
                Storage::disk('public')->delete($supplier->foto);
            }

            // Upload foto baru
            $path = $request->file('foto')->store('suppliers', 'public');
            $data['foto'] = $path;
        }

        $supplier->update($data);

        return redirect()->route('supplier.show', $supplier)
            ->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy(Supplier $supplier)
    {
        // Cek apakah supplier masih memiliki data strawberi
        $hasStrawberi = Strawberi::where('supplier_id', $supplier->id)->exists();

        if ($hasStrawberi) {
            return redirect()->route('supplier.index')
                ->with('error', 'Supplier tidak dapat dihapus karena masih memiliki data strawberi');
        }

        // Hapus foto jika ada
        if ($supplier->foto) {
            Storage::disk('public')->delete($supplier->foto);
        }

        $supplier->delete();

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus');
    }

    public function updatePembayaran(Request $request, Supplier $supplier)
    {
        $request->validate([
            'jumlah_pembayaran' => 'required|numeric|min:0',
            'tanggal_pembayaran' => 'required|date',
            'keterangan_pembayaran' => 'nullable|string',
        ]);

        // Validasi sisa pinjaman
        $sisaPinjaman = $supplier->sisa_pinjaman;
        if ($request->jumlah_pembayaran > $sisaPinjaman) {
            return redirect()->back()
                ->with('error', "Jumlah pembayaran melebihi sisa pinjaman (Rp " . number_format($sisaPinjaman, 0, ',', '.') . ")")
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update total pembayaran supplier
            $supplier->total_pembayaran += $request->jumlah_pembayaran;
            $supplier->save();

            // Buat transaksi pengeluaran untuk pembayaran supplier
            Transaksi::create([
                'jenis' => 'pengeluaran',
                'jumlah' => $request->jumlah_pembayaran,
                'tanggal' => $request->tanggal_pembayaran,
                'kategori' => 'Pembayaran Supplier',
                'keterangan' => "Pembayaran ke supplier {$supplier->nama}: {$request->keterangan_pembayaran}",
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('supplier.show', $supplier)
                ->with('success', 'Pembayaran supplier berhasil dicatat');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updatePinjamanOtomatis(Supplier $supplier, $jumlah)
    {
        $supplier->total_pinjaman += $jumlah;
        $supplier->save();
    }
}
