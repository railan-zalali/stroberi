<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanSupplierExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents
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

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Supplier';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Calculate summary statistics
                $totalSupplier = Supplier::count();
                $totalSupplierAktif = Supplier::where('status', 'aktif')->count();
                $totalPinjaman = Supplier::sum('total_pinjaman');
                $totalPembayaran = Supplier::sum('total_pembayaran');
                $sisaPinjaman = $totalPinjaman - $totalPembayaran;

                // Judul laporan
                $sheet->setCellValue('A1', 'LAPORAN SUPPLIER');
                $sheet->mergeCells('A1:L1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Tanggal laporan
                $tanggal = 'Per Tanggal: ' . Carbon::now()->format('d F Y');
                $sheet->setCellValue('A2', $tanggal);
                $sheet->mergeCells('A2:L2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Ringkasan
                $sheet->setCellValue('A4', 'Total Supplier: ' . $totalSupplier);
                $sheet->setCellValue('C4', 'Supplier Aktif: ' . $totalSupplierAktif);
                $sheet->setCellValue('E4', 'Total Pinjaman: Rp ' . number_format($totalPinjaman, 0, ',', '.'));
                $sheet->setCellValue('H4', 'Total Pembayaran: Rp ' . number_format($totalPembayaran, 0, ',', '.'));
                $sheet->setCellValue('K4', 'Sisa Pinjaman: Rp ' . number_format($sisaPinjaman, 0, ',', '.'));

                $sheet->mergeCells('A4:B4');
                $sheet->mergeCells('C4:D4');
                $sheet->mergeCells('E4:G4');
                $sheet->mergeCells('H4:J4');
                $sheet->mergeCells('K4:L4');

                // Style untuk header
                $sheet->getStyle('A1:L4')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');

                // Garis pembatas
                $sheet->getStyle('A5:L5')->getBorders()
                    ->getBottom()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Set column formats
                $sheet->getStyle('F7:J' . ($event->sheet->getHighestRow()))
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                // Apply conditional formatting for status
                $lastRow = $event->sheet->getHighestRow();
                $conditionalStyles = [
                    [
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS,
                        'operator' => \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL,
                        'value' => '"Aktif"',
                        'style' => [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'ECFDF5'],
                            ],
                            'font' => ['color' => ['rgb' => '065F46']],
                        ],
                    ],
                    [
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS,
                        'operator' => \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL,
                        'value' => '"Tidak aktif"',
                        'style' => [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FEF2F2'],
                            ],
                            'font' => ['color' => ['rgb' => '991B1B']],
                        ],
                    ],
                ];
                $sheet->getStyle('K7:K' . $lastRow)->setConditionalStyles($conditionalStyles);

                // Tambahkan border ke seluruh tabel
                $sheet->getStyle('A6:L' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
