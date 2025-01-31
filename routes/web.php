<?php

use App\Http\Controllers\Santri\DashboardController as SantriDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SantriController;
use App\Http\Controllers\Admin\KategoriSantriController;
use App\Http\Controllers\Admin\BiayaTerjadwalController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\TagihanBulananController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\TagihanTerjadwalController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PenugasanUstadzController;
use App\Http\Controllers\Admin\TahunAjarController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TambahanBulananController;
use App\Models\BiayaTahunan;
use App\Models\PenugasanUstadz;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('home');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
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

    Route::get('kategori-santri', [KategoriSantriController::class, 'index'])->name('admin.kategori.index');
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


    Route::get('tambahan-bulanan', [TambahanBulananController::class, 'index'])->name('admin.tambahan_bulanan.index');
    Route::get('tambahan-bulanan/create', [TambahanBulananController::class, 'create'])->name('admin.tambahan_bulanan.create');
    Route::post('tambahan-bulanan', [TambahanBulananController::class, 'store'])->name('admin.tambahan_bulanan.store');
    Route::get('tambahan-bulanan/{id}/edit', [TambahanBulananController::class, 'edit'])->name('admin.tambahan_bulanan.edit');
    Route::put('tambahan-bulanan/{item}', [TambahanBulananController::class, 'update'])->name('admin.tambahan_bulanan.update');
    Route::delete('tambahan-bulanan/{id}', [TambahanBulananController::class, 'destroy'])->name('admin.tambahan_bulanan.destroy');

    Route::get('tambahan-bulanan/item-santri', [TambahanBulananController::class, 'itemSantri'])->name('admin.tambahan_bulanan.item_santri');
    Route::get('tambahan-bulanan/item-santri/{santri}', [TambahanBulananController::class, 'editItemSantri'])->name('admin.tambahan_bulanan.item_santri.edit');
    Route::put('tambahan-bulanan/item-santri/{item}', [TambahanBulananController::class, 'updateItemSantri'])->name('admin.tambahan_bulanan.item_santri.update');

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

    // Route::resource('kelas', KelasController::class);
    Route::get('kelas', [KelasController::class, 'index'])->name('admin.kelas.index');
    Route::get('kelas/create', [KelasController::class, 'create'])->name('admin.kelas.create');
    Route::post('kelas', [KelasController::class, 'store'])->name('admin.kelas.store');
    Route::get('kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('admin.kelas.edit');
    Route::put('kelas/{kelas}', [KelasController::class, 'update'])->name('admin.kelas.update');
    Route::delete('kelas/{id}', [KelasController::class, 'destroy'])->name('admin.kelas.destroy');

    Route::get('tahun-ajar', [TahunAjarController::class, 'index'])->name('admin.tahun_ajar.index');
    Route::get('tahun-ajar/create', [TahunAjarController::class, 'create'])->name('admin.tahun_ajar.create');
    Route::post('tahun-ajar', [TahunAjarController::class, 'store'])->name('admin.tahun_ajar.store');
    Route::get('tahun-ajar/{tahunAjar}/edit', [TahunAjarController::class, 'edit'])->name('admin.tahun_ajar.edit');
    Route::put('tahun-ajar/{tahunAjar}', [TahunAjarController::class, 'update'])->name('admin.tahun_ajar.update');
    Route::delete('tahun-ajar/{tahunAjar}', [TahunAjarController::class, 'destroy'])->name('admin.tahun_ajar.destroy');

    Route::get('mapel', [MataPelajaranController::class, 'index'])->name('admin.mapel.index');
    Route::get('mapel/create', [MataPelajaranController::class, 'create'])->name('admin.mapel.create');
    Route::post('mapel', [MataPelajaranController::class, 'store'])->name('admin.mapel.store');
    Route::get('mapel/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('admin.mapel.edit');
    Route::put('mapel/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('admin.mapel.update');
    Route::delete('mapel/{mataPelajaran}', [MataPelajaranController::class, 'destroy'])->name('admin.mapel.destroy');

    Route::get('ustadz', [PenugasanUstadzController::class, 'getUstadzs'])->name('admin.ustadz.get');
    Route::get('ustadz/add', [PenugasanUstadzController::class, 'addUstadz'])->name('admin.ustadz.add');
    Route::post('ustadz/add', [PenugasanUstadzController::class, 'storeUstadz'])->name('admin.ustadz.store');

    Route::get('ustadz/penugasan', [PenugasanUstadzController::class, 'getPenugasan'])->name('admin.ustadz.penugasan.index');
    Route::get('ustadz/penugasan/create', [PenugasanUstadzController::class, 'createPenugasan'])->name('admin.ustadz.penugasan.create');
    Route::post('ustadz/penugasan', [PenugasanUstadzController::class, 'storePenugasan'])->name('admin.ustadz.penugasan.store');
    // Route::get('ustadz/penugasan/{id}', [PenugasanUstadzController::class, 'showPenugasan'])->name('admin.ustadz.penugasan.show');

    Route::get('ustadz/penugasan/create-mapel-kelas', [PenugasanUstadzController::class, 'createMapelKelas'])->name('admin.ustadz.penugasan.createMapelKelas');
    Route::post('ustadz/penugasan/create-mapel-kelas', [PenugasanUstadzController::class, 'storeMapelKelas'])->name('admin.ustadz.penugasan.storeMapelKelas');


    Route::get('/penugasan/get-pelajaran', [PenugasanUstadzController::class, 'getPelajaran'])->name('penugasan.getPelajaran');
    Route::get('ustadz/penugasan/get-kelas', [PenugasanUstadzController::class, 'getKelas'])->name('penugasan.getKelas');

    Route::get('ustadz/penugasan/{id}/edit', [PenugasanUstadzController::class, 'editPenugasan'])->name('admin.ustadz.penugasan.edit');
    Route::put('ustadz/penugasan/{id}', [PenugasanUstadzController::class, 'updatePenugasan'])->name('admin.ustadz.penugasan.update');
    Route::delete('ustadz/penugasan/{id}', [PenugasanUstadzController::class, 'destroyPenugasan'])->name('admin.ustadz.penugasan.destroy');

});

Route::get('tulisan', function () {
    return view('tulisan');
})->middleware(['auth', 'verified', 'role:santri|admin']);

require __DIR__ . '/auth.php';
require __DIR__ . '/santri.php';
