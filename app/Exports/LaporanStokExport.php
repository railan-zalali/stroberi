<?php

namespace App\Exports;

use App\Models\Strawberi;
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

class LaporanStokExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents
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
        $query = Strawberi::with('supplier');

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
            'Jenis',
            'Jumlah (kg)',
            'Harga Beli',
            'Harga Jual',
            'Tanggal Masuk',
            'Tanggal Kadaluarsa',
            'Status',
            'Supplier',
            'Keterangan'
        ];
    }

    public function map($strawberi): array
    {
        // Determine status based on expiry date
        $status = 'Baik';
        if ($strawberi->tanggal_kadaluarsa->isPast()) {
            $status = 'Kadaluarsa';
        } elseif ($strawberi->tanggal_kadaluarsa->diffInDays(now()) <= 7) {
            $status = 'Hampir Kadaluarsa';
        }

        return [
            $strawberi->id,
            ucfirst($strawberi->jenis),
            $strawberi->jumlah,
            $strawberi->harga_beli,
            $strawberi->harga_jual,
            $strawberi->tanggal_masuk->format('d/m/Y'),
            $strawberi->tanggal_kadaluarsa->format('d/m/Y'),
            $status,
            $strawberi->supplier->nama ?? '-',
            $strawberi->keterangan ?? '-',
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
        return 'Laporan Stok';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Calculate summary statistics
                $stokSegar = Strawberi::where('jenis', 'segar')
                    ->where('tanggal_kadaluarsa', '>=', now())
                    ->sum('jumlah');

                $stokBeku = Strawberi::where('jenis', 'beku')
                    ->where('tanggal_kadaluarsa', '>=', now())
                    ->sum('jumlah');

                $stokKadaluarsa = Strawberi::where('tanggal_kadaluarsa', '<', now())
                    ->sum('jumlah');

                $stokHampirKadaluarsa = Strawberi::where('tanggal_kadaluarsa', '>=', now())
                    ->where('tanggal_kadaluarsa', '<=', now()->addDays(7))
                    ->sum('jumlah');

                // Judul laporan
                $sheet->setCellValue('A1', 'LAPORAN STOK STRAWBERI');
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Tanggal laporan
                $tanggal = 'Per Tanggal: ' . Carbon::now()->format('d F Y');
                $sheet->setCellValue('A2', $tanggal);
                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Ringkasan
                $sheet->setCellValue('A4', 'Total Stok Segar: ' . number_format($stokSegar, 2) . ' kg');
                $sheet->setCellValue('C4', 'Total Stok Beku: ' . number_format($stokBeku, 2) . ' kg');
                $sheet->setCellValue('E4', 'Total Stok Hampir Kadaluarsa: ' . number_format($stokHampirKadaluarsa, 2) . ' kg');
                $sheet->setCellValue('H4', 'Total Stok Kadaluarsa: ' . number_format($stokKadaluarsa, 2) . ' kg');

                $sheet->mergeCells('A4:B4');
                $sheet->mergeCells('C4:D4');
                $sheet->mergeCells('E4:G4');
                $sheet->mergeCells('H4:J4');

                // Style untuk header
                $sheet->getStyle('A1:J4')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');

                // Garis pembatas
                $sheet->getStyle('A5:J5')->getBorders()
                    ->getBottom()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Set column formats
                $sheet->getStyle('C7:E' . ($event->sheet->getHighestRow()))
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                // Apply conditional formatting for status
                $lastRow = $event->sheet->getHighestRow();
                $conditionalStyles = [
                    [
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS,
                        'operator' => \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL,
                        'formula1' => '"Kadaluarsa"',
                        'style' => [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FEF2F2'],
                            ],
                            'font' => ['color' => ['rgb' => '991B1B']],
                        ],
                    ],
                    [
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS,
                        'operator' => \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL,
                        'formula1' => '"Hampir Kadaluarsa"',
                        'style' => [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FEF3C7'],
                            ],
                            'font' => ['color' => ['rgb' => '92400E']],
                        ],
                    ],
                    [
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS,
                        'operator' => \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL,
                        'formula1' => '"Baik"',
                        'style' => [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'ECFDF5'],
                            ],
                            'font' => ['color' => ['rgb' => '065F46']],
                        ],
                    ],
                ];
                $sheet->getStyle('H7:H' . $lastRow)->setConditionalStyles($conditionalStyles);

                // Tambahkan border ke seluruh tabel
                $sheet->getStyle('A6:J' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
