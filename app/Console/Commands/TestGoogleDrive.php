<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleDriveBackupService;

class TestGoogleDrive extends Command
{
    protected $signature = 'test:google-drive';
    protected $description = 'Test Google Drive connection';

    public function handle()
    {
        try {
            $this->info('Testing Google Drive connection...');

            $service = new GoogleDriveBackupService();

            // Create test file
            $testContent = "Test backup file created at: " . now()->toString();
            $testFile = storage_path('app/test_backup.txt');
            file_put_contents($testFile, $testContent);

            // Upload to Google Drive
            $this->info('Uploading test file...');
            $result = $service->uploadFile($testFile, 'test_backup_' . now()->format('Y-m-d_H-i-s') . '.txt', 'text/plain');

            $this->info('✅ Success! File uploaded with ID: ' . $result->id);

            // Clean up
            unlink($testFile);

            $this->info('Google Drive connection is working properly!');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Please check your credentials and try again.');
        }
    }
}
