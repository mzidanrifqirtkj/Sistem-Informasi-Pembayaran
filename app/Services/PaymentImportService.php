<?php

namespace App\Services;

use App\Models\Santri;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class PaymentImportService
{
    /**
     * Parse import file
     */
    public function parseFile($file, $type)
    {
        $data = Excel::toArray([], $file)[0]; // Get first sheet

        // Remove header row
        $headers = array_shift($data);

        // Validate headers based on type
        $this->validateHeaders($headers, $type);

        $parsedData = [];

        if ($type === 'individual') {
            // Format: NIS | Nominal | Tanggal | Keterangan
            foreach ($data as $row) {
                if (empty($row[0]))
                    continue; // Skip empty rows

                $parsedData[] = [
                    'nis' => trim($row[0]),
                    'nominal' => (float) str_replace(['Rp', '.', ','], '', $row[1]),
                    'tanggal' => $this->parseDate($row[2]),
                    'keterangan' => $row[3] ?? null
                ];
            }
        } else {
            // Format: Tahun | Bulan | Nominal | List_NIS
            foreach ($data as $row) {
                if (empty($row[3]))
                    continue; // Skip empty rows

                $nisList = explode(',', $row[3]);
                foreach ($nisList as $nis) {
                    $parsedData[] = [
                        'nis' => trim($nis),
                        'nominal' => (float) str_replace(['Rp', '.', ','], '', $row[2]),
                        'tanggal' => now(),
                        'keterangan' => "Pembayaran {$row[1]} {$row[0]}"
                    ];
                }
            }
        }

        return $parsedData;
    }

    /**
     * Validate headers
     */
    protected function validateHeaders($headers, $type)
    {
        $expectedHeaders = [
            'individual' => ['NIS', 'Nominal', 'Tanggal', 'Keterangan'],
            'bulk' => ['Tahun', 'Bulan', 'Nominal', 'List_NIS']
        ];

        $expected = $expectedHeaders[$type];
        $headers = array_map('trim', array_slice($headers, 0, count($expected)));

        if ($headers !== $expected) {
            throw new \Exception(
                'Format header tidak sesuai. Expected: ' . implode(' | ', $expected)
            );
        }
    }

    /**
     * Parse date from various formats
     */
    protected function parseDate($dateString)
    {
        if (empty($dateString)) {
            return now();
        }

        // Try various date formats
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'd M Y',
            'd F Y'
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateString);
            } catch (\Exception $e) {
                continue;
            }
        }

        // If numeric (Excel date)
        if (is_numeric($dateString)) {
            return Carbon::createFromTimestamp(
                ($dateString - 25569) * 86400
            );
        }

        throw new \Exception("Format tanggal tidak valid: {$dateString}");
    }

    /**
     * Generate import report
     */
    public function generateReport($results)
    {
        $timestamp = now()->format('YmdHis');
        $filename = "import_report_{$timestamp}.html";

        $html = view('pembayaran.import-report', compact('results'))->render();

        Storage::put("public/reports/{$filename}", $html);

        return Storage::url("reports/{$filename}");
    }
}
