<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/{locale}', [HomeController::class, 'index'])
    ->whereIn('locale', ['fr', 'en'])
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


Route::get('/vendeur/livres/{book}/modifier', [BookController::class, 'edit'])
    ->middleware('auth')
    ->name('books.edit');

Route::put('/vendeur/livres/{book}', [BookController::class, 'update'])
    ->middleware('auth')
    ->name('books.update');

// Page publique listant tous les vendeurs (lien "Vendeur" du menu)
Route::get('/vendeur', [SellerController::class, 'index'])->name('sellers.index');

// Route::get('/vendeur', [VendeurController::class, 'index'])->name('vendeur');
Route::view('/a-propos', 'pages.about')->name('pages.about');
Route::view('/confidentialite', 'pages.privacy')->name('pages.privacy');
Route::view('/cgv', 'pages.terms')->name('pages.terms');