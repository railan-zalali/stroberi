<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::orderBy('tanggal', 'desc')->paginate(10);
        return view('transaksi.index', compact('transaksis'));
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
            'keterangan' => 'nullable|string',
            'kategori' => 'nullable|string',
        ]);

        $transaksi = new Transaksi([
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'kategori' => $request->kategori,
            'user_id' => Auth::id(),
        ]);

        $transaksi->save();

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function show(Transaksi $transaksi)
    {
        return view('transaksi.show', compact('transaksi'));
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
            'keterangan' => 'nullable|string',
            'kategori' => 'nullable|string',
        ]);

        $transaksi->update([
            'jenis' => $request->jenis,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui');
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();
        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }
}
