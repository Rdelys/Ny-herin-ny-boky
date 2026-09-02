<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/{locale}', [HomeController::class, 'index'])
    ->whereIn('locale', ['mg', 'en'])
    ->name('home.locale');

Route::post('/connexion', [AuthController::class, 'login'])->name('login');
Route::post('/inscription/client', [AuthController::class, 'registerClient'])->name('register.client');
Route::post('/inscription/vendeur', [AuthController::class, 'registerSeller'])->name('register.seller');
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
 
Route::get('/profil', [ProfileController::class, 'show'])
    ->middleware('auth')
    ->name('profile');
 
Route::post('/vendeur/livres', [BookController::class, 'store'])
    ->middleware('auth')
    ->name('books.store');
 
Route::delete('/vendeur/livres/{book}', [BookController::class, 'destroy'])
    ->middleware('auth')
    ->name('books.destroy');
 
// Route::get('/vendeur', [VendeurController::class, 'index'])->name('vendeur');