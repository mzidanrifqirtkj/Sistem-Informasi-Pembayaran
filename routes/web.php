<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SantriController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/data-santri', [SantriController::class, 'showSantri']);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile');

Route::get('/data_santri', [SantriController::class, 'index'])->name('admin.data_santri');
Route::get('/santri/import', [SantriController::class, 'importForm'])->name('santri.importForm');
Route::post('/santri/import', [SantriController::class, 'import'])->name('santri.import');

Route::resource('santri', SantriController::class);



