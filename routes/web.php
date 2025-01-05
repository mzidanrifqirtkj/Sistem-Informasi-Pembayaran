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



