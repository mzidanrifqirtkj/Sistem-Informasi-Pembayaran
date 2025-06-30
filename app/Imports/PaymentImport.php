<?php

namespace App\Imports;

use App\Models\Santri;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class PaymentImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $importType;
    protected $results = [];

    public function __construct($importType = 'individual')
    {
        $this->importType = $importType;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if ($this->importType === 'individual') {
                $this->processIndividualRow($row);
            } else {
                $this->processBulkRow($row);
            }
        }
    }

    protected function processIndividualRow($row)
    {
        try {
            // Validate required fields
            if (empty($row['nis']) || empty($row['nominal'])) {
                throw new \Exception('NIS dan Nominal harus diisi');
            }

            // Find santri
            $santri = Santri::where('nis', trim($row['nis']))->first();
            if (!$santri) {
                throw new \Exception("Santri dengan NIS {$row['nis']} tidak ditemukan");
            }

            if ($santri->status !== 'aktif') {
                throw new \Exception("Santri {$santri->nama_santri} tidak aktif");
            }

            // Parse nominal
            $nominal = $this->parseNominal($row['nominal']);
            if ($nominal <= 0) {
                throw new \Exception('Nominal harus lebih dari 0');
            }

            // Parse tanggal
            $tanggal = $this->parseDate($row['tanggal'] ?? null);

            $this->results[] = [
                'santri' => $santri,
                'nominal' => $nominal,
                'tanggal' => $tanggal,
                'keterangan' => $row['keterangan'] ?? null,
                'status' => 'valid'
            ];

        } catch (\Exception $e) {
            $this->results[] = [
                'nis' => $row['nis'] ?? 'N/A',
                'error' => $e->getMessage(),
                'status' => 'error'
            ];
        }
    }

    protected function processBulkRow($row)
    {
        try {
            // Validate required fields
            if (
                empty($row['tahun']) || empty($row['bulan']) ||
                empty($row['nominal']) || empty($row['list_nis'])
            ) {
                throw new \Exception('Semua field harus diisi');
            }

            // Parse nominal
            $nominal = $this->parseNominal($row['nominal']);
            if ($nominal <= 0) {
                throw new \Exception('Nominal harus lebih dari 0');
            }

            // Parse NIS list
            $nisList = array_map('trim', explode(',', $row['list_nis']));

            foreach ($nisList as $nis) {
                if (empty($nis))
                    continue;

                try {
                    $santri = Santri::where('nis', $nis)->first();
                    if (!$santri) {
                        throw new \Exception("Santri dengan NIS {$nis} tidak ditemukan");
                    }

                    if ($santri->status !== 'aktif') {
                        throw new \Exception("Santri {$santri->nama_santri} tidak aktif");
                    }

                    $this->results[] = [
                        'santri' => $santri,
                        'nominal' => $nominal,
                        'tanggal' => now(),
                        'keterangan' => "Pembayaran {$row['bulan']} {$row['tahun']}",
                        'status' => 'valid'
                    ];

                } catch (\Exception $e) {
                    $this->results[] = [
                        'nis' => $nis,
                        'error' => $e->getMessage(),
                        'status' => 'error'
                    ];
                }
            }

        } catch (\Exception $e) {
            $this->results[] = [
                'nis' => 'Bulk Row',
                'error' => $e->getMessage(),
                'status' => 'error'
            ];
        }
    }

    protected function parseNominal($nominal)
    {
        if (is_numeric($nominal)) {
            return (float) $nominal;
        }

        // Remove currency formatting
        $cleaned = preg_replace('/[^\d]/', '', $nominal);
        return (float) $cleaned;
    }

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

    public function rules(): array
    {
        if ($this->importType === 'individual') {
            return [
                'nis' => 'required|string',
                'nominal' => 'required',
                'tanggal' => 'nullable',
                'keterangan' => 'nullable|string'
            ];
        } else {
            return [
                'tahun' => 'required|integer|min:2020',
                'bulan' => 'required|string',
                'nominal' => 'required',
                'list_nis' => 'required|string'
            ];
        }
    }

    public function customValidationMessages()
    {
        return [
            'nis.required' => 'NIS tidak boleh kosong',
            'nominal.required' => 'Nominal tidak boleh kosong',
            'tahun.required' => 'Tahun tidak boleh kosong',
            'tahun.integer' => 'Tahun harus berupa angka',
            'tahun.min' => 'Tahun minimal 2020',
            'bulan.required' => 'Bulan tidak boleh kosong',
            'list_nis.required' => 'List NIS tidak boleh kosong'
        ];
    }

    public function getResults()
    {
        $valid = collect($this->results)->where('status', 'valid')->all();
        $errors = collect($this->results)->where('status', 'error')->all();

        return [
            'valid' => $valid,
            'errors' => $errors
        ];
    }
}
