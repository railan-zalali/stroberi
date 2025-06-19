<?php

namespace App\Http\Controllers;

use App\Models\Strawberi;
use App\Models\Supplier;
use Illuminate\Http\Request;

class StrawberiController extends Controller
{
    public function index()
    {
        $strawberis = Strawberi::with('supplier')->orderBy('tanggal_masuk', 'desc')->paginate(10);
        return view('strawberi.index', compact('strawberis'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('strawberi.create', compact('suppliers'));
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

        $strawberi = new Strawberi([
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
            'supplier_id' => $request->supplier_id,
            'keterangan' => $request->keterangan,
        ]);

        $strawberi->save();

        return redirect()->route('strawberi.index')
            ->with('success', 'Stok strawberi berhasil ditambahkan');
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
