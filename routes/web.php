<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\BiayaSantriController;
use App\Http\Controllers\DaftarBiayaController;
use App\Http\Controllers\KategoriBiayaController;
use App\Http\Controllers\PembayaranBulkController;
use App\Http\Controllers\PembayaranVoidController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\QoriKelasController;
use App\Http\Controllers\RiwayatKelasController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Santri\DashboardController as SantriDashboardController;
use App\Http\Controllers\DashboardController as AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\KategoriSantriController;
use App\Http\Controllers\BiayaTerjadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelKelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\TagihanBulananController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TagihanTerjadwalController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PenugasanUstadzController;
use App\Http\Controllers\TahunAjarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TambahanBulananController;
use App\Http\Controllers\WaliKelasController;
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
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    //Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('profile')->middleware('permission:profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update_password');

    // Santri
    Route::get('santri', [SantriController::class, 'index'])->name('santri.index')->middleware('permission:santri.view');
    Route::get('santri/data', [SantriController::class, 'getSantri'])->name('santri.data')->middleware('permission:santri.view');
    Route::get('santri/create', [SantriController::class, 'create'])->name('santri.create')->middleware('permission:santri.create');
    Route::post('santri', [SantriController::class, 'store'])->name('santri.store')->middleware('permission:santri.create');
    Route::get('santri/import', [SantriController::class, 'importForm'])->name('santri.importForm')->middleware('permission:santri.import');
    Route::post('santri/import', [SantriController::class, 'import'])->name('santri.import')->middleware('permission:santri.import');
    Route::get('/template-download/santri', [SantriController::class, 'downloadTemplate'])->name('download.template');
    Route::get('santri/{santri}/edit', [SantriController::class, 'edit'])->name('santri.edit')->middleware('permission:santri.edit');
    Route::put('santri/{santri}', [SantriController::class, 'update'])->name('santri.update')->middleware('permission:santri.edit');
    Route::delete('santri/{santri}', [SantriController::class, 'destroy'])->name('santri.destroy')->middleware('permission:santri.delete');
    Route::get('santri/{santri}', [SantriController::class, 'show'])->name('santri.show')->middleware('permission:santri.view');

    // User
    Route::get('user', [UserController::class, 'index'])->name('user.index')->middleware('permission:user.view');
    Route::get('user/data', [UserController::class, 'getUser'])->name('user.data')->middleware('permission:user.view');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create')->middleware('permission:user.create');
    Route::post('user', [UserController::class, 'store'])->name('user.store')->middleware('permission:user.create');
    Route::get('user/import', [UserController::class, 'importForm'])->name('user.importForm')->middleware('permission:user.import');
    Route::post('user/import', [UserController::class, 'import'])->name('user.import')->middleware('permission:user.import');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit')->middleware('permission:user.edit');
    Route::patch('user/{user}', [UserController::class, 'update'])->name('user.update')->middleware('permission:user.edit');
    Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy')->middleware('permission:user.delete');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');

    // Tagihan Terjadwal
    Route::resource('tagihan_terjadwal', TagihanTerjadwalController::class);
    // Additional routes for TagihanTerjadwal
    Route::get('tagihan-terjadwal/biaya-santri/by-santri', [TagihanTerjadwalController::class, 'getBiayaSantriBySantriId'])
        ->name('tagihan_terjadwal.getBiayaSantriBySantriId');

    Route::get('tagihan-terjadwal/bulk/create', [TagihanTerjadwalController::class, 'createBulkTagihanTerjadwal'])
        ->name('tagihan_terjadwal.createBulkTerjadwal');

    Route::post('tagihan-terjadwal/bulk/generate', [TagihanTerjadwalController::class, 'generateBulkTagihanTerjadwal'])
        ->name('tagihan_terjadwal.generateBulkTerjadwal');

    Route::get('tagihan-terjadwal/bulk/progress', [TagihanTerjadwalController::class, 'getBulkProgress'])
        ->name('tagihan_terjadwal.getBulkProgress');

    Route::get('tagihan-terjadwal/export', [TagihanTerjadwalController::class, 'export'])
        ->name('tagihan_terjadwal.export');

    // Tambahan Bulanan
    Route::get('tambahan-bulanan', [TambahanBulananController::class, 'index'])->name('tambahan_bulanan.index')->middleware('permission:tambahan-bulanan.view');
    Route::get('tambahan-bulanan/create', [TambahanBulananController::class, 'create'])->name('tambahan_bulanan.create')->middleware('permission:tambahan-bulanan.create');
    Route::post('tambahan-bulanan', [TambahanBulananController::class, 'store'])->name('tambahan_bulanan.store')->middleware('permission:tambahan-bulanan.create');
    Route::get('tambahan-bulanan/{id}/edit', [TambahanBulananController::class, 'edit'])->name('tambahan_bulanan.edit')->middleware('permission:tambahan-bulanan.edit');
    Route::put('tambahan-bulanan/{item}', [TambahanBulananController::class, 'update'])->name('tambahan_bulanan.update')->middleware('permission:tambahan-bulanan.edit');
    Route::delete('tambahan-bulanan/{id}', [TambahanBulananController::class, 'destroy'])->name('tambahan_bulanan.destroy')->middleware('permission:tambahan-bulanan.delete');

    Route::get('tambahan-bulanan/item-santri', [TambahanBulananController::class, 'itemSantri'])->name('tambahan_bulanan.item_santri')->middleware('permission:item-santri.view');
    Route::get('tambahan-bulanan/item-santri/{santri}', [TambahanBulananController::class, 'editItemSantri'])->name('tambahan_bulanan.item_santri.edit')->middleware('permission:item-santri.edit');
    Route::put('tambahan-bulanan/item-santri/{item}', [TambahanBulananController::class, 'updateItemSantri'])->name('tambahan_bulanan.item_santri.update')->middleware('permission:item-santri.edit');

    // Tagihan Bulanan
    Route::prefix('tagihan-bulanan')->name('tagihan_bulanan.')->group(function () {
        Route::get('/', [TagihanBulananController::class, 'index'])->name('index');
        Route::get('/create', [TagihanBulananController::class, 'create'])->name('create');
        Route::post('/', [TagihanBulananController::class, 'store'])->name('store');
        Route::get('/{id}', [TagihanBulananController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TagihanBulananController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TagihanBulananController::class, 'update'])->name('update');
        Route::delete('/{id}', [TagihanBulananController::class, 'destroy'])->name('destroy');

        // Bulk operations
        Route::get('/bulk/create', [TagihanBulananController::class, 'createBulkBulanan'])->name('createBulkBulanan');
        Route::post('/bulk/generate', [TagihanBulananController::class, 'generateBulkBulanan'])->name('generateBulkBulanan');

        // AJAX endpoints
        Route::get('/ajax/santri-biaya-info', [TagihanBulananController::class, 'getSantriBiayaInfo'])->name('getSantriBiayaInfo');
        Route::get('/ajax/santri-yearly-data', [TagihanBulananController::class, 'getSantriYearlyData'])->name('getSantriYearlyData');
        Route::get('/ajax/available-months', [TagihanBulananController::class, 'getAvailableMonths'])->name('getAvailableMonths');

        // Export
        Route::get('/export/excel', [TagihanBulananController::class, 'export'])->name('export');

        // Payment
        Route::post('/{id}/payment', [TagihanBulananController::class, 'createPayment'])->name('createPayment');
        Route::post('/payment/handle-overpayment', [TagihanBulananController::class, 'handleOverpayment'])->name('handleOverpayment');
    });

    // Pembayaran
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        // List santri - kasir & admin can access
        Route::get('/', [PembayaranController::class, 'index'])
            ->name('index')
            ->middleware('permission:pembayaran.list');
        // Show payment form for specific santri
        Route::get('/santri/{santri}', [PembayaranController::class, 'show'])
            ->name('show')
            ->middleware('permission:pembayaran.create');
        // Preview payment allocation
        Route::post('/preview', [PembayaranController::class, 'preview'])
            ->name('preview')
            ->middleware('permission:pembayaran.create');
        // Process payment
        Route::post('/store', [PembayaranController::class, 'store'])
            ->name('store')
            ->middleware('permission:pembayaran.create');
        // Show receipt - multiple roles can view
        Route::get('/receipt/{id}', [PembayaranController::class, 'receipt'])
            ->name('receipt')
            ->middleware('permission:pembayaran.view');
        // Print receipt
        Route::get('/receipt/{id}/print', [PembayaranController::class, 'printReceipt'])
            ->name('print-receipt')
            ->middleware('permission:pembayaran.view');
        // Payment history
        Route::get('/history', [PembayaranController::class, 'history'])
            ->name('history')
            ->middleware('permission:pembayaran.history');
    });

    // Void routes - admin only
    Route::prefix('pembayaran/void')->name('pembayaran.void.')
        ->middleware('permission:pembayaran.void')
        ->group(function () {
            // Show void confirmation
            Route::get('/{id}', [PembayaranVoidController::class, 'show'])->name('show');
            // Get void modal
            Route::get('/{id}/modal', [PembayaranVoidController::class, 'voidModal'])->name('modal');
            // Process void
            Route::post('/{id}', [PembayaranVoidController::class, 'void'])->name('process');
            Route::get('/{id}/info', function ($id) {
                $pembayaran = \App\Models\Pembayaran::with('voidedBy')->findOrFail($id);

                return response()->json([
                    'voided_by_name' => $pembayaran->voidedBy->name ?? 'Unknown',
                    'voided_at' => $pembayaran->voided_at->format('d/m/Y H:i:s'),
                    'void_reason' => $pembayaran->void_reason
                ]);
            })->name('pembayaran.void.info')->middleware('permission:pembayaran.list');
        });

    // Bulk payment routes - admin only
    Route::prefix('pembayaran/bulk')->name('pembayaran.bulk.')
        ->middleware('permission:pembayaran.bulk')
        ->group(function () {
            // Bulk payment form
            Route::get('/', [PembayaranBulkController::class, 'index'])->name('index');
            // Process bulk payment
            Route::post('/process', [PembayaranBulkController::class, 'process'])->name('process');
            // Import form
            Route::get('/import', [PembayaranBulkController::class, 'importForm'])->name('import');
            // Download template
            Route::get('/template', [PembayaranBulkController::class, 'downloadTemplate'])->name('template');
            // Process import
            Route::post('/import', [PembayaranBulkController::class, 'import'])->name('import.process');
            // Preview import
            Route::post('/import/preview', [PembayaranBulkController::class, 'previewImport'])->name('import.preview');
        });

    // Biaya Terjadwal
    Route::get('biaya-terjadwal', [BiayaTerjadwalController::class, 'index'])->name('biaya_terjadwal.index')->middleware('permission:view_biaya_terjadwal');
    Route::get('biaya-terjadwal/create', [BiayaTerjadwalController::class, 'create'])->name('biaya_terjadwal.create')->middleware('permission:create_biaya_terjadwal');
    Route::post('biaya-terjadwal', [BiayaTerjadwalController::class, 'store'])->name('biaya_terjadwal.store')->middleware('permission:create_biaya_terjadwal');
    Route::get('biaya-terjadwal/{id}/edit', [BiayaTerjadwalController::class, 'edit'])->name('biaya_terjadwal.edit')->middleware('permission:edit_biaya_terjadwal');
    Route::put('biaya-terjadwal/{id}', [BiayaTerjadwalController::class, 'update'])->name('biaya_terjadwal.update')->middleware('permission:edit_biaya_terjadwal');
    Route::delete('biaya-terjadwal/{id}', [BiayaTerjadwalController::class, 'destroy'])->name('biaya_terjadwal.destroy')->middleware('permission:delete_biaya_terjadwal');

    // Kelas
    Route::get('kelas', [KelasController::class, 'index'])->name('kelas.index')->middleware('permission:kelas.view');
    Route::get('kelas/create', [KelasController::class, 'create'])->name('kelas.create')->middleware('permission:kelas.create');
    Route::post('kelas', [KelasController::class, 'store'])->name('kelas.store')->middleware('permission:kelas.create');
    Route::get('kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit')->middleware('permission:kelas.edit');
    Route::put('kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update')->middleware('permission:kelas.edit');
    Route::delete('kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy')->middleware('permission:kelas.delete');

    // Mapel Kelas
    Route::get('mapel-kelas', [MapelKelasController::class, 'index'])->name('mapel_kelas.index')->middleware('permission:mapel-kelas.view');
    Route::get('mapel-kelas/create', [MapelKelasController::class, 'create'])->name('mapel_kelas.create')->middleware('permission:mapel-kelas.create');
    Route::post('mapel-kelas', [MapelKelasController::class, 'store'])->name('mapel_kelas.store')->middleware('permission:mapel-kelas.create');
    Route::get('mapel-kelas/{mapelKelas}/edit', [MapelKelasController::class, 'edit'])->name('mapel_kelas.edit')->middleware('permission:mapel-kelas.edit');
    Route::put('mapel-kelas/{mapelKelas}', [MapelKelasController::class, 'update'])->name('mapel_kelas.update')->middleware('permission:mapel-kelas.edit');
    Route::delete('mapel-kelas/{mapelKelas}', [MapelKelasController::class, 'destroy'])->name('mapel_kelas.destroy')->middleware('permission:mapel-kelas.delete');

    // Tahun Ajar
    Route::get('tahun-ajar', [TahunAjarController::class, 'index'])->name('tahun_ajar.index')->middleware('permission:tahun-ajar.view');
    Route::get('tahun-ajar/create', [TahunAjarController::class, 'create'])->name('tahun_ajar.create')->middleware('permission:tahun-ajar.create');
    Route::post('tahun-ajar', [TahunAjarController::class, 'store'])->name('tahun_ajar.store')->middleware('permission:tahun-ajar.create');
    Route::get('tahun-ajar/{tahunAjar}/edit', [TahunAjarController::class, 'edit'])->name('tahun_ajar.edit')->middleware('permission:tahun-ajar.edit');
    Route::put('tahun-ajar/{tahunAjar}', [TahunAjarController::class, 'update'])->name('tahun_ajar.update')->middleware('permission:tahun-ajar.edit');
    Route::delete('tahun-ajar/{tahunAjar}', [TahunAjarController::class, 'destroy'])->name('tahun_ajar.destroy')->middleware('permission:tahun-ajar.delete');

    // Mata Pelajaran
    Route::get('mapel', [MataPelajaranController::class, 'index'])->name('mapel.index')->middleware('permission:mapel.view');
    Route::get('mapel/create', [MataPelajaranController::class, 'create'])->name('mapel.create')->middleware('permission:mapel.create');
    Route::post('mapel', [MataPelajaranController::class, 'store'])->name('mapel.store')->middleware('permission:mapel.create');
    Route::get('mapel/{mataPelajaran}/edit', [MataPelajaranController::class, 'edit'])->name('mapel.edit')->middleware('permission:mapel.edit');
    Route::put('mapel/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('mapel.update')->middleware('permission:mapel.edit');
    Route::delete('mapel/{mataPelajaran}', [MataPelajaranController::class, 'destroy'])->name('mapel.destroy')->middleware('permission:mapel.delete');

    // Ustadz
    Route::get('ustadz', [PenugasanUstadzController::class, 'getUstadzs'])->name('ustadz.get')->middleware('permission:ustadz.view');
    Route::get('ustadz/add', [PenugasanUstadzController::class, 'addUstadz'])->name('ustadz.add')->middleware('permission:ustadz.create');
    Route::post('ustadz/add', [PenugasanUstadzController::class, 'storeUstadz'])->name('ustadz.store')->middleware('permission:ustadz.create');

    // Penugasan Ustadz
    Route::get('ustadz/penugasan', [PenugasanUstadzController::class, 'index'])->name('ustadz.penugasan.index')->middleware('permission:penugasan-ustadz.view');
    Route::get('ustadz/penugasan/get-wali', [PenugasanUstadzController::class, 'getWaliKelas'])->name('ustadz.penugasan.getWaliKelas')->middleware('permission:penugasan-ustadz.view');
    Route::get('ustadz/penugasan/get-qori', [PenugasanUstadzController::class, 'getQori'])->name('ustadz.penugasan.getQori')->middleware('permission:penugasan-ustadz.view');

    // Penugasan Qori
    Route::get('ustadz/penugasan/qori/create', [PenugasanUstadzController::class, 'createQori'])->name('ustadz.penugasan.qori.create')->middleware('permission:penugasan-ustadz.create');
    Route::get('ustadz/penugasan/qori/get-pelajaran', [PenugasanUstadzController::class, 'getPelajaran'])->name('ustadz.penugasan.qori.getPelajaran')->middleware('permission:penugasan-ustadz.view');
    Route::post('ustadz/penugasan/qori', [PenugasanUstadzController::class, 'storeQori'])->name('ustadz.penugasan.qori.store')->middleware('permission:penugasan-ustadz.create');

    // Penugasan Wali Kelas
    Route::get('ustadz/penugasan/mustahiq/create', [PenugasanUstadzController::class, 'createMustahiq'])->name('ustadz.penugasan.mustahiq.create')->middleware('permission:penugasan-ustadz.create');
    Route::get('ustadz/penugasan/mustahiq/get-kelas', [PenugasanUstadzController::class, 'getKelas'])->name('ustadz.penugasan.mustahiq.getKelas')->middleware('permission:penugasan-ustadz.view');
    Route::post('ustadz/penugasan/mustahiq', [PenugasanUstadzController::class, 'storeMustahiq'])->name('ustadz.penugasan.mustahiq.store')->middleware('permission:penugasan-ustadz.create');

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

    // Roles Management
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:roles.view');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.edit');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');

    //Permission Management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:permissions.view');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create')->middleware('permission:permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:permissions.create');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:permissions.edit');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:permissions.delete');

    //Qori Kelas
    Route::get('/qori_kelas', [QoriKelasController::class, 'index'])->name('qori_kelas.index')->middleware('permission:qori-kelas.view');
    Route::get('/qori_kelas/create', [QoriKelasController::class, 'create'])->name('qori_kelas.create')->middleware('permission:qori-kelas.create');
    Route::post('/qori_kelas', [QoriKelasController::class, 'store'])->name('qori_kelas.store')->middleware('permission:qori-kelas.create');
    Route::get('/qori_kelas/{id}/edit', [QoriKelasController::class, 'edit'])->name('qori_kelas.edit')->middleware('permission:qori-kelas.edit');
    Route::put('/qori_kelas/{id}', [QoriKelasController::class, 'update'])->name('qori_kelas.update')->middleware('permission:qori-kelas.edit');
    Route::delete('qori_kelas/{id}', [QoriKelasController::class, 'destroy'])
        ->name('qori_kelas.destroy')->middleware('permission:qori-kelas.delete');
    Route::post('/qori_kelas/generate', [QoriKelasController::class, 'generateFromSantri'])->name('qori_kelas.generate')->middleware('permission:qori-kelas.create');
    Route::post('qori_kelas/{id}/toggle-status', [QoriKelasController::class, 'toggleStatus'])
        ->name('qori_kelas.toggle-status')->middleware('permission:qori-kelas.edit');

    // Biaya Santri
    Route::get('biaya-santris', [BiayaSantriController::class, 'index'])->name('biaya-santris.index')->middleware('permission:biaya-santri.view');
    Route::get('biaya-santris/create', [BiayaSantriController::class, 'create'])->name('biaya-santris.create')->middleware('permission:biaya-santri.create');
    Route::post('biaya-santris', [BiayaSantriController::class, 'store'])->name('biaya-santris.store')->middleware('permission:biaya-santri.create');
    Route::get('biaya-santris/{id}', [BiayaSantriController::class, 'show'])->name('biaya-santris.show')->middleware('permission:biaya-santri.view');
    Route::get('biaya-santris/{id}/edit', [BiayaSantriController::class, 'edit'])->name('biaya-santris.edit')->middleware('permission:biaya-santri.edit');
    Route::put('biaya-santris/{id}', [BiayaSantriController::class, 'update'])->name('biaya-santris.update')->middleware('permission:biaya-santri.edit');
    Route::delete('biaya-santris/{id}', [BiayaSantriController::class, 'destroy'])->name('biaya-santris.destroy')->middleware('permission:biaya-santri.delete');

    Route::get('/search-santri', [BiayaSantriController::class, 'searchSantri'])->name('biaya-santris.search-santri');
    Route::get('/search-biaya', [BiayaSantriController::class, 'searchBiaya'])->name('biaya-santris.search-biaya');

    // Daftar Biaya
    Route::get('/daftar-biayas', [DaftarBiayaController::class, 'index'])->name('daftar-biayas.index')->middleware('permission:daftar-biaya.view');
    Route::get('/daftar-biayas/create', [DaftarBiayaController::class, 'create'])->name('daftar-biayas.create')->middleware('permission:daftar-biaya.create');
    Route::post('/daftar-biayas', [DaftarBiayaController::class, 'store'])->name('daftar-biayas.store')->middleware('permission:daftar-biaya.create');
    Route::get('/daftar-biayas/{id}/edit', [DaftarBiayaController::class, 'edit'])->name('daftar-biayas.edit')->middleware('permission:daftar-biaya.edit');
    Route::put('/daftar-biayas/{id}', [DaftarBiayaController::class, 'update'])->name('daftar-biayas.update')->middleware('permission:daftar-biaya.edit');
    Route::delete('/daftar-biayas/{id}', [DaftarBiayaController::class, 'destroy'])->name('daftar-biayas.destroy')->middleware('permission:daftar-biaya.delete');
    Route::get('daftar-biayas/data', [DaftarBiayaController::class, 'data'])->name('daftar-biayas.data');
    Route::get('daftar-biayas/get-categories', [DaftarBiayaController::class, 'getCategoriesByStatus'])->name('daftar-biayas.get-categories');

    // Kategori Biaya
    Route::get('/kategori-biayas', [KategoriBiayaController::class, 'index'])->name('kategori-biayas.index')->middleware('permission:kategori-biaya.view');
    Route::get('/kategori-biayas/create', [KategoriBiayaController::class, 'create'])->name('kategori-biayas.create')->middleware('permission:kategori-biaya.create');
    Route::post('/kategori-biayas', [KategoriBiayaController::class, 'store'])->name('kategori-biayas.store')->middleware('permission:kategori-biaya.create');
    Route::get('/kategori-biayas/{id}/edit', [KategoriBiayaController::class, 'edit'])->name('kategori-biayas.edit')->middleware('permission:kategori-biaya.edit');
    Route::put('/kategori-biayas/{id}', [KategoriBiayaController::class, 'update'])->name('kategori-biayas.update')->middleware('permission:kategori-biaya.edit');
    Route::delete('/kategori-biayas/{id}', [KategoriBiayaController::class, 'destroy'])->name('kategori-biayas.destroy')->middleware('permission:kategori-biaya.delete');

    //Riwayat Kelas
    Route::get('riwayat-kelas', [RiwayatKelasController::class, 'index'])->name('riwayat-kelas.index')->middleware('permission:riwayat-kelas.view');
    Route::get('/riwayat-kelas/create', [RiwayatKelasController::class, 'create'])->name('riwayat-kelas.create')->middleware('permission:riwayat-kelas.create');
    Route::post('/riwayat-kelas/store', [RiwayatKelasController::class, 'store'])->name('riwayat-kelas.store')->middleware('permission:riwayat-kelas.create');
    Route::get('/riwayat-kelas/{id}/edit', [RiwayatKelasController::class, 'edit'])->name('riwayat-kelas.edit')->middleware('permission:riwayat-kelas.edit');
    Route::put('/riwayat-kelas/{id}', [RiwayatKelasController::class, 'update'])->name('riwayat-kelas.update')->middleware('permission:riwayat-kelas.edit');
    Route::delete('/riwayat-kelas/{id}', [RiwayatKelasController::class, 'destroy'])->name('riwayat-kelas.destroy')->middleware('permission:riwayat-kelas.delete');
    Route::get('/riwayat-kelas/data', [RiwayatKelasController::class, 'getData'])->name('riwayat-kelas.data');
});

// Route untuk admin dan santri
Route::middleware(['auth', 'role:admin|santri'])->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    // Data Santri (Hanya untuk melihat data diri sendiri)
    Route::get('santri/{santri}', [SantriController::class, 'show'])->name('santri.show')->middleware('permission:santri.view');

    // Tambahan Bulanan Santri
    Route::get('tambahan-bulanan/item-santri', [TambahanBulananController::class, 'itemSantri'])->name('tambahan_bulanan.item_santri')->middleware('permission:item-santri.view');

    // Tagihan Terjadwal Santri
    Route::get('tagihan-terjadwal', [TagihanTerjadwalController::class, 'index'])->name('tagihan_terjadwal.index')->middleware('permission:tagihan-terjadwal.view');

    // Tagihan Bulanan Santri
    Route::get('tagihan-bulanan', [TagihanBulananController::class, 'index'])->name('tagihan_bulanan.index')->middleware('permission:tagihan-bulanan.view');

    // Riwayat Pembayaran Santri
    Route::get('pembayaran/riwayat', [PembayaranController::class, 'riwayat'])->name('pembayaran.riwayat')->middleware('permission:pembayaran.history');

    // Absensi
    Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index')->middleware('permission:view_absensi');
    Route::get('absensi/data', [AbsensiController::class, 'getAbsensi'])->name('absensi.data')->middleware('permission:view_absensi');

    // Profile Santri
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('permission:profile.view');
    Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update_password')->middleware('permission:profile.edit');
});

// Route untuk admin dan ustadz
Route::middleware(['auth', 'role:admin|ustadz'])->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    // Data Santri (Ustadz bisa lihat santri yang diajar)
    Route::get('santri', [SantriController::class, 'index'])->name('santri.index')->middleware('permission:santri.view');
    Route::get('santri/data', [SantriController::class, 'getSantri'])->name('santri.data')->middleware('permission:santri.view');
    Route::get('santri/{santri}', [SantriController::class, 'show'])->name('santri.show')->middleware('permission:santri.view');

    // Tagihan Terjadwal (Ustadz bisa lihat data santri yang diajar)
    Route::get('tagihan-terjadwal', [TagihanTerjadwalController::class, 'index'])->name('tagihan_terjadwal.index')->middleware('permission:tagihan-terjadwal.view');

    // Tagihan Bulanan (Ustadz bisa lihat data santri yang diajar)
    Route::get('tagihan-bulanan', [TagihanBulananController::class, 'index'])->name('tagihan_bulanan.index')->middleware('permission:tagihan-bulanan.view');
    Route::get('tagihan-bulanan/{id}', [TagihanBulananController::class, 'show'])->name('tagihan_bulanan.show')->middleware('permission:tagihan-bulanan.view');

    // Pembayaran (Ustadz bisa lihat data santri yang diajar)
    Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index')->middleware('permission:pembayaran.list');
    Route::get('pembayaran/santri/{santri}', [PembayaranController::class, 'show'])->name('pembayaran.show')->middleware('permission:pembayaran.create');
    Route::get('pembayaran/history', [PembayaranController::class, 'history'])->name('pembayaran.history')->middleware('permission:pembayaran.history');

    // Academic data (Ustadz bisa akses data akademik)
    Route::get('kelas', [KelasController::class, 'index'])->name('kelas.index')->middleware('permission:kelas.view');
    Route::get('mapel', [MataPelajaranController::class, 'index'])->name('mapel.index')->middleware('permission:mapel.view');
    Route::get('mapel-kelas', [MapelKelasController::class, 'index'])->name('mapel_kelas.index')->middleware('permission:mapel-kelas.view');
    Route::get('tahun-ajar', [TahunAjarController::class, 'index'])->name('tahun_ajar.index')->middleware('permission:tahun-ajar.view');
    Route::get('qori_kelas', [QoriKelasController::class, 'index'])->name('qori_kelas.index')->middleware('permission:qori-kelas.view');
    Route::get('riwayat-kelas', [RiwayatKelasController::class, 'index'])->name('riwayat-kelas.index')->middleware('permission:riwayat-kelas.view');

    // Ustadz data
    Route::get('ustadz', [PenugasanUstadzController::class, 'getUstadzs'])->name('ustadz.get')->middleware('permission:ustadz.view');
    Route::get('ustadz/penugasan', [PenugasanUstadzController::class, 'index'])->name('ustadz.penugasan.index')->middleware('permission:penugasan-ustadz.view');

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('permission:profile.view');
    Route::post('profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update_password')->middleware('permission:profile.edit');
});

Route::get('tulisan', function () {
    return view('tulisan');
})->middleware(['auth', 'verified', 'role:santri|admin']);

require __DIR__ . '/auth.php';
