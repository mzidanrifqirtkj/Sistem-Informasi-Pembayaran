<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TagihanBulanan;
use App\Models\TagihanTerjadwal;
use App\Models\Pembayaran;
use App\Models\PaymentAllocation;
use App\Services\GoogleDriveBackupService;
use App\Exports\TagihanBulananExport;
use App\Exports\TagihanTerjadwalExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupMonthlyTagihan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:monthly-tagihan
                            {--month= : Specific month to backup (default: previous month)}
                            {--year= : Specific year to backup (default: current year)}
                            {--type=all : Type of backup (all, bulanan, terjadwal)}
                            {--dry-run : Run without uploading to Google Drive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup tagihan data to Google Drive monthly';

    protected $googleDriveService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GoogleDriveBackupService $googleDriveService)
    {
        parent::__construct();
        $this->googleDriveService = $googleDriveService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = now();

        // Get backup parameters
        $month = $this->option('month') ?? Carbon::now()->subMonth()->format('M');
        $year = $this->option('year') ?? Carbon::now()->year;
        $type = $this->option('type');
        $isDryRun = $this->option('dry-run');

        $this->info('Starting monthly backup...');
        $this->info("Backup Period: {$month} {$year}");
        $this->info("Backup Type: {$type}");

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No files will be uploaded');
        }

        try {
            $results = [];

            // Backup Tagihan Bulanan
            if (in_array($type, ['all', 'bulanan'])) {
                $this->info('Backing up Tagihan Bulanan...');
                $result = $this->backupTagihanBulanan($month, $year, $isDryRun);
                $results['bulanan'] = $result;
            }

            // Backup Tagihan Terjadwal
            if (in_array($type, ['all', 'terjadwal'])) {
                $this->info('Backing up Tagihan Terjadwal...');
                $result = $this->backupTagihanTerjadwal($month, $year, $isDryRun);
                $results['terjadwal'] = $result;
            }

            // Cleanup old local files
            $this->cleanupLocalFiles();

            // Show summary
            $this->showSummary($results, $startTime);

            // Log success
            Log::info('Monthly backup completed', [
                'type' => $type,
                'month' => $month,
                'year' => $year,
                'results' => $results,
                'duration' => now()->diffInSeconds($startTime) . ' seconds'
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());

            Log::error('Monthly backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Backup Tagihan Bulanan
     */
    protected function backupTagihanBulanan($month, $year, $isDryRun)
    {
        $this->line('Generating Tagihan Bulanan export...');

        // Create export with filters
        $filters = [
            'tahun' => $year,
            'bulan' => $month
        ];

        $fileName = "Backup_Tagihan_Bulanan_{$month}_{$year}.xlsx";
        $filePath = "backups/tagihan_bulanan/{$fileName}";

        // Generate Excel file
        Excel::store(new TagihanBulananExport($filters), $filePath, 'local');

        $fullPath = Storage::disk('local')->path($filePath);
        $fileSize = filesize($fullPath);

        $this->info("File generated: {$fileName} (" . $this->formatBytes($fileSize) . ")");

        // Upload to Google Drive
        if (!$isDryRun) {
            $this->line('Uploading to Google Drive...');

            $uploadResult = $this->googleDriveService->uploadMonthlyBackup(
                'tagihan_bulanan',
                $fullPath,
                $month,
                $year
            );

            $this->info("✓ Uploaded to Google Drive (ID: {$uploadResult->id})");
        }

        // Get statistics
        $stats = [
            'total_records' => TagihanBulanan::where('tahun', $year)
                ->where('bulan', $month)
                ->count(),
            'file_size' => $fileSize,
            'file_name' => $fileName
        ];

        return $stats;
    }

    /**
     * Backup Tagihan Terjadwal
     */
    protected function backupTagihanTerjadwal($month, $year, $isDryRun)
    {
        $this->line('Generating Tagihan Terjadwal export...');

        // For Tagihan Terjadwal, we backup quarterly
        $quarter = ceil(TagihanBulanan::$bulanMapping[$month] / 3);
        $quarterName = "Q{$quarter}";

        $filters = [
            'tahun' => $year,
            'quarter' => $quarter
        ];

        $fileName = "Backup_Tagihan_Terjadwal_{$quarterName}_{$year}.xlsx";
        $filePath = "backups/tagihan_terjadwal/{$fileName}";

        // Generate Excel file
        Excel::store(new TagihanTerjadwalExport($filters), $filePath, 'local');

        $fullPath = Storage::disk('local')->path($filePath);
        $fileSize = filesize($fullPath);

        $this->info("File generated: {$fileName} (" . $this->formatBytes($fileSize) . ")");

        // Upload to Google Drive
        if (!$isDryRun) {
            $this->line('Uploading to Google Drive...');

            $uploadResult = $this->googleDriveService->uploadMonthlyBackup(
                'tagihan_terjadwal',
                $fullPath,
                $quarterName,
                $year
            );

            $this->info("✓ Uploaded to Google Drive (ID: {$uploadResult->id})");
        }

        // Get statistics
        $stats = [
            'total_records' => TagihanTerjadwal::where('tahun', $year)->count(),
            'file_size' => $fileSize,
            'file_name' => $fileName
        ];

        return $stats;
    }

    /**
     * Cleanup old local backup files
     */
    protected function cleanupLocalFiles()
    {
        $this->line('Cleaning up old local files...');

        $directories = [
            'backups/tagihan_bulanan',
            'backups/tagihan_terjadwal'
        ];

        $deletedCount = 0;

        foreach ($directories as $dir) {
            $files = Storage::disk('local')->files($dir);

            foreach ($files as $file) {
                $lastModified = Storage::disk('local')->lastModified($file);
                $daysOld = Carbon::createFromTimestamp($lastModified)->diffInDays(now());

                // Delete files older than 7 days
                if ($daysOld > 7) {
                    Storage::disk('local')->delete($file);
                    $deletedCount++;
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} old backup files");
        }
    }

    /**
     * Show backup summary
     */
    protected function showSummary($results, $startTime)
    {
        $this->newLine();
        $this->info('=== BACKUP SUMMARY ===');

        foreach ($results as $type => $stats) {
            $this->line("• Tagihan " . ucfirst($type) . ":");
            $this->line("  - Records: " . number_format($stats['total_records']));
            $this->line("  - File Size: " . $this->formatBytes($stats['file_size']));
            $this->line("  - File Name: " . $stats['file_name']);
        }

        $duration = now()->diffInSeconds($startTime);
        $this->newLine();
        $this->info("Total Duration: {$duration} seconds");
        $this->info('✓ Backup completed successfully!');
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
