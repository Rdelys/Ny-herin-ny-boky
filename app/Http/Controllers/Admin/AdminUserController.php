<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Liste de tous les utilisateurs (clients + vendeurs), avec leur
     * nombre de livres publiés — donnée réelle (withCount).
     */
    public function index(Request $request): View
    {
        $role = $request->query('role');
        $roleActif = in_array($role, ['client', 'vendeur'], true) ? $role : null;
        $recherche = trim((string) $request->query('q', ''));

        $users = User::query()
            ->withCount('books')
            ->when($roleActif, fn ($q) => $q->where('role', $roleActif))
            ->when($recherche !== '', function ($q) use ($recherche) {
                $q->where(function ($sub) use ($recherche) {
                    $sub->where('name', 'like', '%' . $recherche . '%')
                        ->orWhere('email', 'like', '%' . $recherche . '%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roleActif' => $roleActif,
            'recherche' => $recherche,
            'compteurs' => User::query()
                ->selectRaw('role, count(*) as total')
                ->groupBy('role')
                ->pluck('total', 'role'),
        ]);
    }

    /**
     * Fiche d'un utilisateur. Pour un vendeur : tous ses livres et le
     * détail de ce que la plateforme lui doit. Pour un client : ses
     * commandes.
     */
    public function show(User $user): View
    {
        $user->load('sellerProfile', 'buyerProfile');

        $estVendeur = $user->isSeller();

        $books = $estVendeur
            ? $user->books()->latest()->paginate(12)
            : null;

        $orders = $estVendeur
            ? $user->sales()->with(['buyer', 'deliverer'])->latest()->take(20)->get()
            : $user->orders()->with(['seller.sellerProfile', 'deliverer'])->latest()->take(20)->get();

        $argent = Order::query()
            ->facturables()
            ->where($estVendeur ? 'seller_id' : 'buyer_id', $user->id)
            ->selectRaw('COALESCE(SUM(total), 0) as total')
            ->selectRaw('COALESCE(SUM(montant_vendeur), 0) as part_vendeur')
            ->selectRaw('SUM(CASE WHEN paiement_vendeur = ? THEN montant_vendeur ELSE 0 END) as argent_du', [Order::PAIEMENT_DU])
            ->selectRaw('SUM(CASE WHEN paiement_vendeur = ? THEN montant_vendeur ELSE 0 END) as argent_envoye', [Order::PAIEMENT_ENVOYE])
            ->first();

        return view('admin.users.show', [
            'user' => $user,
            'estVendeur' => $estVendeur,
            'books' => $books,
            'orders' => $orders,
            'totalCommandes' => (int) $argent->total,
            'argentDu' => (int) $argent->argent_du,
            'argentEnvoye' => (int) $argent->argent_envoye,
        ]);
    }
}
