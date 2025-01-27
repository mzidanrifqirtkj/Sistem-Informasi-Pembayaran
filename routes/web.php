<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SantriController;
use App\Http\Controllers\Admin\KategoriSantriController;
use App\Http\Controllers\Admin\BiayaTerjadwalController;
use App\Http\Controllers\Admin\TagihanBulananController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\TagihanTerjadwalController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\UserController;
use App\Models\BiayaTahunan;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('profile', [ProfileController::class, 'index'])->name('admin.profile');


Route::get('santri', [SantriController::class, 'index'])->name('admin.santri.index');
Route::get('santri/create', [SantriController::class, 'create'])->name('admin.santri.create');
Route::post('santri', [SantriController::class, 'store'])->name('admin.santri.store');
Route::get('/santri/import', [SantriController::class, 'importForm'])->name('admin.santri.importForm');
Route::post('/santri/import', [SantriController::class, 'import'])->name('admin.santri.import');
Route::get('santri/{santri}/edit', [SantriController::class, 'edit'])->name('admin.santri.edit');
Route::put('santri/{santri}', [SantriController::class, 'update'])->name('admin.santri.update');
Route::delete('santri/{santri}', [SantriController::class, 'destroy'])->name('admin.santri.destroy');
Route::get('santri/{santri}', [SantriController::class, 'show'])->name('admin.santri.show');

Route::get('user', [UserController::class, 'index'])->name('admin.user.index');
Route::get('user/create', [UserController::class, 'create'])->name('admin.user.create');
Route::post('user', [UserController::class, 'store'])->name('admin.user.store');
Route::get('/user/import', [UserController::class, 'importForm'])->name('admin.user.importForm');
Route::post('/user/import', [UserController::class, 'import'])->name('admin.user.import');
Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('admin.user.edit');
Route::put('user/{id}', [UserController::class, 'update'])->name('admin.user.update');
Route::delete('user/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
Route::get('user', [UserController::class, 'index'])->name('admin.user.index');

Route::get('kategori-santri/create', [KategoriSantriController::class, 'create'])->name('admin.kategori.create');
Route::post('kategori-santri', [KategoriSantriController::class, 'store'])->name('admin.kategori.store');
Route::get('kategori-santri/{id}/edit', [KategoriSantriController::class, 'edit'])->name('admin.kategori.edit');
Route::put('kategori-santri/{id}', [KategoriSantriController::class, 'update'])->name('admin.kategori.update');
Route::delete('kategori-santri/{id}', [KategoriSantriController::class, 'destroy'])->name('admin.kategori.destroy');

Route::get('tagihan-terjadwal', [TagihanTerjadwalController::class, 'index'])->name('admin.tagihan_terjadwal.index');
Route::get('tagihan-terjadwal/create', [TagihanTerjadwalController::class, 'create'])->name('admin.tagihan_terjadwal.create');
Route::post('tagihan-terjadwal', [TagihanTerjadwalController::class, 'store'])->name('admin.tagihan_terjadwal.store');
Route::get('tagihan-terjadwal/bulk-generate', [TagihanTerjadwalController::class, 'createBulkTagihanTerjadwal'])->name('admin.tagihan_terjadwal.createBulkTerjadwal');
Route::post('tagihan-terjadwal/bulk-generate', [TagihanTerjadwalController::class, 'generateBulkTagihanTerjadwal'])->name('admin.tagihan_terjadwal.bulkTerjadwal');
Route::get('tagihan-terjadwal/{id}/edit', [TagihanTerjadwalController::class, 'edit'])->name('admin.tagihan_terjadwal.edit');
Route::put('tagihan-terjadwal/{id}', [TagihanTerjadwalController::class, 'update'])->name('admin.tagihan_terjadwal.update');
Route::delete('tagihan-terjadwal/{id}', [TagihanTerjadwalController::class, 'destroy'])->name('admin.tagihan_terjadwal.destroy');


Route::get('tagihan-bulanan', [TagihanBulananController::class, 'index'])->name('admin.tagihan_bulanan.index');
Route::get('tagihan-bulanan/create', [TagihanBulananController::class, 'create'])->name('admin.tagihan_bulanan.create');
Route::post('tagihan-bulanan', [TagihanBulananController::class, 'store'])->name('admin.tagihan_bulanan.store');
Route::get('tagihan-bulanan/bulk-generate', [TagihanBulananController::class, 'createBulkBulanan'])->name('admin.tagihan_bulanan.createBulkBulanan');
Route::post('tagihan-bulanan/bulk-generate', [TagihanBulananController::class, 'generateBulkBulanan'])->name('admin.tagihan_bulanan.bulkBulanan');
Route::get('tagihan-bulanan/{id}/edit', [TagihanBulananController::class, 'edit'])->name('admin.tagihan_bulanan.edit');
Route::put('tagihan-bulanan/{id}', [TagihanBulananController::class, 'update'])->name('admin.tagihan_bulanan.update');
Route::delete('tagihan-bulanan/{id}', [TagihanBulananController::class, 'destroy'])->name('admin.tagihan_bulanan.destroy');

Route::get('pembayaran', [PembayaranController::class, 'index'])->name('admin.pembayaran.index');
Route::get('pembayaran/riwayat', [PembayaranController::class, 'riwayat'])->name('admin.pembayaran.riwayat');
Route::get('pembayaran/create', [PembayaranController::class, 'create'])->name('admin.pembayaran.create');
Route::get('pembayaran/{id}', [PembayaranController::class, 'show'])->name('admin.pembayaran.show');
Route::get('pembayaran/{id}/edit', [PembayaranController::class, 'edit'])->name('admin.pembayaran.edit');
Route::post('pembayaran', [PembayaranController::class, 'store'])->name('admin.pembayaran.store');
Route::put('pembayaran/{id}', [PembayaranController::class, 'update'])->name('admin.pembayaran.update');
Route::delete('pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('admin.pembayaran.destroy');

Route::get('biaya-terjadwal', [BiayaTerjadwalController::class, 'index'])->name('admin.biaya_terjadwal.index');
Route::get('biaya-terjadwal/create', [BiayaTerjadwalController::class, 'create'])->name('admin.biaya_terjadwal.create');
Route::post('biaya-terjadwal', [BiayaTerjadwalController::class, 'store'])->name('admin.biaya_terjadwal.store');
Route::get('biaya-terjadwal/{id}/edit', [BiayaTerjadwalController::class, 'edit'])->name('admin.biaya_terjadwal.edit');
Route::put('biaya-terjadwal/{id}', [BiayaTerjadwalController::class, 'update'])->name('admin.biaya_terjadwal.update');
Route::delete('biaya-terjadwal/{id}', [BiayaTerjadwalController::class, 'destroy'])->name('admin.biaya_terjadwal.destroy');

// Route::resource('santri', SantriController::class);
//zidan
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:admin|santri'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('tulisan', function () {
    return view('tulisan');
})->middleware(['auth', 'verified', 'role:santri|admin']);

require __DIR__ . '/auth.php';

