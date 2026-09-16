<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // ---- Chiffres réels, calculés depuis la base ----
        $totalUsers = User::count();
        $totalClients = User::where('role', 'client')->count();
        $totalSellers = User::where('role', 'vendeur')->count();
        $totalBooks = Book::count();
        $totalStock = (int) Book::sum('quantite');

        // Valeur potentielle du catalogue au prix vendeur (SANS la commission
        // de 10%, qui n'est ajoutée qu'à l'affichage côté acheteur).
        $catalogPotentialValue = (int) Book::query()
            ->selectRaw('COALESCE(SUM(prix_achat * quantite), 0) as total')
            ->value('total');

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalClients' => $totalClients,
            'totalSellers' => $totalSellers,
            'totalBooks' => $totalBooks,
            'totalStock' => $totalStock,
            'catalogPotentialValue' => $catalogPotentialValue,

            // ---- Aperçus statiques : nécessitent un système de suivi des
            // connexions / vues / commandes / paiements qui n'existe pas
            // encore. A brancher sur de vraies données une fois confirmé. ----
            'previewLoginsPerUser' => [
                ['name' => 'Miora R.', 'count' => 24],
                ['name' => 'Fenosoa A.', 'count' => 18],
                ['name' => 'Tolotra H.', 'count' => 15],
                ['name' => 'Dauphin-Elys Ra.', 'count' => 11],
                ['name' => 'Hery M.', 'count' => 7],
            ],
            'previewSiteViews' => 4820,
            'previewPendingPayment' => 312000,
            'previewPaidOut' => 1284000,
            'previewProfit' => 156400,
            'previewOrdersPaid' => 47,
            'previewOrdersPending' => 9,
            'previewWeeklyViews' => [420, 380, 510, 460, 600, 540, 610],
        ]);
    }
}