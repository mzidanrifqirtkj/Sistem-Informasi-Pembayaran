<?php

namespace App\Exports;

use App\Models\TagihanTerjadwal;
use App\Models\Pembayaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TagihanTerjadwalExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, ShouldAutoSize
{
    private $filters;
    private $exportData;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->exportData = collect();
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        try {
            // Start with base query
            $query = TagihanTerjadwal::with([
                'santri',
                'daftarBiaya.kategoriBiaya',
                'biayaSantri',
                'tahunAjar',
                'pembayarans'
            ]);

            // Apply filters
            $this->applyFilters($query);

            // Process data in chunks to avoid memory issues
            $query->chunk(1000, function ($tagihans) {
                foreach ($tagihans as $tagihan) {
                    // Validate relations to avoid errors
                    if (!$tagihan->santri || !$tagihan->daftarBiaya || !$tagihan->daftarBiaya->kategoriBiaya) {
                        Log::warning("TagihanTerjadwal export: Broken relations found", [
                            'tagihan_id' => $tagihan->id_tagihan_terjadwal
                        ]);
                        continue;
                    }

                    // Calculate payment totals
                    $totalDibayar = $tagihan->pembayarans->sum('nominal_pembayaran');
                    $sisaTagihan = max(0, $tagihan->nominal - $totalDibayar);

                    // Add to export data
                    $this->exportData->push([
                        'nama_santri' => $tagihan->santri->nama_santri,
                        'nis' => $tagihan->santri->nis,
                        'jenis_biaya' => $tagihan->daftarBiaya->kategoriBiaya->nama_kategori,
                        'tahun' => $tagihan->tahun,
                        'tahun_ajar' => $tagihan->tahunAjar->tahun_ajar ?? '-',
                        'nominal_tagihan' => $tagihan->nominal,
                        'total_dibayar' => $totalDibayar,
                        'sisa_tagihan' => $sisaTagihan,
                        'status' => $this->getStatusText($tagihan->status),
                        // 'tanggal_buat' => $tagihan->created_at ? $tagihan->created_at->format('d/m/Y H:i') : '-',
                        // 'biaya_santri_id' => $tagihan->biaya_santri_id,
                        // 'keterangan' => $this->getRincianText($tagihan->rincian)
                    ]);
                }

                // Clear memory after each chunk
                unset($tagihans);
            });

            return $this->exportData;

        } catch (\Exception $e) {
            Log::error('Export TagihanTerjadwal error: ' . $e->getMessage());
            // Return empty collection if error occurs
            return collect();
        }
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query)
    {
        if (!empty($this->filters['tahun'])) {
            $query->where('tahun', $this->filters['tahun']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['jenis_biaya'])) {
            $query->whereHas('daftarBiaya.kategoriBiaya', function ($q) {
                $q->where('id_kategori_biaya', $this->filters['jenis_biaya']);
            });
        }

        if (!empty($this->filters['search'])) {
            $searchTerm = $this->filters['search'];
            $query->whereHas('santri', function ($q) use ($searchTerm) {
                $q->where('nama_santri', 'like', "%{$searchTerm}%")
                    ->orWhere('nis', 'like', "%{$searchTerm}%");
            });
        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Santri',
            'NIS',
            'Jenis Biaya',
            'Tahun',
            'Tahun Ajar',
            'Nominal Tagihan',
            'Total Dibayar',
            'Sisa Tagihan',
            'Status',
            // 'Tanggal Dibuat',
            // 'ID Biaya Santri',
            // 'Keterangan'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['nama_santri'],
            $row['nis'],
            $row['jenis_biaya'],
            $row['tahun'],
            $row['tahun_ajar'],
            $row['nominal_tagihan'],
            $row['total_dibayar'],
            $row['sisa_tagihan'],
            $row['status'],
            // $row['tanggal_buat'],
            // $row['biaya_santri_id'],
            // $row['keterangan']
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Nominal Tagihan
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Total Dibayar
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Sisa Tagihan
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '366092']
                ]
            ],
            // Add borders to all cells
            'A1:L' . ($this->exportData->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get status text in Indonesian
     */
    private function getStatusText(string $status): string
    {
        switch ($status) {
            case 'belum_lunas':
                return 'Belum Lunas';
            case 'dibayar_sebagian':
                return 'Dibayar Sebagian';
            case 'lunas':
                return 'Lunas';
            default:
                return ucfirst($status);
        }
    }

    /**
     * Get rincian text from array
     */
    private function getRincianText($rincian): string
    {
        if (!is_array($rincian) || empty($rincian)) {
            return '-';
        }

        $texts = [];
        foreach ($rincian as $item) {
            if (isset($item['keterangan'])) {
                $nominal = isset($item['nominal']) ? 'Rp ' . number_format($item['nominal'], 0, ',', '.') : '';
                $texts[] = $item['keterangan'] . ($nominal ? ' (' . $nominal . ')' : '');
            }
        }

        return implode('; ', $texts) ?: '-';
    }
}
