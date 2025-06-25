<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $tanggalMulai;
    protected $tanggalAkhir;
    protected $jenis;
    protected $kategori;

    public function __construct($tanggalMulai = null, $tanggalAkhir = null, $jenis = null, $kategori = null)
    {
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->jenis = $jenis;
        $this->kategori = $kategori;
    }

    public function collection()
    {
        $query = Transaksi::with('user')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        if ($this->tanggalMulai && $this->tanggalAkhir) {
            $query->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalAkhir]);
        }

        if ($this->jenis) {
            $query->where('jenis', $this->jenis);
        }

        if ($this->kategori) {
            $query->where('kategori', $this->kategori);
        }

        return $query->get();
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
            'Dibuat Pada',
            'Diperbarui Pada',
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
            $transaksi->created_at->format('d/m/Y H:i'),
            $transaksi->updated_at->format('d/m/Y H:i'),
        ];
    }
}
