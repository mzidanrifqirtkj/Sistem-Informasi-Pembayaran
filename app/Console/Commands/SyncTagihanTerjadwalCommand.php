<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TagihanTerjadwal;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncTagihanTerjadwalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:sync-terjadwal
                           {--dry-run : Show what would be changed without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize TagihanTerjadwal status based on payments';

    private $stats = [
        'total' => 0,
        'no_change' => 0,
        'updated_to_lunas' => 0,
        'updated_to_sebagian' => 0,
        'updated_to_belum_lunas' => 0,
        'errors' => 0
    ];

    private $errors = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('🔄 Syncing TagihanTerjadwal status...');
        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
        }
        $this->newLine();

        try {
            DB::beginTransaction();

            // Get total count for progress bar
            $totalTagihans = TagihanTerjadwal::count();
            $this->stats['total'] = $totalTagihans;

            if ($totalTagihans === 0) {
                $this->warn('No TagihanTerjadwal records found.');
                return 0;
            }

            // Create progress bar
            $progressBar = $this->output->createProgressBar($totalTagihans);
            $progressBar->setFormat('verbose');
            $progressBar->start();

            // Process in chunks to avoid memory issues
            TagihanTerjadwal::with('pembayarans')
                ->chunk(100, function ($tagihans) use ($isDryRun, $progressBar) {
                    foreach ($tagihans as $tagihan) {
                        $this->processTagihan($tagihan, $isDryRun);
                        $progressBar->advance();
                    }
                });

            $progressBar->finish();
            $this->newLine(2);

            if (!$isDryRun) {
                DB::commit();
                $this->info('✅ Changes committed to database');
            } else {
                DB::rollBack();
                $this->info('📋 Dry run completed - no changes made');
            }

            $this->displayResults();
            $this->logResults($isDryRun);

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error occurred: ' . $e->getMessage());
            Log::error('SyncTagihanTerjadwal command error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Process individual tagihan
     */
    private function processTagihan(TagihanTerjadwal $tagihan, bool $isDryRun): void
    {
        try {
            // Calculate total pembayaran
            $totalPembayaran = $tagihan->pembayarans->sum('nominal_pembayaran');
            $nominalTagihan = $tagihan->nominal;

            // Calculate expected status
            $expectedStatus = $this->calculateStatus($totalPembayaran, $nominalTagihan);
            $currentStatus = $tagihan->status;

            if ($currentStatus === $expectedStatus) {
                $this->stats['no_change']++;
            } else {
                // Track status changes
                switch ($expectedStatus) {
                    case 'lunas':
                        $this->stats['updated_to_lunas']++;
                        break;
                    case 'dibayar_sebagian':
                        $this->stats['updated_to_sebagian']++;
                        break;
                    case 'belum_lunas':
                        $this->stats['updated_to_belum_lunas']++;
                        break;
                }

                // Update if not dry run
                if (!$isDryRun) {
                    $tagihan->update(['status' => $expectedStatus]);
                }

                // Log the change
                Log::info('TagihanTerjadwal status sync', [
                    'id' => $tagihan->id_tagihan_terjadwal,
                    'santri_id' => $tagihan->santri_id,
                    'old_status' => $currentStatus,
                    'new_status' => $expectedStatus,
                    'total_pembayaran' => $totalPembayaran,
                    'nominal_tagihan' => $nominalTagihan,
                    'dry_run' => $isDryRun
                ]);
            }

        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->errors[] = [
                'id' => $tagihan->id_tagihan_terjadwal,
                'error' => $e->getMessage()
            ];

            Log::error('Error processing TagihanTerjadwal in sync command', [
                'id' => $tagihan->id_tagihan_terjadwal,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate status based on payment amount
     */
    private function calculateStatus(float $totalPembayaran, float $nominalTagihan): string
    {
        if ($totalPembayaran == 0) {
            return 'belum_lunas';
        } elseif ($totalPembayaran >= $nominalTagihan) {
            return 'lunas';
        } else {
            return 'dibayar_sebagian';
        }
    }

    /**
     * Display results in formatted table
     */
    private function displayResults(): void
    {
        $this->info('📊 Sync Results:');

        $headers = ['Status Changes', 'Count'];
        $rows = [
            ['No change required', number_format($this->stats['no_change'])],
            ['Updated to Lunas', number_format($this->stats['updated_to_lunas'])],
            ['Updated to Sebagian', number_format($this->stats['updated_to_sebagian'])],
            ['Updated to Belum Lunas', number_format($this->stats['updated_to_belum_lunas'])],
            ['Errors encountered', number_format($this->stats['errors'])],
        ];

        $this->table($headers, $rows);

        // Show errors if any
        if ($this->stats['errors'] > 0) {
            $this->newLine();
            $this->warn('⚠️  Errors found:');
            foreach ($this->errors as $error) {
                $this->line("- ID {$error['id']}: {$error['error']}");
            }
        }

        $totalUpdated = $this->stats['updated_to_lunas'] +
            $this->stats['updated_to_sebagian'] +
            $this->stats['updated_to_belum_lunas'];

        $this->newLine();
        $this->info("✅ Sync completed: {$totalUpdated} records updated out of {$this->stats['total']} total");
    }

    /**
     * Log results to file
     */
    private function logResults(bool $isDryRun): void
    {
        $logData = [
            'command' => 'tagihan:sync-terjadwal',
            'dry_run' => $isDryRun,
            'timestamp' => now()->toDateTimeString(),
            'stats' => $this->stats,
            'errors' => $this->errors
        ];

        Log::channel('single')->info('TagihanTerjadwal sync completed', $logData);
    }
}
