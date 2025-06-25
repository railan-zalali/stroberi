<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Carbon\Carbon;

class LaporanKeuanganExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents, WithCustomStartCell
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function startCell(): string
    {
        return 'A6';
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

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $startDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($this->tahun, $this->bulan, 1)->endOfMonth();

                $totalPemasukan = Transaksi::where('jenis', 'pemasukan')
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->sum('jumlah');

                $totalPengeluaran = Transaksi::where('jenis', 'pengeluaran')
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->sum('jumlah');

                $laba = $totalPemasukan - $totalPengeluaran;

                // Judul laporan
                $sheet->setCellValue('A1', 'LAPORAN KEUANGAN');
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Periode laporan
                $periode = 'Periode: ' . Carbon::createFromDate($this->tahun, $this->bulan, 1)->format('F Y');
                $sheet->setCellValue('A2', $periode);
                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Ringkasan
                $sheet->setCellValue('A4', 'Total Pemasukan: Rp ' . number_format($totalPemasukan, 0, ',', '.'));
                $sheet->setCellValue('C4', 'Total Pengeluaran: Rp ' . number_format($totalPengeluaran, 0, ',', '.'));
                $sheet->setCellValue('E4', 'Laba: Rp ' . number_format($laba, 0, ',', '.'));

                $sheet->mergeCells('A4:B4');
                $sheet->mergeCells('C4:D4');
                $sheet->mergeCells('E4:G4');

                // Style untuk header
                $sheet->getStyle('A1:G4')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');

                // Garis pembatas
                $sheet->getStyle('A5:G5')->getBorders()
                    ->getBottom()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Set column format
                $sheet->getStyle('E7:E' . ($event->sheet->getHighestRow()))
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                // Apply conditional formatting for Pemasukan and Pengeluaran
                $lastRow = $event->sheet->getHighestRow();
                $conditionalStyles = [
                    [
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EQUAL,
                        'operator' => \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL,
                        'text' => 'Pemasukan',
                        'style' => [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'ECFDF5'],
                            ],
                            'font' => ['color' => ['rgb' => '065F46']],
                        ],
                    ],
                    [
                        'type' => \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EQUAL,
                        'operator' => \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_EQUAL,
                        'text' => 'Pengeluaran',
                        'style' => [
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FEF2F2'],
                            ],
                            'font' => ['color' => ['rgb' => '991B1B']],
                        ],
                    ],
                ];
                $sheet->getStyle('C7:C' . $lastRow)->setConditionalStyles($conditionalStyles);

                // Tambahkan border ke seluruh tabel
                $sheet->getStyle('A6:G' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
