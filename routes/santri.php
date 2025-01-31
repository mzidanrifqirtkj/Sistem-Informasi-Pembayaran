<?php

use App\Http\Controllers\Santri\BiayaTerjadwalController;
use App\Http\Controllers\Santri\DashboardController as SantriDashboardController;
use App\Http\Controllers\Santri\PembayaranController;
use App\Http\Controllers\Santri\SantriController;
use App\Http\Controllers\Santri\TagihanBulananController;
use App\Http\Controllers\Santri\TagihanTerjadwalController;
use App\Http\Controllers\Santri\TambahanBulananController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:santri'])->prefix('santri')->group(function () {
    Route::get('dashboard', [SantriDashboardController::class, 'index'])->name('santri.dashboard');

    Route::get('tambahan-bulanan/item-santri', [TambahanBulananController::class, 'itemSantri'])->name('santri.tambahan_bulanan.item_santri');

    Route::get('tagihan-terjadwal', [TagihanTerjadwalController::class, 'index'])->name('santri.tagihan_terjadwal.index');

    Route::get('tagihan-bulanan', [TagihanBulananController::class, 'index'])->name('santri.tagihan_bulanan.index');

    Route::get('pembayaran', [PembayaranController::class, 'index'])->name('santri.pembayaran.index');

    Route::get('pembayaran/riwayat', [PembayaranController::class, 'riwayat'])->name('santri.pembayaran.riwayat');

    Route::get('santri/{santri}', [SantriController::class, 'show'])->name('santri.santri.show');

    Route::get('biaya-terjadwal', [BiayaTerjadwalController::class, 'index'])->name('santri.biaya_terjadwal.index');
});
