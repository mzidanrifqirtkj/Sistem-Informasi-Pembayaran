<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleDriveBackupService
{
    protected $client;
    protected $service;
    protected $folderId;

    public function __construct()
    {
        $this->initializeClient();
    }

    /**
     * Initialize Google Client
     */
    protected function initializeClient()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri('https://developers.google.com/oauthplayground');
        $this->client->setScopes(['https://www.googleapis.com/auth/drive.file']);
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');

        // Set refresh token
        $this->client->refreshToken(config('services.google.refresh_token'));

        $this->service = new Drive($this->client);
        $this->folderId = config('services.google.folder_id');
    }

    /**
     * Upload file to Google Drive
     */
    public function uploadFile($filePath, $fileName, $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    {
        try {
            // Create folder structure if needed
            $folderId = $this->getOrCreateYearFolder();

            $fileMetadata = new DriveFile([
                'name' => $fileName,
                'parents' => [$folderId]
            ]);

            $content = file_get_contents($filePath);

            $file = $this->service->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id, name, createdTime'
            ]);

            Log::info('File uploaded to Google Drive', [
                'file_id' => $file->id,
                'file_name' => $file->name,
                'created_time' => $file->createdTime
            ]);

            return $file;

        } catch (\Exception $e) {
            Log::error('Google Drive upload failed', [
                'error' => $e->getMessage(),
                'file' => $fileName
            ]);

            throw $e;
        }
    }

    /**
     * Get or create year folder
     */
    protected function getOrCreateYearFolder()
    {
        $year = Carbon::now()->year;
        $folderName = "Backup_{$year}";

        // Search for existing folder
        $response = $this->service->files->listFiles([
            'q' => "mimeType='application/vnd.google-apps.folder' and name='{$folderName}' and '{$this->folderId}' in parents and trashed=false",
            'fields' => 'files(id, name)'
        ]);

        if (count($response->files) > 0) {
            return $response->files[0]->id;
        }

        // Create new folder
        $fileMetadata = new DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$this->folderId]
        ]);

        $folder = $this->service->files->create($fileMetadata, [
            'fields' => 'id'
        ]);

        return $folder->id;
    }

    /**
     * Create subfolder for specific backup type
     */
    protected function getOrCreateSubfolder($parentId, $folderName)
    {
        // Search for existing folder
        $response = $this->service->files->listFiles([
            'q' => "mimeType='application/vnd.google-apps.folder' and name='{$folderName}' and '{$parentId}' in parents and trashed=false",
            'fields' => 'files(id, name)'
        ]);

        if (count($response->files) > 0) {
            return $response->files[0]->id;
        }

        // Create new folder
        $fileMetadata = new DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId]
        ]);

        $folder = $this->service->files->create($fileMetadata, [
            'fields' => 'id'
        ]);

        return $folder->id;
    }

    /**
     * Upload monthly backup
     */
    public function uploadMonthlyBackup($type, $filePath, $bulan, $tahun)
    {
        $yearFolder = $this->getOrCreateYearFolder();
        $typeFolder = $this->getOrCreateSubfolder($yearFolder, ucfirst($type));

        $fileName = "Backup_{$type}_{$bulan}_{$tahun}.xlsx";

        $fileMetadata = new DriveFile([
            'name' => $fileName,
            'parents' => [$typeFolder]
        ]);

        $content = file_get_contents($filePath);

        return $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'uploadType' => 'multipart',
            'fields' => 'id, name, createdTime'
        ]);
    }

    /**
     * List backup files
     */
    public function listBackups($year = null)
    {
        $query = "mimeType!='application/vnd.google-apps.folder' and trashed=false";

        if ($year) {
            $query .= " and name contains '{$year}'";
        }

        $response = $this->service->files->listFiles([
            'q' => $query,
            'orderBy' => 'createdTime desc',
            'fields' => 'files(id, name, createdTime, size)',
            'pageSize' => 100
        ]);

        return $response->files;
    }

    /**
     * Delete old backups
     */
    public function cleanupOldBackups($daysToKeep = 1825) // 5 years
    {
        $cutoffDate = Carbon::now()->subDays($daysToKeep)->toIso8601String();

        $response = $this->service->files->listFiles([
            'q' => "createdTime < '{$cutoffDate}' and trashed=false",
            'fields' => 'files(id, name, createdTime)'
        ]);

        $deletedCount = 0;

        foreach ($response->files as $file) {
            try {
                $this->service->files->delete($file->id);
                $deletedCount++;

                Log::info('Old backup deleted', [
                    'file_name' => $file->name,
                    'created_time' => $file->createdTime
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to delete old backup', [
                    'file_name' => $file->name,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $deletedCount;
    }
}
