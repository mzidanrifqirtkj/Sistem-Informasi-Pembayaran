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

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard')->middleware('permission:view_dashboard');
    Route::get('profile', [ProfileController::class, 'index'])->name('admin.profile')->middleware('permission:view_profile');

    // Santri
    Route::get('santri', [SantriController::class, 'index'])->name('admin.santri.index')->middleware('permission:view_santri');
    Route::get('santri/data', [SantriController::class, 'getSantri'])->name('admin.santri.data')->middleware('permission:view_santri');
    Route::get('santri/create', [SantriController::class, 'create'])->name('admin.santri.create')->middleware('permission:create_santri');
    Route::post('santri', [SantriController::class, 'store'])->name('admin.santri.store')->middleware('permission:create_santri');
    Route::get('/santri/import', [SantriController::class, 'importForm'])->name('admin.santri.importForm')->middleware('permission:import_santri');
    Route::post('/santri/import', [SantriController::class, 'import'])->name('admin.santri.import')->middleware('permission:import_santri');
    Route::get('santri/{santri}/edit', [SantriController::class, 'edit'])->name('admin.santri.edit')->middleware('permission:edit_santri');
    Route::put('santri/{santri}', [SantriController::class, 'update'])->name('admin.santri.update')->middleware('permission:edit_santri');
    Route::delete('santri/{santri}', [SantriController::class, 'destroy'])->name('admin.santri.destroy')->middleware('permission:delete_santri');
    Route::get('santri/{santri}', [SantriController::class, 'show'])->name('admin.santri.show')->middleware('permission:view_santri');

    // User
    Route::get('user', [UserController::class, 'index'])->name('admin.user.index')->middleware('permission:view_user');
    Route::get('user/data', [UserController::class, 'getUser'])->name('admin.user.data')->middleware('permission:view_user');
    Route::get('user/create', [UserController::class, 'create'])->name('admin.user.create')->middleware('permission:create_user');
    Route::post('user', [UserController::class, 'store'])->name('admin.user.store')->middleware('permission:create_user');
    Route::get('/user/import', [UserController::class, 'importForm'])->name('admin.user.importForm')->middleware('permission:import_user');
    Route::post('/user/import', [UserController::class, 'import'])->name('admin.user.import')->middleware('permission:import_user');
    Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('admin.user.edit')->middleware('permission:edit_user');
    Route::put('user/{id}', [UserController::class, 'update'])->name('admin.user.update')->middleware('permission:edit_user');
    Route::delete('user/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy')->middleware('permission:delete_user');

    // Kategori Santri
    Route::get('kategori-santri', [KategoriSantriController::class, 'index'])->name('admin.kategori.index')->middleware('permission:view_kategori_santri');
    Route::get('kategori-santri/create', [KategoriSantriController::class, 'create'])->name('admin.kategori.create')->middleware('permission:create_kategori_santri');
    Route::post('kategori-santri', [KategoriSantriController::class, 'store'])->name('admin.kategori.store')->middleware('permission:create_kategori_santri');
    Route::get('kategori-santri/{id}/edit', [KategoriSantriController::class, 'edit'])->name('admin.kategori.edit')->middleware('permission:edit_kategori_santri');
    Route::put('kategori-santri/{id}', [KategoriSantriController::class, 'update'])->name('admin.kategori.update')->middleware('permission:edit_kategori_santri');
    Route::delete('kategori-santri/{id}', [KategoriSantriController::class, 'destroy'])->name('admin.kategori.destroy')->middleware('permission:delete_kategori_santri');

    // Tagihan Terjadwal
    Route::get('tagihan-terjadwal', [TagihanTerjadwalController::class, 'index'])->name('admin.tagihan_terjadwal.index')->middleware('permission:view_tagihan_terjadwal');
    Route::get('tagihan-terjadwal/create', [TagihanTerjadwalController::class, 'create'])->name('admin.tagihan_terjadwal.create')->middleware('permission:create_tagihan_terjadwal');
    Route::post('tagihan-terjadwal', [TagihanTerjadwalController::class, 'store'])->name('admin.tagihan_terjadwal.store')->middleware('permission:create_tagihan_terjadwal');
    Route::get('tagihan-terjadwal/bulk-generate', [TagihanTerjadwalController::class, 'createBulkTagihanTerjadwal'])->name('admin.tagihan_terjadwal.createBulkTerjadwal')->middleware('permission:bulk_generate_tagihan_terjadwal');
    Route::post('tagihan-terjadwal/bulk-generate', [TagihanTerjadwalController::class, 'generateBulkTagihanTerjadwal'])->name('admin.tagihan_terjadwal.bulkTerjadwal')->middleware('permission:bulk_generate_tagihan_terjadwal');
    Route::get('tagihan-terjadwal/{id}/edit', [TagihanTerjadwalController::class, 'edit'])->name('admin.tagihan_terjadwal.edit')->middleware('permission:edit_tagihan_terjadwal');
    Route::put('tagihan-terjadwal/{id}', [TagihanTerjadwalController::class, 'update'])->name('admin.tagihan_terjadwal.update')->middleware('permission:edit_tagihan_terjadwal');
    Route::delete('tagihan-terjadwal/{id}', [TagihanTerjadwalController::class, 'destroy'])->name('admin.tagihan_terjadwal.destroy')->middleware('permission:delete_tagihan_terjadwal');

    // Tambahan Bulanan
    Route::get('tambahan-bulanan', [TambahanBulananController::class, 'index'])->name('admin.tambahan_bulanan.index')->middleware('permission:view_tambahan_bulanan');
    Route::get('tambahan-bulanan/create', [TambahanBulananController::class, 'create'])->name('admin.tambahan_bulanan.create')->middleware('permission:create_tambahan_bulanan');
    Route::post('tambahan-bulanan', [TambahanBulananController::class, 'store'])->name('admin.tambahan_bulanan.store')->middleware('permission:create_tambahan_bulanan');
    Route::get('tambahan-bulanan/{id}/edit', [TambahanBulananController::class, 'edit'])->name('admin.tambahan_bulanan.edit')->middleware('permission:edit_tambahan_bulanan');
    Route::put('tambahan-bulanan/{item}', [TambahanBulananController::class, 'update'])->name('admin.tambahan_bulanan.update')->middleware('permission:edit_tambahan_bulanan');
    Route::delete('tambahan-bulanan/{id}', [TambahanBulananController::class, 'destroy'])->name('admin.tambahan_bulanan.destroy')->middleware('permission:delete_tambahan_bulanan');

    Route::get('tambahan-bulanan/item-santri', [TambahanBulananController::class, 'itemSantri'])->name('admin.tambahan_bulanan.item_santri')->middleware('permission:view_item_santri');
    Route::get('tambahan-bulanan/item-santri/{santri}', [TambahanBulananController::class, 'editItemSantri'])->name('admin.tambahan_bulanan.item_santri.edit')->middleware('permission:edit_item_santri');
    Route::put('tambahan-bulanan/item-santri/{item}', [TambahanBulananController::class, 'updateItemSantri'])->name('admin.tambahan_bulanan.item_santri.update')->middleware('permission:edit_item_santri');

    // Tagihan Bulanan
    Route::get('tagihan-bulanan', [TagihanBulananController::class, 'index'])->name('admin.tagihan_bulanan.index')->middleware('permission:view_tagihan_bulanan');
    Route::get('tagihan-bulanan/create', [TagihanBulananController::class, 'create'])->name('admin.tagihan_bulanan.create')->middleware('permission:create_tagihan_bulanan');
    Route::post('tagihan-bulanan', [TagihanBulananController::class, 'store'])->name('admin.tagihan_bulanan.store')->middleware('permission:create_tagihan_bulanan');
    Route::get('tagihan-bulanan/bulk-generate', [TagihanBulananController::class, 'createBulkBulanan'])->name('admin.tagihan_bulanan.createBulkBulanan')->middleware('permission:bulk_generate_tagihan_bulanan');
    Route::post('tagihan-bulanan/bulk-generate', [TagihanBulananController::class, 'generateBulkBulanan'])->name('admin.tagihan_bulanan.bulkBulanan')->middleware('permission:bulk_generate_tagihan_bulanan');
    Route::get('tagihan-bulanan/{id}/edit', [TagihanBulananController::class, 'edit'])->name('admin.tagihan_bulanan.edit')->middleware('permission:edit_tagihan_bulanan');
    Route::put('tagihan-bulanan/{id}', [TagihanBulananController::class, 'update'])->name('admin.tagihan_bulanan.update')->middleware('permission:edit_tagihan_bulanan');
    Route::delete('tagihan-bulanan/{id}', [TagihanBulananController::class, 'destroy'])->name('admin.tagihan_bulanan.destroy')->middleware('permission:delete_tagihan_bulanan');

    // Pembayaran
    Route::get('pembayaran', [PembayaranController::class, 'index'])->name('admin.pembayaran.index')->middleware('permission:view_pembayaran');
    Route::get('pembayaran/riwayat', [PembayaranController::class, 'riwayat'])->name('admin.pembayaran.riwayat')->middleware('permission:view_riwayat_pembayaran');
    Route::get('pembayaran/create', [PembayaranController::class, 'create'])->name('admin.pembayaran.create')->middleware('permission:create_pembayaran');
    Route::get('pembayaran/{id}', [PembayaranController::class, 'show'])->name('admin.pembayaran.show')->middleware('permission:view_pembayaran');
    Route::get('pembayaran/{id}/edit', [PembayaranController::class, 'edit'])->name('admin.pembayaran.edit')->middleware('permission:edit_pembayaran');
    Route::post('pembayaran', [PembayaranController::class, 'store'])->name('admin.pembayaran.store')->middleware('permission:create_pembayaran');
    Route::put('pembayaran/{id}', [PembayaranController::class, 'update'])->name('admin.pembayaran.update')->middleware('permission:edit_pembayaran');
    Route::delete('pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('admin.pembayaran.destroy')->middleware('permission:delete_pembayaran');

    // Biaya Terjadwal
    Route::get('biaya-terjadwal', [BiayaTerjadwalController::class, 'index'])->name('admin.biaya_terjadwal.index')->middleware('permission:view_biaya_terjadwal');
    Route::get('biaya-terjadwal/create', [BiayaTerjadwalController::class, 'create'])->name('admin.biaya_terjadwal.create')->middleware('permission:create_biaya_terjadwal');
    Route::post('biaya-terjadwal', [BiayaTerjadwalController::class, 'store'])->name('admin.biaya_terjadwal.store')->middleware('permission:create_biaya_terjadwal');
    Route::get('biaya-terjadwal/{id}/edit', [BiayaTerjadwalController::class, 'edit'])->name('admin.biaya_terjadwal.edit')->middleware('permission:edit_biaya_terjadwal');
    Route::put('biaya-terjadwal/{id}', [BiayaTerjadwalController::class, 'update'])->name('admin.biaya_terjadwal.update')->middleware('permission:edit_biaya_terjadwal');
    Route::delete('biaya-terjadwal/{id}', [BiayaTerjadwalController::class, 'destroy'])->name('admin.biaya_terjadwal.destroy')->middleware('permission:delete_biaya_terjadwal');

    // Kelas
    Route::get('kelas', [KelasController::class, 'index'])->name('admin.kelas.index')->middleware('permission:view_kelas');
    Route::get('kelas/create', [KelasController::class, 'create'])->name('admin.kelas.create')->middleware('permission:create_kelas');
    Route::post('kelas', [KelasController::class, 'store'])->name('admin.kelas.store')->middleware('permission:create_kelas');
    Route::get('kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('admin.kelas.edit')->middleware('permission:edit_kelas');
    Route::put('kelas/{kelas}', [KelasController::class, 'update'])->name('admin.kelas.update')->middleware('permission:edit_kelas');
    Route::delete('kelas/{id}', [KelasController::class, 'destroy'])->name('admin.kelas.destroy')->middleware('permission:delete_kelas');

    // Mapel Kelas
    Route::get('mapel-kelas', [MapelKelasController::class, 'index'])->name('admin.mapel_kelas.index')->middleware('permission:view_mapel_kelas');
    Route::get('mapel-kelas/create', [MapelKelasController::class, 'create'])->name('admin.mapel_kelas.create')->middleware('permission:create_mapel_kelas');
    Route::post('mapel-kelas', [MapelKelasController::class, 'store'])->name('admin.mapel_kelas.store')->middleware('permission:create_mapel_kelas');
    Route::get('mapel-kelas/{mapelKelas}/edit', [MapelKelasController::class, 'edit'])->name('admin.mapel_kelas.edit')->middleware('permission:edit_mapel_kelas');
    Route::put('mapel-kelas/{mapelKelas}', [MapelKelasController::class, 'update'])->name('admin.mapel_kelas.update')->middleware('permission:edit_mapel_kelas');
    Route::delete('mapel-kelas/{mapelKelas}', [MapelKelasController::class, 'destroy'])->name('admin.mapel_kelas.destroy')->middleware('permission:delete_mapel_kelas');

    // Tahun Ajar
    Route::get('tahun-ajar', [TahunAjarController::class, 'index'])->name('admin.tahun_ajar.index')->middleware('permission:view_tahun_ajar');
    Route::get('tahun-ajar/create', [TahunAjarController::class, 'create'])->name('admin.tahun_ajar.create')->middleware('permission:create_tahun_ajar');
    Route::post('tahun-ajar', [TahunAjarController::class, 'store'])->name('admin.tahun_ajar.store')->middleware('permission:create_tahun_ajar');
    Route::get('tahun-ajar/{tahunAjar}/edit', [TahunAjarController::class, 'edit'])->name('admin.tahun_ajar.edit')->middleware('permission:edit_tahun_ajar');
    Route::put('tahun-ajar/{tahunAjar}', [TahunAjarController::class, 'update'])->name('admin.tahun_ajar.update')->middleware('permission:edit_tahun_ajar');
    Route::delete('tahun-ajar/{tahunAjar}', [TahunAjarController::class, 'destroy'])->name('admin.tahun_ajar.destroy')->middleware('permission:delete_tahun_ajar');

    // Mata Pelajaran
    Route::get('mapel', [MataPelajaranController::class, 'index'])->name('admin.mapel.index')->middleware('permission:view_mapel');
    Route::get('mapel/create', [MataPelajaranController::class, 'create'])->name('admin.mapel.create')->middleware('permission:create_mapel');
    Route::post('mapel', [MataPelajaranController::class, 'store'])->name('admin.mapel.store')->middleware('permission:create_mapel');
    Route::get('mapel/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('admin.mapel.edit')->middleware('permission:edit_mapel');
    Route::put('mapel/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('admin.mapel.update')->middleware('permission:edit_mapel');
    Route::delete('mapel/{mataPelajaran}', [MataPelajaranController::class, 'destroy'])->name('admin.mapel.destroy')->middleware('permission:delete_mapel');

    // Ustadz
    Route::get('ustadz', [PenugasanUstadzController::class, 'getUstadzs'])->name('admin.ustadz.get')->middleware('permission:view_ustadz');
    Route::get('ustadz/add', [PenugasanUstadzController::class, 'addUstadz'])->name('admin.ustadz.add')->middleware('permission:create_ustadz');
    Route::post('ustadz/add', [PenugasanUstadzController::class, 'storeUstadz'])->name('admin.ustadz.store')->middleware('permission:create_ustadz');

    // Penugasan Ustadz
    Route::get('ustadz/penugasan', [PenugasanUstadzController::class, 'index'])->name('admin.ustadz.penugasan.index')->middleware('permission:view_penugasan_ustadz');
    Route::get('ustadz/penugasan/get-wali', [PenugasanUstadzController::class, 'getWaliKelas'])->name('admin.ustadz.penugasan.getWaliKelas')->middleware('permission:get_wali_kelas');
    Route::get('ustadz/penugasan/get-qori', [PenugasanUstadzController::class, 'getQori'])->name('admin.ustadz.penugasan.getQori')->middleware('permission:get_qori');

    // Penugasan Qori
    Route::get('ustadz/penugasan/qori/create', [PenugasanUstadzController::class, 'createQori'])->name('admin.ustadz.penugasan.qori.create')->middleware('permission:create_qori');
    Route::get('ustadz/penugasan/qori/get-pelajaran', [PenugasanUstadzController::class, 'getPelajaran'])->name('admin.ustadz.penugasan.qori.getPelajaran')->middleware('permission:get_pelajaran');
    Route::post('ustadz/penugasan/qori', [PenugasanUstadzController::class, 'storeQori'])->name('admin.ustadz.penugasan.qori.store')->middleware('permission:store_qori');

    // Penugasan Wali Kelas
    Route::get('ustadz/penugasan/mustahiq/create', [PenugasanUstadzController::class, 'createMustahiq'])->name('admin.ustadz.penugasan.mustahiq.create')->middleware('permission:create_mustahiq');
    Route::get('ustadz/penugasan/mustahiq/get-kelas', [PenugasanUstadzController::class, 'getKelas'])->name('admin.ustadz.penugasan.mustahiq.getKelas')->middleware('permission:get_kelas');
    Route::post('ustadz/penugasan/mustahiq', [PenugasanUstadzController::class, 'storeMustahiq'])->name('admin.ustadz.penugasan.mustahiq.store')->middleware('permission:store_mustahiq');

    // Absensi
    Route::get('absensi', [AbsensiController::class, 'index'])->name('admin.absensi.index')->middleware('permission:view_absensi');
    Route::get('absensi/import', [AbsensiController::class, 'importForm'])->name('admin.absensi.importForm')->middleware('permission:import_absensi');
    Route::post('absensi/import', [AbsensiController::class, 'import'])->name('admin.absensi.import')->middleware('permission:import_absensi');
    Route::get('absensi/data', [AbsensiController::class, 'getAbsensi'])->name('admin.absensi.data')->middleware('permission:view_absensi');
    Route::get('absensi/{id}/edit', [AbsensiController::class, 'edit'])->name('admin.absensi.edit')->middleware('permission:edit_absensi');
    Route::put('absensi/{id}', [AbsensiController::class, 'update'])->name('admin.absensi.update')->middleware('permission:edit_absensi');
    Route::delete('absensi/{id}', [AbsensiController::class, 'destroy'])->name('admin.absensi.destroy')->middleware('permission:delete_absensi');
    Route::get('/admin/santri/list', [AbsensiController::class, 'getSantriList'])->name('admin.santri.list')->middleware('permission:get_santri_list');

    Route::get('absensi/create', [AbsensiController::class, 'create'])->name('admin.absensi.create')->middleware('permission:create_absensi');
    Route::post('absensi', [AbsensiController::class, 'store'])->name('admin.absensi.store')->middleware('permission:create_absensi');
    Route::get('absensi/{absensi}', [AbsensiController::class, 'show'])->name('admin.absensi.show')->middleware('permission:view_absensi');
});

Route::middleware(['auth', 'role:admin|santri'])->prefix('admin')->group(function () {
    // Dashboard Santri
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard')->middleware('permission:view_dashboard');

    // Data Santri (Hanya untuk melihat data diri sendiri)
    Route::get('santri/{santri}', [SantriController::class, 'show'])->name('admin.santri.show')->middleware('permission:view_santri');

    // Tambahan Bulanan Santri
    Route::get('tambahan-bulanan/item-santri', [TambahanBulananController::class, 'itemSantri'])->name('admin.tambahan_bulanan.item_santri')->middleware('permission:view_item_santri');

    // Tagihan Terjadwal Santri
    Route::get('tagihan-terjadwal', [TagihanTerjadwalController::class, 'index'])->name('admin.tagihan_terjadwal.index')->middleware('permission:view_tagihan_terjadwal');

    // Tagihan Bulanan Santri
    Route::get('tagihan-bulanan', [TagihanBulananController::class, 'index'])->name('admin.tagihan_bulanan.index')->middleware('permission:view_tagihan_bulanan');

    // Riwayat Pembayaran Santri
    Route::get('pembayaran/riwayat', [PembayaranController::class, 'riwayat'])->name('admin.pembayaran.riwayat')->middleware('permission:view_riwayat_pembayaran');

    // Biaya Terjadwal Santri
    // Route::get('biaya-terjadwal', [BiayaTerjadwalController::class, 'index'])->name('admin.biaya_terjadwal.index')->middleware('permission:view_biaya_terjadwal');

    // Profile Santri
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit')->middleware('permission:view_profile');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('admin.profile.update_password')->middleware('permission:edit_profile');
});

Route::get('tulisan', function () {
    return view('tulisan');
})->middleware(['auth', 'verified', 'role:santri|admin']);

require __DIR__ . '/auth.php';
// require __DIR__ . '/santri.php';
