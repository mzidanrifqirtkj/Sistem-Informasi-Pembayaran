<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PaymentTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $type;

    public function __construct($type = 'individual')
    {
        $this->type = $type;
    }

    public function array(): array
    {
        if ($this->type === 'individual') {
            return [
                ['001234', '500000', date('Y-m-d'), 'Bayar SPP 3 bulan'],
                ['001235', '300000', date('Y-m-d'), 'Bayar SPP 2 bulan'],
                ['', '', '', ''],
                ['', '', '', '** Hapus baris contoh di atas sebelum import **']
            ];
        } else {
            return [
                [date('Y'), 'Jan', '150000', '001234,001235,001236,001237'],
                [date('Y'), 'Feb', '150000', '001234,001235'],
                ['', '', '', ''],
                ['', '', '', '** Hapus baris contoh di atas sebelum import **']
            ];
        }
    }

    public function headings(): array
    {
        if ($this->type === 'individual') {
            return ['NIS', 'Nominal', 'Tanggal', 'Keterangan'];
        } else {
            return ['Tahun', 'Bulan', 'Nominal', 'List_NIS'];
        }
    }

    public function columnWidths(): array
    {
        if ($this->type === 'individual') {
            return [
                'A' => 15,
                'B' => 20,
                'C' => 15,
                'D' => 40,
            ];
        } else {
            return [
                'A' => 10,
                'B' => 10,
                'C' => 20,
                'D' => 50,
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ]);

        // Style data cells
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A2:D{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add instructions
                $sheet->setCellValue('F1', 'PETUNJUK PENGISIAN:');
                $sheet->getStyle('F1')->getFont()->setBold(true);

                if ($this->type === 'individual') {
                    $sheet->setCellValue('F2', '1. Isi NIS santri (wajib)');
                    $sheet->setCellValue('F3', '2. Isi nominal pembayaran tanpa Rp dan titik');
                    $sheet->setCellValue('F4', '3. Format tanggal: YYYY-MM-DD (contoh: ' . date('Y-m-d') . ')');
                    $sheet->setCellValue('F5', '4. Keterangan bersifat opsional');
                    $sheet->setCellValue('F6', '5. Hapus baris contoh sebelum import');
                } else {
                    $sheet->setCellValue('F2', '1. Tahun format: YYYY (contoh: ' . date('Y') . ')');
                    $sheet->setCellValue('F3', '2. Bulan format: Jan, Feb, Mar, dst');
                    $sheet->setCellValue('F4', '3. Nominal tanpa Rp dan titik');
                    $sheet->setCellValue('F5', '4. List NIS dipisah koma, tanpa spasi');
                    $sheet->setCellValue('F6', '5. Hapus baris contoh sebelum import');
                }

                // Format instructions column
                $sheet->getColumnDimension('F')->setWidth(40);
                $sheet->getStyle('F1:F6')->getAlignment()->setWrapText(true);

                // Freeze header row
                $sheet->freezePane('A2');
            }
        ];
    }
}
