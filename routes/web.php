<?php

use App\Http\Controllers\SantriController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/data-santri', [SantriController::class, 'showSantri']);
