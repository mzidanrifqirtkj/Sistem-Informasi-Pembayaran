<?php

use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Santri\DashboardController as SantriDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SantriController;
use App\Http\Controllers\Admin\KategoriSantriController;
use App\Http\Controllers\Admin\BiayaTerjadwalController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MapelKelasController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\TagihanBulananController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\TagihanTerjadwalController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PenugasanUstadzController;
use App\Http\Controllers\Admin\TahunAjarController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TambahanBulananController;
use App\Http\Controllers\Admin\WaliKelasController;
use App\Models\BiayaTahunan;
use App\Models\PenugasanUstadz;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Route untuk admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('permission:view_dashboard');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile')->middleware('permission:view_profile');

    // Santri
    Route::get('santri', [SantriController::class, 'index'])->name('santri.index')->middleware('permission:view_santri');
    Route::get('santri/data', [SantriController::class, 'getSantri'])->name('santri.data')->middleware('permission:view_santri');
    Route::get('santri/create', [SantriController::class, 'create'])->name('santri.create')->middleware('permission:create_santri');
    Route::post('santri', [SantriController::class, 'store'])->name('santri.store')->middleware('permission:create_santri');
    Route::get('santri/import', [SantriController::class, 'importForm'])->name('santri.importForm')->middleware('permission:import_santri');
    Route::post('santri/import', [SantriController::class, 'import'])->name('santri.import')->middleware('permission:import_santri');
    Route::get('santri/{santri}/edit', [SantriController::class, 'edit'])->name('santri.edit')->middleware('permission:edit_santri');
    Route::put('santri/{santri}', [SantriController::class, 'update'])->name('santri.update')->middleware('permission:edit_santri');
    Route::delete('santri/{santri}', [SantriController::class, 'destroy'])->name('santri.destroy')->middleware('permission:delete_santri');
    Route::get('santri/{santri}', [SantriController::class, 'show'])->name('santri.show')->middleware('permission:view_santri');

    // User
    Route::get('user', [UserController::class, 'index'])->name('user.index')->middleware('permission:view_user');
    Route::get('user/data', [UserController::class, 'getUser'])->name('user.data')->middleware('permission:view_user');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create')->middleware('permission:create_user');
    Route::post('user', [UserController::class, 'store'])->name('user.store')->middleware('permission:create_user');
    Route::get('user/import', [UserController::class, 'importForm'])->name('user.importForm')->middleware('permission:import_user');
    Route::post('user/import', [UserController::class, 'import'])->name('user.import')->middleware('permission:import_user');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit')->middleware('permission:edit_user');
    Route::patch('user/{user}', [UserController::class, 'update'])->name('user.update')->middleware('permission:edit_user');
    Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy')->middleware('permission:delete_user');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');

    // Kategori Santri
    Route::get('kategori-santri', [KategoriSantriController::class, 'index'])->name('kategori.index')->middleware('permission:view_kategori_santri');
    Route::get('kategori-santri/create', [KategoriSantriController::class, 'create'])->name('kategori.create')->middleware('permission:create_kategori_santri');
    Route::post('kategori-santri', [KategoriSantriController::class, 'store'])->name('kategori.store')->middleware('permission:create_kategori_santri');
    Route::get('kategori-santri/{id}/edit', [KategoriSantriController::class, 'edit'])->name('kategori.edit')->middleware('permission:edit_kategori_santri');
    Route::put('kategori-santri/{id}', [KategoriSantriController::class, 'update'])->name('kategori.update')->middleware('permission:edit_kategori_santri');
    Route::delete('kategori-santri/{id}', [KategoriSantriController::class, 'destroy'])->name('kategori.destroy')->middleware('permission:delete_kategori_santri');

    // Tagihan Terjadwal
    Route::get('tagihan-terjadwal', [TagihanTerjadwalController::class, 'index'])->name('tagihan_terjadwal.index')->middleware('permission:view_tagihan_terjadwal');
    Route::get('tagihan-terjadwal/create', [TagihanTerjadwalController::class, 'create'])->name('tagihan_terjadwal.create')->middleware('permission:create_tagihan_terjadwal');
    Route::post('tagihan-terjadwal', [TagihanTerjadwalController::class, 'store'])->name('tagihan_terjadwal.store')->middleware('permission:create_tagihan_terjadwal');
    Route::get('tagihan-terjadwal/bulk-generate', [TagihanTerjadwalController::class, 'createBulkTagihanTerjadwal'])->name('tagihan_terjadwal.createBulkTerjadwal')->middleware('permission:bulk_generate_tagihan_terjadwal');
    Route::post('tagihan-terjadwal/bulk-generate', [TagihanTerjadwalController::class, 'generateBulkTagihanTerjadwal'])->name('tagihan_terjadwal.bulkTerjadwal')->middleware('permission:bulk_generate_tagihan_terjadwal');
    Route::get('tagihan-terjadwal/{id}/edit', [TagihanTerjadwalController::class, 'edit'])->name('tagihan_terjadwal.edit')->middleware('permission:edit_tagihan_terjadwal');
    Route::put('tagihan-terjadwal/{id}', [TagihanTerjadwalController::class, 'update'])->name('tagihan_terjadwal.update')->middleware('permission:edit_tagihan_terjadwal');
    Route::delete('tagihan-terjadwal/{id}', [TagihanTerjadwalController::class, 'destroy'])->name('tagihan_terjadwal.destroy')->middleware('permission:delete_tagihan_terjadwal');

    // Tambahan Bulanan
    Route::get('tambahan-bulanan', [TambahanBulananController::class, 'index'])->name('tambahan_bulanan.index')->middleware('permission:view_tambahan_bulanan');
    Route::get('tambahan-bulanan/create', [TambahanBulananController::class, 'create'])->name('tambahan_bulanan.create')->middleware('permission:create_tambahan_bulanan');
    Route::post('tambahan-bulanan', [TambahanBulananController::class, 'store'])->name('tambahan_bulanan.store')->middleware('permission:create_tambahan_bulanan');
    Route::get('tambahan-bulanan/{id}/edit', [TambahanBulananController::class, 'edit'])->name('tambahan_bulanan.edit')->middleware('permission:edit_tambahan_bulanan');
    Route::put('tambahan-bulanan/{item}', [TambahanBulananController::class, 'update'])->name('tambahan_bulanan.update')->middleware('permission:edit_tambahan_bulanan');
    Route::delete('tambahan-bulanan/{id}', [TambahanBulananController::class, 'destroy'])->name('tambahan_bulanan.destroy')->middleware('permission:delete_tambahan_bulanan');

    Route::get('tambahan-bulanan/item-santri', [TambahanBulananController::class, 'itemSantri'])->name('tambahan_bulanan.item_santri')->middleware('permission:view_item_santri');
    Route::get('tambahan-bulanan/item-santri/{santri}', [TambahanBulananController::class, 'editItemSantri'])->name('tambahan_bulanan.item_santri.edit')->middleware('permission:edit_item_santri');
    Route::put('tambahan-bulanan/item-santri/{item}', [TambahanBulananController::class, 'updateItemSantri'])->name('tambahan_bulanan.item_santri.update')->middleware('permission:edit_item_santri');

    // Tagihan Bulanan
    Route::get('tagihan-bulanan', [TagihanBulananController::class, 'index'])->name('tagihan_bulanan.index')->middleware('permission:view_tagihan_bulanan');
    Route::get('tagihan-bulanan/create', [TagihanBulananController::class, 'create'])->name('tagihan_bulanan.create')->middleware('permission:create_tagihan_bulanan');
    Route::post('tagihan-bulanan', [TagihanBulananController::class, 'store'])->name('tagihan_bulanan.store')->middleware('permission:create_tagihan_bulanan');
    Route::get('tagihan-bulanan/bulk-generate', [TagihanBulananController::class, 'createBulkBulanan'])->name('tagihan_bulanan.createBulkBulanan')->middleware('permission:bulk_generate_tagihan_bulanan');
    Route::post('tagihan-bulanan/bulk-generate', [TagihanBulananController::class, 'generateBulkBulanan'])->name('tagihan_bulanan.bulkBulanan')->middleware('permission:bulk_generate_tagihan_bulanan');
    Route::get('tagihan-bulanan/{id}/edit', [TagihanBulananController::class, 'edit'])->name('tagihan_bulanan.edit')->middleware('permission:edit_tagihan_bulanan');
    Route::put('tagihan-bulanan/{id}', [TagihanBulananController::class, 'update'])->name('tagihan_bulanan.update')->middleware('permission:edit_tagihan_bulanan');
    Route::delete('tagihan-bulanan/{id}', [TagihanBulananController::class, 'destroy'])->name('tagihan_bulanan.destroy')->middleware('permission:delete_tagihan_bulanan');

    // Pembayaran
    Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index')->middleware('permission:view_pembayaran');
    Route::get('pembayaran/riwayat', [PembayaranController::class, 'riwayat'])->name('pembayaran.riwayat')->middleware('permission:view_riwayat_pembayaran');
    Route::get('pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create')->middleware('permission:create_pembayaran');
    Route::get('pembayaran/{id}', [PembayaranController::class, 'show'])->name('pembayaran.show')->middleware('permission:view_pembayaran');
    Route::get('pembayaran/{id}/edit', [PembayaranController::class, 'edit'])->name('pembayaran.edit')->middleware('permission:edit_pembayaran');
    Route::post('pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store')->middleware('permission:create_pembayaran');
    Route::put('pembayaran/{id}', [PembayaranController::class, 'update'])->name('pembayaran.update')->middleware('permission:edit_pembayaran');
    Route::delete('pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy')->middleware('permission:delete_pembayaran');

    // Biaya Terjadwal
    Route::get('biaya-terjadwal', [BiayaTerjadwalController::class, 'index'])->name('biaya_terjadwal.index')->middleware('permission:view_biaya_terjadwal');
    Route::get('biaya-terjadwal/create', [BiayaTerjadwalController::class, 'create'])->name('biaya_terjadwal.create')->middleware('permission:create_biaya_terjadwal');
    Route::post('biaya-terjadwal', [BiayaTerjadwalController::class, 'store'])->name('biaya_terjadwal.store')->middleware('permission:create_biaya_terjadwal');
    Route::get('biaya-terjadwal/{id}/edit', [BiayaTerjadwalController::class, 'edit'])->name('biaya_terjadwal.edit')->middleware('permission:edit_biaya_terjadwal');
    Route::put('biaya-terjadwal/{id}', [BiayaTerjadwalController::class, 'update'])->name('biaya_terjadwal.update')->middleware('permission:edit_biaya_terjadwal');
    Route::delete('biaya-terjadwal/{id}', [BiayaTerjadwalController::class, 'destroy'])->name('biaya_terjadwal.destroy')->middleware('permission:delete_biaya_terjadwal');

    // Kelas
    Route::get('kelas', [KelasController::class, 'index'])->name('kelas.index')->middleware('permission:view_kelas');
    Route::get('kelas/create', [KelasController::class, 'create'])->name('kelas.create')->middleware('permission:create_kelas');
    Route::post('kelas', [KelasController::class, 'store'])->name('kelas.store')->middleware('permission:create_kelas');
    Route::get('kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit')->middleware('permission:edit_kelas');
    Route::put('kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update')->middleware('permission:edit_kelas');
    Route::delete('kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy')->middleware('permission:delete_kelas');

    // Mapel Kelas
    Route::get('mapel-kelas', [MapelKelasController::class, 'index'])->name('mapel_kelas.index')->middleware('permission:view_mapel_kelas');
    Route::get('mapel-kelas/create', [MapelKelasController::class, 'create'])->name('mapel_kelas.create')->middleware('permission:create_mapel_kelas');
    Route::post('mapel-kelas', [MapelKelasController::class, 'store'])->name('mapel_kelas.store')->middleware('permission:create_mapel_kelas');
    Route::get('mapel-kelas/{mapelKelas}/edit', [MapelKelasController::class, 'edit'])->name('mapel_kelas.edit')->middleware('permission:edit_mapel_kelas');
    Route::put('mapel-kelas/{mapelKelas}', [MapelKelasController::class, 'update'])->name('mapel_kelas.update')->middleware('permission:edit_mapel_kelas');
    Route::delete('mapel-kelas/{mapelKelas}', [MapelKelasController::class, 'destroy'])->name('mapel_kelas.destroy')->middleware('permission:delete_mapel_kelas');

    // Tahun Ajar
    Route::get('tahun-ajar', [TahunAjarController::class, 'index'])->name('tahun_ajar.index')->middleware('permission:view_tahun_ajar');
    Route::get('tahun-ajar/create', [TahunAjarController::class, 'create'])->name('tahun_ajar.create')->middleware('permission:create_tahun_ajar');
    Route::post('tahun-ajar', [TahunAjarController::class, 'store'])->name('tahun_ajar.store')->middleware('permission:create_tahun_ajar');
    Route::get('tahun-ajar/{tahunAjar}/edit', [TahunAjarController::class, 'edit'])->name('tahun_ajar.edit')->middleware('permission:edit_tahun_ajar');
    Route::put('tahun-ajar/{tahunAjar}', [TahunAjarController::class, 'update'])->name('tahun_ajar.update')->middleware('permission:edit_tahun_ajar');
    Route::delete('tahun-ajar/{tahunAjar}', [TahunAjarController::class, 'destroy'])->name('tahun_ajar.destroy')->middleware('permission:delete_tahun_ajar');

    // Mata Pelajaran
    Route::get('mapel', [MataPelajaranController::class, 'index'])->name('mapel.index')->middleware('permission:view_mapel');
    Route::get('mapel/create', [MataPelajaranController::class, 'create'])->name('mapel.create')->middleware('permission:create_mapel');
    Route::post('mapel', [MataPelajaranController::class, 'store'])->name('mapel.store')->middleware('permission:create_mapel');
    Route::get('mapel/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('mapel.edit')->middleware('permission:edit_mapel');
    Route::put('mapel/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('mapel.update')->middleware('permission:edit_mapel');
    Route::delete('mapel/{mataPelajaran}', [MataPelajaranController::class, 'destroy'])->name('mapel.destroy')->middleware('permission:delete_mapel');

    // Ustadz
    Route::get('ustadz', [PenugasanUstadzController::class, 'getUstadzs'])->name('ustadz.get')->middleware('permission:view_ustadz');
    Route::get('ustadz/add', [PenugasanUstadzController::class, 'addUstadz'])->name('ustadz.add')->middleware('permission:create_ustadz');
    Route::post('ustadz/add', [PenugasanUstadzController::class, 'storeUstadz'])->name('ustadz.store')->middleware('permission:create_ustadz');

    // Penugasan Ustadz
    Route::get('ustadz/penugasan', [PenugasanUstadzController::class, 'index'])->name('ustadz.penugasan.index')->middleware('permission:view_penugasan_ustadz');
    Route::get('ustadz/penugasan/get-wali', [PenugasanUstadzController::class, 'getWaliKelas'])->name('ustadz.penugasan.getWaliKelas')->middleware('permission:get_wali_kelas');
    Route::get('ustadz/penugasan/get-qori', [PenugasanUstadzController::class, 'getQori'])->name('ustadz.penugasan.getQori')->middleware('permission:get_qori');

    // Penugasan Qori
    Route::get('ustadz/penugasan/qori/create', [PenugasanUstadzController::class, 'createQori'])->name('ustadz.penugasan.qori.create')->middleware('permission:create_qori');
    Route::get('ustadz/penugasan/qori/get-pelajaran', [PenugasanUstadzController::class, 'getPelajaran'])->name('ustadz.penugasan.qori.getPelajaran')->middleware('permission:get_pelajaran');
    Route::post('ustadz/penugasan/qori', [PenugasanUstadzController::class, 'storeQori'])->name('ustadz.penugasan.qori.store')->middleware('permission:store_qori');

    // Penugasan Wali Kelas
    Route::get('ustadz/penugasan/mustahiq/create', [PenugasanUstadzController::class, 'createMustahiq'])->name('ustadz.penugasan.mustahiq.create')->middleware('permission:create_mustahiq');
    Route::get('ustadz/penugasan/mustahiq/get-kelas', [PenugasanUstadzController::class, 'getKelas'])->name('ustadz.penugasan.mustahiq.getKelas')->middleware('permission:get_kelas');
    Route::post('ustadz/penugasan/mustahiq', [PenugasanUstadzController::class, 'storeMustahiq'])->name('ustadz.penugasan.mustahiq.store')->middleware('permission:store_mustahiq');

    // Absensi
    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index')->middleware('permission:view_absensi');
    Route::get('absensi/import', [AbsensiController::class, 'importForm'])->name('absensi.importForm')->middleware('permission:import_absensi');
    Route::post('absensi/import', [AbsensiController::class, 'import'])->name('absensi.import')->middleware('permission:import_absensi');
    Route::get('absensi/data', [AbsensiController::class, 'getAbsensi'])->name('absensi.data')->middleware('permission:view_absensi');
    Route::get('absensi/{id}/edit', [AbsensiController::class, 'edit'])->name('absensi.edit')->middleware('permission:edit_absensi');
    Route::put('absensi/{id}', [AbsensiController::class, 'update'])->name('absensi.update')->middleware('permission:edit_absensi');
    Route::delete('absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy')->middleware('permission:delete_absensi');
    Route::get('santri/list', [AbsensiController::class, 'getSantriList'])->name('santri.list')->middleware('permission:get_santri_list');

    Route::get('absensi/create', [AbsensiController::class, 'create'])->name('absensi.create')->middleware('permission:create_absensi');
    Route::post('absensi', [AbsensiController::class, 'store'])->name('absensi.store')->middleware('permission:create_absensi');
    Route::get('absensi/{absensi}', [AbsensiController::class, 'show'])->name('absensi.show')->middleware('permission:view_absensi');
});

// Route untuk admin dan santri
Route::middleware(['auth', 'role:admin|santri'])->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('permission:view_dashboard');

    // Data Santri (Hanya untuk melihat data diri sendiri)
    Route::get('santri/{santri}', [SantriController::class, 'show'])->name('santri.show')->middleware('permission:view_santri');

    // Tambahan Bulanan Santri
    Route::get('tambahan-bulanan/item-santri', [TambahanBulananController::class, 'itemSantri'])->name('tambahan_bulanan.item_santri')->middleware('permission:view_item_santri');

    // Tagihan Terjadwal Santri
    Route::get('tagihan-terjadwal', [TagihanTerjadwalController::class, 'index'])->name('tagihan_terjadwal.index')->middleware('permission:view_tagihan_terjadwal');

    // Tagihan Bulanan Santri
    Route::get('tagihan-bulanan', [TagihanBulananController::class, 'index'])->name('tagihan_bulanan.index')->middleware('permission:view_tagihan_bulanan');

    // Riwayat Pembayaran Santri
    Route::get('pembayaran/riwayat', [PembayaranController::class, 'riwayat'])->name('pembayaran.riwayat')->middleware('permission:view_riwayat_pembayaran');

    // Absensi
    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index')->middleware('permission:view_absensi');
    Route::get('absensi/data', [AbsensiController::class, 'getAbsensi'])->name('absensi.data')->middleware('permission:view_absensi');

    // Profile Santri
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('permission:view_profile');
    Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update_password')->middleware('permission:edit_profile');
});

Route::get('tulisan', function () {
    return view('tulisan');
})->middleware(['auth', 'verified', 'role:santri|admin']);

require __DIR__ . '/auth.php';
