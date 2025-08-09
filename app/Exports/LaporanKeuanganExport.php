<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class LaporanKeuanganExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth();

        return Transaksi::with('user')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal',
            'Jenis',
            'Kategori',
            'Jumlah',
            'Keterangan',
            'Dibuat Oleh',
        ];
    }

    public function map($transaksi): array
    {
        return [
            $transaksi->id,
            $transaksi->tanggal->format('d/m/Y'),
            ucfirst($transaksi->jenis),
            $transaksi->kategori ?? '-',
            $transaksi->jumlah,
            $transaksi->keterangan ?? '-',
            $transaksi->user->name ?? '-',
        ];
    }
}
