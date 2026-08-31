<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/{locale}', [HomeController::class, 'index'])
    ->whereIn('locale', ['mg', 'en'])
    ->name('home.locale');

// Route::get('/vendeur', [VendeurController::class, 'index'])->name('vendeur');