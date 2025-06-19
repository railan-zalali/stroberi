<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(10);
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
        ]);

        $supplier = new Supplier([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'total_pinjaman' => $request->total_pinjaman ?? 0,
            'total_pembayaran' => $request->total_pembayaran ?? 0,
            'keterangan' => $request->keterangan,
        ]);

        $supplier->save();

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    public function show(Supplier $supplier)
    {
        return view('supplier.show', compact('supplier'));
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
        ]);

        $supplier->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'total_pinjaman' => $request->total_pinjaman ?? $supplier->total_pinjaman,
            'total_pembayaran' => $request->total_pembayaran ?? $supplier->total_pembayaran,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil diperbarui');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus');
    }
}
