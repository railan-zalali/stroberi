<?php

namespace App\Exports;

use App\Models\Strawberi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class LaporanStokExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
    
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
        ]);
        
        // Mendapatkan jumlah baris data
        $highestRow = $sheet->getHighestRow();
        
        // Conditional formatting untuk kolom status kadaluarsa
        for ($row = 2; $row <= $highestRow; $row++) {
            $sisaHariCell = $sheet->getCell('M' . $row)->getValue();
            
            if ($sisaHariCell === 'Kadaluarsa') {
                $sheet->getStyle('M' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'FF0000']],
                ]);
            } elseif (strpos($sisaHariCell, 'hari lagi') !== false) {
                $days = (int) $sisaHariCell;
                if ($days <= 7) {
                    $sheet->getStyle('M' . $row)->applyFromArray([
                        'font' => ['color' => ['rgb' => 'FFA500']],
                    ]);
                } else {
                    $sheet->getStyle('M' . $row)->applyFromArray([
                        'font' => ['color' => ['rgb' => '008000']],
                    ]);
                }
            }
        }
        
        return $sheet;
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
            'Sisa Hari',
            'Status',
            'Supplier',
            'Keterangan',
            'Catatan Penyesuaian',
            'Update Terakhir'
        ];
    }

    public function map($strawberi): array
    {
        // Hitung sisa hari kadaluarsa
        $sisaHari = now()->diffInDays($strawberi->tanggal_kadaluarsa, false);
        $sisaHariText = $sisaHari < 0 ? 'Kadaluarsa' : $sisaHari . ' hari lagi';
        
        return [
            $strawberi->id,
            $strawberi->batch_number,
            ucfirst($strawberi->jenis),
            number_format($strawberi->stok_awal, 2),
            number_format($strawberi->stok_terjual, 2),
            number_format($strawberi->stok_rusak, 2),
            number_format($strawberi->stok_adjustment, 2),
            number_format($strawberi->stok_tersisa, 2),
            'Rp ' . number_format($strawberi->harga_beli, 0, ',', '.'),
            'Rp ' . number_format($strawberi->harga_jual, 0, ',', '.'),
            $strawberi->tanggal_masuk->format('d/m/Y'),
            $strawberi->tanggal_kadaluarsa->format('d/m/Y'),
            $sisaHariText,
            $strawberi->getStockStatus(),
            $strawberi->supplier->nama ?? '-',
            $strawberi->keterangan ?? '-',
            $strawberi->adjustment_notes ?? '-',
            $strawberi->last_stock_update ? $strawberi->last_stock_update->format('d/m/Y H:i:s') : '-'
        ];
    }
}
