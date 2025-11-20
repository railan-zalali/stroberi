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
        // Ambil data strawberi dari supplier ini (hanya yang sudah diposting)
        $strawberis = Strawberi::where('supplier_id', $supplier->id)
            ->where('is_posted', true)
            ->orderBy('tanggal_masuk', 'desc')
            ->paginate(5);

        // Hitung total kg strawberi yang telah dibeli dari supplier ini
        $totalKg = Strawberi::where('supplier_id', $supplier->id)->sum('jumlah');

        // Hitung total nilai strawberi yang telah dibeli
        $totalNilai = Strawberi::where('supplier_id', $supplier->id)
            ->selectRaw('SUM(jumlah * harga_beli) as total')
            ->first()->total ?? 0;

        // Ambil transaksi terkait supplier ini
        $transaksis = Transaksi::where('supplier_id', $supplier->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(5);

        // Ringkasan: tampilkan nilai pembelian pending agar 0 setelah diselesaikan
        $pembayaranKeSupplier = Transaksi::where('supplier_id', $supplier->id)
            ->where('jenis', 'pengeluaran')
            ->where('is_pinjaman', false)
            ->where('kategori', 'Pembelian Strawberi (Pending)')
            ->sum('jumlah');

        $pinjamanSupplier = Transaksi::where('supplier_id', $supplier->id)
            ->where('jenis', 'pengeluaran')
            ->where('is_pinjaman', true)
            ->sum('jumlah');

        $pembayaranDariSupplier = Transaksi::where('supplier_id', $supplier->id)
            ->where('jenis', 'pemasukan')
            ->where('tipe_transaksi', 'pengembalian')
            ->sum('jumlah');

        $sisaPinjaman = $supplier->sisa_pinjaman;

        // Pending purchases (belum diposting ke pembukuan)
        $pendingBatches = Transaksi::where('supplier_id', $supplier->id)
            ->where('jenis', 'pengeluaran')
            ->where('is_pinjaman', false)
            ->where('kategori', 'Pembelian Strawberi (Pending)')
            ->orderBy('tanggal', 'desc')
            ->get();

        $pendingTotalKg = 0;
        $pendingTotalNilai = $pendingBatches->sum('jumlah');

        return view('supplier.show', compact(
            'supplier',
            'strawberis',
            'totalKg',
            'totalNilai',
            'transaksis',
            'sisaPinjaman',
            'pembayaranKeSupplier',
            'pinjamanSupplier',
            'pembayaranDariSupplier',
            'pendingBatches',
            'pendingTotalKg',
            'pendingTotalNilai'
        ));
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    // Selesaikan transaksi kredit: pindahkan pembelian pending ke pembukuan
    public function finishTransactions(Supplier $supplier)
    {
        DB::beginTransaction();
        try {
            // Ubah transaksi pembelian pending menjadi pembelian (masuk pembukuan) dengan audit keterangan
            $userName = auth()->user() ? auth()->user()->name : 'system';
            $nowText = now()->format('d/m/Y H:i');

            $pendingPurchases = Transaksi::where('supplier_id', $supplier->id)
                ->where('jenis', 'pengeluaran')
                ->where('is_pinjaman', false)
                ->where('kategori', 'Pembelian Strawberi (Pending)')
                ->get();

            foreach ($pendingPurchases as $tx) {
                $auditNote = " | Diselesaikan pada {$nowText} oleh {$userName}";
                $tx->tipe_transaksi = 'pembelian';
                $tx->kategori = 'Pembelian Strawberi';
                $tx->keterangan = trim(($tx->keterangan ?? '') . $auditNote);
                $tx->save();
            }

            DB::commit();
            return redirect()->route('supplier.show', $supplier)
                ->with('success', 'Transaksi pembelian diselesaikan. Pembukuan diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
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

    // Metode untuk mencatat pengembalian pinjaman dari supplier ke perusahaan
    public function updatePengembalian(Request $request, Supplier $supplier)
    {
        $request->validate([
            'jumlah_pengembalian' => 'required|numeric|min:0',
            'tanggal_pengembalian' => 'required|date',
            'keterangan_pengembalian' => 'nullable|string',
            'metode_pengembalian' => 'required|in:tunai,transfer',
        ]);

        // Validasi sisa pinjaman
        $sisaPinjaman = $supplier->sisa_pinjaman;
        if ($request->jumlah_pengembalian > $sisaPinjaman) {
            return redirect()->back()
                ->with('error', "Jumlah pengembalian melebihi sisa pinjaman (Rp " . number_format($sisaPinjaman, 0, ',', '.') . ")")
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Tentukan kategori berdasarkan metode pengembalian
            $kategori = $request->metode_pengembalian === 'tunai'
                ? 'Pengembalian Pinjaman Supplier (Tunai)'
                : 'Pengembalian Pinjaman Supplier (Transfer)';

            // Buat keterangan yang lebih informatif
            $keterangan = "Pengembalian pinjaman {$request->metode_pengembalian} oleh supplier {$supplier->nama}";

            // Tambahkan keterangan tambahan jika ada
            if ($request->keterangan_pengembalian) {
                $keterangan .= ": {$request->keterangan_pengembalian}";
            }

            // Tambahkan informasi pelunasan jika jumlah pengembalian sama dengan sisa pinjaman
            if ($request->jumlah_pengembalian == $sisaPinjaman) {
                $keterangan .= " (Pelunasan)";
            }

            // Buat transaksi pemasukan untuk pengembalian pinjaman supplier
            Transaksi::create([
                'jenis' => 'pemasukan', // Pemasukan karena uang masuk ke perusahaan dari supplier
                'jumlah' => $request->jumlah_pengembalian,
                'tanggal' => $request->tanggal_pengembalian,
                'kategori' => $kategori,
                'keterangan' => $keterangan,
                'user_id' => auth()->id(),
                'supplier_id' => $supplier->id,
                'tipe_transaksi' => 'pengembalian',
                'is_pinjaman' => false,
            ]);

            DB::commit();

            // Pesan sukses yang lebih informatif
            $pesan = 'Pengembalian pinjaman supplier berhasil dicatat';
            if ($supplier->sisa_pinjaman <= 0) {
                $pesan .= '. Semua pinjaman telah dilunasi!';
            }

            return redirect()->route('supplier.show', $supplier)
                ->with('success', $pesan);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Metode untuk mencatat pinjaman baru dari perusahaan ke supplier
    public function createPinjaman(Request $request, Supplier $supplier)
    {
        $request->validate([
            'jumlah_pinjaman' => 'required|numeric|min:0',
            'tanggal_pinjaman' => 'required|date',
            'keterangan_pinjaman' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Buat transaksi pinjaman
            Transaksi::create([
                'jenis' => 'pengeluaran', // Pengeluaran karena uang keluar dari perusahaan ke supplier
                'jumlah' => $request->jumlah_pinjaman,
                'tanggal' => $request->tanggal_pinjaman,
                'kategori' => 'Pinjaman Supplier',
                'keterangan' => "Pinjaman untuk supplier {$supplier->nama}: {$request->keterangan_pinjaman}",
                'user_id' => auth()->id(),
                'supplier_id' => $supplier->id,
                'tipe_transaksi' => 'pinjaman',
                'is_pinjaman' => true,
            ]);

            DB::commit();

            return redirect()->route('supplier.show', $supplier)
                ->with('success', 'Pinjaman untuk supplier berhasil dicatat');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    // Metode untuk menambahkan pinjaman otomatis dari perusahaan ke supplier
    public function updatePinjamanOtomatis(Supplier $supplier, $jumlah)
    {
        // Buat transaksi pinjaman
        Transaksi::create([
            'jenis' => 'pengeluaran', // Pengeluaran karena uang keluar dari perusahaan ke supplier
            'jumlah' => $jumlah,
            'tanggal' => now(),
            'kategori' => 'Pinjaman Supplier',
            'keterangan' => "Penambahan pinjaman otomatis untuk supplier {$supplier->nama}",
            'user_id' => auth()->id(),
            'supplier_id' => $supplier->id,
            'tipe_transaksi' => 'pinjaman',
            'is_pinjaman' => true,
        ]);
    }
}
