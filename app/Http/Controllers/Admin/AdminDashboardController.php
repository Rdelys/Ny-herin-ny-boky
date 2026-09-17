<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // ---- Utilisateurs & catalogue ----
        $totalUsers = User::count();
        $totalClients = User::where('role', 'client')->count();
        $totalSellers = User::where('role', 'vendeur')->count();
        $totalBooks = Book::count();
        $totalStock = (int) Book::sum('quantite');

        // Valeur potentielle du catalogue au prix vendeur (SANS la commission,
        // qui n'est ajoutée qu'à l'affichage côté acheteur).
        $catalogPotentialValue = (int) Book::query()
            ->selectRaw('COALESCE(SUM(prix_achat * quantite), 0) as total')
            ->value('total');

        // ---- Argent : une commande annulée ne compte nulle part ----
        $argent = Order::query()
            ->facturables()
            ->selectRaw('COALESCE(SUM(total), 0) as encaisse')
            ->selectRaw('COALESCE(SUM(montant_vendeur), 0) as du_vendeurs')
            ->selectRaw('COALESCE(SUM(total - montant_vendeur), 0) as benefice')
            ->selectRaw('SUM(CASE WHEN paiement_vendeur = ? THEN montant_vendeur ELSE 0 END) as argent_du', [Order::PAIEMENT_DU])
            ->selectRaw('SUM(CASE WHEN paiement_vendeur = ? THEN montant_vendeur ELSE 0 END) as argent_envoye', [Order::PAIEMENT_ENVOYE])
            ->first();

        $compteursStatut = Order::query()
            ->selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $compteursPaiement = Order::query()
            ->facturables()
            ->selectRaw('paiement_vendeur, count(*) as total')
            ->groupBy('paiement_vendeur')
            ->pluck('total', 'paiement_vendeur');

        // ---- Commandes des 7 derniers jours (agrégé en PHP : volumes
        // faibles, et ça reste indépendant du moteur SQL) ----
        $recentes = Order::query()
            ->facturables()
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get(['created_at', 'total']);

        $joursLabels = [];
        $joursCommandes = [];
        $joursRevenus = [];

        foreach (range(6, 0) as $i) {
            $jour = now()->subDays($i);
            $duJour = $recentes->filter(fn ($o) => $o->created_at->isSameDay($jour));

            $joursLabels[] = $jour->translatedFormat('D d/m');
            $joursCommandes[] = $duJour->count();
            $joursRevenus[] = (int) $duJour->sum('total');
        }

        // ---- Top vendeurs par chiffre d'affaires réel ----
        $topVendeurs = Order::query()
            ->facturables()
            ->selectRaw('seller_id, SUM(total) as ca, SUM(montant_vendeur) as part_vendeur, COUNT(*) as commandes')
            ->groupBy('seller_id')
            ->orderByDesc('ca')
            ->with('seller.sellerProfile')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalClients' => $totalClients,
            'totalSellers' => $totalSellers,
            'totalBooks' => $totalBooks,
            'totalStock' => $totalStock,
            'catalogPotentialValue' => $catalogPotentialValue,

            'totalOrders' => (int) $compteursStatut->sum(),
            'compteursStatut' => $compteursStatut,
            'compteursPaiement' => $compteursPaiement,

            'encaisse' => (int) $argent->encaisse,
            'benefice' => (int) $argent->benefice,
            'argentDu' => (int) $argent->argent_du,
            'argentEnvoye' => (int) $argent->argent_envoye,
            'commissionRate' => Setting::commissionRate(),

            'joursLabels' => $joursLabels,
            'joursCommandes' => $joursCommandes,
            'joursRevenus' => $joursRevenus,
            'topVendeurs' => $topVendeurs,
        ]);
    }
}
