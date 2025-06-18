<?php

namespace App\Exports;

use App\Models\Santri;
use App\Models\TagihanBulanan;
use App\Models\Pembayaran;
use App\Models\PaymentAllocation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\DB;

class TagihanBulananExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $tahun;
    protected $rowNumber = 0;
    protected $totalRows = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
        $this->tahun = $filters['tahun'] ?? date('Y');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Santri::with([
            'riwayatKelas.mapelKelas.kelas',
            'tagihanBulanan' => function ($q) {
                $q->where('tahun', $this->tahun);

                if (!empty($this->filters['bulan'])) {
                    $q->where('bulan', $this->filters['bulan']);
                }

                if (!empty($this->filters['status'])) {
                    $q->where('status', $this->filters['status']);
                }

                $q->orderBy('bulan_urutan');
            },
            'tagihanBulanan.pembayarans',
            'tagihanBulanan.paymentAllocations'
        ])
            ->where('status', 'aktif');

        // Apply filters
        if (!empty($this->filters['nama_santri'])) {
            $query->where(function ($q) {
                $q->where('nama_santri', 'like', '%' . $this->filters['nama_santri'] . '%')
                    ->orWhere('nis', 'like', '%' . $this->filters['nama_santri'] . '%');
            });
        }

        if (!empty($this->filters['kelas_id'])) {
            if ($this->filters['kelas_id'] === 'tanpa_kelas') {
                $query->whereDoesntHave('riwayatKelas');
            } else {
                $query->whereHas('riwayatKelas.mapelKelas.kelas', function ($q) {
                    $q->where('id_kelas', $this->filters['kelas_id']);
                });
            }
        }

        if (!empty($this->filters['status'])) {
            $query->whereHas('tagihanBulanan', function ($q) {
                $q->where('tahun', $this->tahun)
                    ->where('status', $this->filters['status']);
            });
        }

        $santris = $query->orderBy('nama_santri')->get();

        // Process data
        $santris->each(function ($santri) {
            $tagihans = $santri->tagihanBulanan->where('tahun', $this->tahun);

            $santri->total_tagihan = $tagihans->count();
            $santri->total_lunas = $tagihans->where('status', 'lunas')->count();
            $santri->total_sebagian = $tagihans->where('status', 'dibayar_sebagian')->count();
            $santri->total_belum = $tagihans->where('status', 'belum_lunas')->count();
            $santri->total_nominal = $tagihans->sum('nominal');
            $santri->total_dibayar = $tagihans->sum('total_pembayaran');
            $santri->total_kekurangan = $santri->total_nominal - $santri->total_dibayar;
            $santri->persentase = $santri->total_nominal > 0
                ? round(($santri->total_dibayar / $santri->total_nominal) * 100, 2)
                : 0;
        });

        $this->totalRows = $santris->count();

        return $santris;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Santri',
            'Total Tagihan',
            'Total Lunas',
            'Total Sebagian',
            'Total Belum',
            'Total Nominal',
            'Total Dibayar',
            'Total Kekurangan',
            'Persentase'
        ];
    }

    /**
     * @param mixed $santri
     * @return array
     */
    public function map($santri): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $santri->nis,
            $santri->nama_santri,
            $santri->total_tagihan . ' bulan',
            $santri->total_lunas . ' bulan',
            $santri->total_sebagian . ' bulan',
            $santri->total_belum . ' bulan',
            $santri->total_nominal,
            $santri->total_dibayar,
            $santri->total_kekurangan,
            $santri->persentase . '%'
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $title = 'Tagihan Bulanan ' . $this->tahun;

        if (!empty($this->filters['bulan'])) {
            $title .= ' - ' . $this->filters['bulan'];
        }

        return $title;
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E7E7E7']
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $this->totalRows + 1; // +1 for header
                $lastColumn = 'K'; // Persentase column
    
                // Set borders
                $sheet->getStyle("A1:{$lastColumn}{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                // Format currency columns
                $sheet->getStyle("H2:J{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"??_);_(@_)');

                // Center align some columns
                $sheet->getStyle("A1:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B1:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D1:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("K1:K{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add summary row
                $summaryRow = $lastRow + 2;
                $sheet->setCellValue("A{$summaryRow}", 'TOTAL');
                $sheet->mergeCells("A{$summaryRow}:C{$summaryRow}");

                // Sum formulas
                $sheet->setCellValue("D{$summaryRow}", "=SUM(D2:D{$lastRow})");
                $sheet->setCellValue("E{$summaryRow}", "=SUM(E2:E{$lastRow})");
                $sheet->setCellValue("F{$summaryRow}", "=SUM(F2:F{$lastRow})");
                $sheet->setCellValue("G{$summaryRow}", "=SUM(G2:G{$lastRow})");
                $sheet->setCellValue("H{$summaryRow}", "=SUM(H2:H{$lastRow})");
                $sheet->setCellValue("I{$summaryRow}", "=SUM(I2:I{$lastRow})");
                $sheet->setCellValue("J{$summaryRow}", "=SUM(J2:J{$lastRow})");

                // Average percentage
                $sheet->setCellValue("K{$summaryRow}", "=AVERAGE(K2:K{$lastRow})");

                // Style summary row
                $sheet->getStyle("A{$summaryRow}:K{$summaryRow}")
                    ->getFont()
                    ->setBold(true);

                $sheet->getStyle("A{$summaryRow}:K{$summaryRow}")
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('FFE699');

                // Add filter information
                $infoRow = $summaryRow + 2;
                $sheet->setCellValue("A{$infoRow}", 'Filter Information:');
                $sheet->getStyle("A{$infoRow}")->getFont()->setBold(true);

                $infoRow++;
                $sheet->setCellValue("A{$infoRow}", 'Tahun: ' . $this->tahun);

                if (!empty($this->filters['bulan'])) {
                    $infoRow++;
                    $sheet->setCellValue("A{$infoRow}", 'Bulan: ' . $this->filters['bulan']);
                }

                if (!empty($this->filters['status'])) {
                    $infoRow++;
                    $statusText = ucfirst(str_replace('_', ' ', $this->filters['status']));
                    $sheet->setCellValue("A{$infoRow}", 'Status: ' . $statusText);
                }

                // Export date
                $infoRow++;
                $sheet->setCellValue("A{$infoRow}", 'Tanggal Export: ' . now()->format('d/m/Y H:i:s'));

                // Freeze panes
                $sheet->freezePane('D2');
            },
        ];
    }
}
