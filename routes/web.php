<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SantriController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/santri/import', [SantriController::class, 'importForm'])->name('santri.importForm');
Route::post('/santri/import', [SantriController::class, 'import'])->name('santri.import');

Route::resource('santri', SantriController::class);



