<?php

// ============================================================
// A AJOUTER / FUSIONNER dans routes/web.php
// ============================================================

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookCatalogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/{locale}', [HomeController::class, 'index'])
    ->whereIn('locale', ['fr', 'mg', 'en']) // mg inclus : corrige le switch de langue
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

Route::get('/vendeur/livres/{book}/modifier', [BookController::class, 'edit'])
    ->middleware('auth')
    ->name('books.edit');

Route::put('/vendeur/livres/{book}', [BookController::class, 'update'])
    ->middleware('auth')
    ->name('books.update');

Route::delete('/vendeur/livres/{book}', [BookController::class, 'destroy'])
    ->middleware('auth')
    ->name('books.destroy');

// Catalogue public + recherche (barre de recherche du header, menu "Livres")
Route::get('/livres', [BookCatalogController::class, 'index'])->name('books.index');

// Pages vendeurs : liste, puis fiche d'un vendeur (tous ses livres)
Route::get('/vendeur', [SellerController::class, 'index'])->name('sellers.index');
Route::get('/vendeur/{seller}', [SellerController::class, 'show'])->name('sellers.show');

// Pages statiques (À propos / Confidentialité / CGV)
Route::view('/a-propos', 'pages.about')->name('pages.about');
Route::view('/confidentialite', 'pages.privacy')->name('pages.privacy');
Route::view('/cgv', 'pages.terms')->name('pages.terms');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/connexion', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/deconnexion', [AdminAuthController::class, 'logout'])->name('logout');
 
    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/utilisateurs', [AdminUserController::class, 'index'])->name('users.index');
 
        // Pages pas encore construites (contenu à venir, voir demande du client)
        Route::view('/commandes', 'admin.commandes')->name('commandes');
        Route::view('/paiements', 'admin.paiements')->name('paiements');
        Route::view('/parametres', 'admin.parametres')->name('parametres');
    });
});