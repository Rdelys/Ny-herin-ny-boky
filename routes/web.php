<?php

// ============================================================
// A AJOUTER / FUSIONNER dans routes/web.php
// ============================================================

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookCatalogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDelivererController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\NewsletterController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/{locale}', [HomeController::class, 'index'])
    ->whereIn('locale', ['fr', 'mg', 'en']) // mg inclus : corrige le switch de langue
    ->name('home.locale');

Route::post('/connexion', [AuthController::class, 'login'])->name('login');
Route::post('/inscription/client', [AuthController::class, 'registerClient'])->name('register.client');
Route::post('/inscription/vendeur', [AuthController::class, 'registerSeller'])->name('register.seller');
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

// Public, juste après la route logout par exemple
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.subscribe');


// SEO : sitemap et robots.txt générés depuis la base (fiches vendeurs
// incluses automatiquement).
Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/profil', [ProfileController::class, 'show'])
    ->middleware('auth')
    ->name('profile');

// Onglet « Modifier mon profil » de l'espace vendeur
Route::put('/profil/vendeur', [ProfileController::class, 'updateSeller'])
    ->middleware('auth')
    ->name('profile.seller.update');

    // À côté des routes /profil existantes
Route::put('/profil/client', [ProfileController::class, 'updateClient'])
    ->middleware('auth')
    ->name('profile.client.update');

Route::put('/profil/mot-de-passe', [ProfileController::class, 'updatePassword'])
    ->middleware('auth')
    ->name('profile.password.update');

Route::delete('/profil', [ProfileController::class, 'destroyAccount'])
    ->middleware('auth')
    ->name('profile.destroy');
    
// Passage de commande depuis la modal (client connecté uniquement)
Route::post('/commandes', [OrderController::class, 'store'])->name('orders.store');

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
        Route::get('/utilisateurs/{user}', [AdminUserController::class, 'show'])->name('users.show');

        // Commandes = suivi des commandes payées (référence à vérifier) et
        // pilotage du statut de livraison.
        Route::get('/commandes', [AdminOrderController::class, 'index'])->name('commandes');
        Route::post('/commandes/{order}/statut', [AdminOrderController::class, 'updateStatus'])
            ->name('commandes.statut');

        // Paiements = reversement de l'argent aux vendeurs (dû / envoyé).
        Route::get('/paiements', [AdminPaymentController::class, 'index'])->name('paiements');
        Route::post('/paiements/{order}/reversement', [AdminPaymentController::class, 'updatePayout'])
            ->name('paiements.reversement');

        // Liste des livreurs, dans laquelle l'admin pioche pour assigner
        // une commande passée « En livraison ».
        Route::get('/livreurs', [AdminDelivererController::class, 'index'])->name('livreurs.index');
        Route::post('/livreurs', [AdminDelivererController::class, 'store'])->name('livreurs.store');
        Route::put('/livreurs/{livreur}', [AdminDelivererController::class, 'update'])->name('livreurs.update');
        Route::delete('/livreurs/{livreur}', [AdminDelivererController::class, 'destroy'])->name('livreurs.destroy');

        Route::get('/parametres', [AdminSettingsController::class, 'edit'])->name('parametres');
        Route::post('/parametres', [AdminSettingsController::class, 'update'])->name('parametres.update');
        Route::post('/parametres/paiement', [AdminSettingsController::class, 'updatePaymentAccounts'])
            ->name('parametres.paiement');

    });

    // ---- Dans le groupe admin, à l'intérieur de Route::middleware('admin')->group(...) ----
// À côté des routes /parametres existantes :
Route::get('/parametres/newsletter/export', [AdminSettingsController::class, 'exportNewsletter'])
    ->name('parametres.newsletter.export');
Route::delete('/parametres/newsletter/{subscriber}', [AdminSettingsController::class, 'destroyNewsletterSubscriber'])
    ->name('parametres.newsletter.destroy');

// Dans le groupe admin, à côté des routes /parametres/newsletter/... existantes
Route::post('/parametres/newsletter/envoyer', [AdminSettingsController::class, 'sendNewsletter'])
    ->name('parametres.newsletter.send');
    
});