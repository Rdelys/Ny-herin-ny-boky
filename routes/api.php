<?php

// ============================================================
// routes/api.php — API pour l'application mobile (acheteurs uniquement)
//
// Toutes les routes sont préfixées automatiquement par /api (voir
// bootstrap/app.php : withRouting(api: __DIR__.'/../routes/api.php', ...)).
// Avec l'URL de base https://boky.tafely-test.online/, une route comme
// Route::get('/books', ...) est donc accessible sur :
//   https://boky.tafely-test.online/api/books
//
// Authentification : Laravel Sanctum (jetons Bearer). Voir le README
// d'installation fourni à part pour les étapes composer + migration.
// ============================================================

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques (pas besoin d'être connecté)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']);
Route::get('/categories', [BookController::class, 'categories']);
Route::get('/payment-accounts', [BookController::class, 'paymentAccounts']);

/*
|--------------------------------------------------------------------------
| Routes protégées (jeton Sanctum requis) — réservées aux acheteurs
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'client.api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
});
