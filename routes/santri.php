<?php

// use App\Http\Controllers\Santri\BiayaTerjadwalController;
// use App\Http\Controllers\Santri\DashboardController as SantriDashboardController;
// use App\Http\Controllers\Santri\PembayaranController;
// use App\Http\Controllers\Santri\ProfileController;
// use App\Http\Controllers\Santri\SantriController;
// use App\Http\Controllers\Santri\TagihanBulananController;
// use App\Http\Controllers\Santri\TagihanTerjadwalController;
// use App\Http\Controllers\Santri\TambahanBulananController;
// use Illuminate\Support\Facades\Route;

// Route::middleware(['auth', 'role:santri'])->prefix('santri')->group(function () {
//     // Dashboard Santri
//     Route::get('dashboard', [SantriDashboardController::class, 'index'])->name(' ')->middleware('permission:view_dashboard');

//     // Data Santri (Hanya untuk melihat data diri sendiri)
//     Route::get('santri/{santri}', [SantriController::class, 'show'])->name('santri.data.show')->middleware('permission:view_santri');

//     // Tambahan Bulanan Santri
//     Route::get('tambahan-bulanan/item-santri', [TambahanBulananController::class, 'itemSantri'])->name('santri.tambahan_bulanan.item_santri')->middleware('permission:view_item_santri');

//     // Tagihan Terjadwal Santri
//     Route::get('tagihan-terjadwal', [TagihanTerjadwalController::class, 'index'])->name('santri.tagihan_terjadwal.index')->middleware('permission:view_tagihan_terjadwal');

//     // Tagihan Bulanan Santri
//     Route::get('tagihan-bulanan', [TagihanBulananController::class, 'index'])->name('santri.tagihan_bulanan.index')->middleware('permission:view_tagihan_bulanan');

//     // Riwayat Pembayaran Santri
//     Route::get('pembayaran/riwayat', [PembayaranController::class, 'riwayat'])->name('santri.pembayaran.riwayat')->middleware('permission:view_riwayat_pembayaran');

//     // Biaya Terjadwal Santri
//     Route::get('biaya-terjadwal', [BiayaTerjadwalController::class, 'index'])->name('santri.biaya_terjadwal.index')->middleware('permission:view_biaya_terjadwal');

//     // Profile Santri
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('santri.profile.edit')->middleware('permission:view_profile');
//     Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('santri.profile.update_password')->middleware('permission:edit_profile');
// });
