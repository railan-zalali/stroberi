<?php

namespace App\Exports;

use App\Models\Strawberi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class LaporanStokExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $jenis;
    protected $status;
    protected $supplier_id;

    public function __construct($jenis = null, $status = null, $supplier_id = null)
    {
        $this->jenis = $jenis;
        $this->status = $status;
        $this->supplier_id = $supplier_id;
    }

    public function collection()
    {
        $query = Strawberi::with(['supplier', 'stockMovements']);

        if ($this->jenis) {
            $query->where('jenis', $this->jenis);
        }

        if ($this->status) {
            if ($this->status == 'kadaluarsa') {
                $query->where('tanggal_kadaluarsa', '<', now());
            } elseif ($this->status == 'hampir_kadaluarsa') {
                $query->where('tanggal_kadaluarsa', '>=', now())
                    ->where('tanggal_kadaluarsa', '<=', now()->addDays(7));
            } elseif ($this->status == 'baik') {
                $query->where('tanggal_kadaluarsa', '>', now()->addDays(7));
            }
        }

        if ($this->supplier_id) {
            $query->where('supplier_id', $this->supplier_id);
        }

        return $query->orderBy('tanggal_masuk', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Batch Number',
            'Jenis',
            'Stok Awal (kg)',
            'Terjual (kg)',
            'Rusak (kg)',
            'Penyesuaian (kg)',
            'Stok Tersisa (kg)',
            'Harga Beli',
            'Harga Jual',
            'Tanggal Masuk',
            'Tanggal Kadaluarsa',
            'Status',
            'Supplier',
            'Keterangan',
            'Catatan Penyesuaian',
            'Update Terakhir'
        ];
    }

    public function map($strawberi): array
    {
        return [
            $strawberi->id,
            $strawberi->batch_number,
            ucfirst($strawberi->jenis),
            number_format($strawberi->stok_awal, 2),
            number_format($strawberi->stok_terjual, 2),
            number_format($strawberi->stok_rusak, 2),
            number_format($strawberi->stok_adjustment, 2),
            number_format($strawberi->stok_tersisa, 2),
            number_format($strawberi->harga_beli, 2),
            number_format($strawberi->harga_jual, 2),
            $strawberi->tanggal_masuk->format('d/m/Y'),
            $strawberi->tanggal_kadaluarsa->format('d/m/Y'),
            $strawberi->getStockStatus(),
            $strawberi->supplier->nama ?? '-',
            $strawberi->keterangan ?? '-',
            $strawberi->adjustment_notes ?? '-',
            $strawberi->last_stock_update ? $strawberi->last_stock_update->format('d/m/Y H:i:s') : '-'
        ];
    }
}
