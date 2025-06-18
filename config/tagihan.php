<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tagihan Configuration
    |--------------------------------------------------------------------------
    */

    // Payment duplicate detection window (in minutes)
    'payment_duplicate_window' => env('PAYMENT_DUPLICATE_WINDOW', 5),

    // Cache TTL (in seconds)
    'cache_ttl' => env('TAGIHAN_CACHE_TTL', 300),

    // Backup retention days
    'backup_retention_days' => env('BACKUP_RETENTION_DAYS', 1825), // 5 years

    // Google Drive folder ID for backups
    'google_drive_folder_id' => env('GOOGLE_DRIVE_FOLDER_ID'),

    // Categories for bulanan
    'kategori_bulanan' => ['tambahan', 'jalur'],

    // Categories for terjadwal
    'kategori_terjadwal' => ['tahunan', 'insidental'],

    // Month mapping
    'months' => [
        'Jan' => 1,
        'Feb' => 2,
        'Mar' => 3,
        'Apr' => 4,
        'May' => 5,
        'Jun' => 6,
        'Jul' => 7,
        'Aug' => 8,
        'Sep' => 9,
        'Oct' => 10,
        'Nov' => 11,
        'Dec' => 12
    ],

    // Status colors
    'status_colors' => [
        'lunas' => 'success',
        'dibayar_sebagian' => 'warning',
        'belum_lunas' => 'danger'
    ],

    // Export settings
    'export' => [
        'chunk_size' => 1000,
        'memory_limit' => '512M',
        'time_limit' => 300
    ],

    // Bulk operation settings
    'bulk' => [
        'batch_size' => 100,
        'lock_timeout' => 600, // 10 minutes
    ]
];
