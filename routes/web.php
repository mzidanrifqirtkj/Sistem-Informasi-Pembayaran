<?php

use App\Http\Controllers\SantriController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/data-santri', [SantriController::class, 'showSantris']);
Route::get('/santri/import', [SantriController::class, 'importForm'])->name('santri.importForm');
Route::post('/santri/import', [SantriController::class, 'import'])->name('santri.import');

Route::resource('santri', SantriController::class);



