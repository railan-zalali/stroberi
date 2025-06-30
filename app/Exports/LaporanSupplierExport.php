<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\DB;

class LaporanSupplierExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Supplier::withCount(['strawberis as total_kg' => function ($query) {
            $query->select(DB::raw('SUM(jumlah)'));
        }])
            ->withCount(['strawberis as total_nilai' => function ($query) {
                $query->select(DB::raw('SUM(jumlah * harga_beli)'));
            }])
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Alamat',
            'Telepon',
            'Email',
            'Total Pinjaman',
            'Total Pembayaran',
            'Sisa Pinjaman',
            'Total Volume (kg)',
            'Total Nilai (Rp)',
            'Status',
            'Keterangan'
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->nama,
            $supplier->alamat ?? '-',
            $supplier->telepon ?? '-',
            $supplier->email ?? '-',
            $supplier->total_pinjaman,
            $supplier->total_pembayaran,
            $supplier->total_pinjaman - $supplier->total_pembayaran,
            $supplier->total_kg ?? 0,
            $supplier->total_nilai ?? 0,
            ucfirst($supplier->status),
            $supplier->keterangan ?? '-',
        ];
    }
}
