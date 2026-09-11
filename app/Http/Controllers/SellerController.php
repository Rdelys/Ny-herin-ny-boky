<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class SellerController extends Controller
{
    /**
     * Page publique listant tous les vendeurs (lien "Vendeur" du menu).
     */
    public function index(): View
    {
        $sellers = User::query()
            ->where('role', 'vendeur')
            ->with('sellerProfile')
            ->withCount('books')
            ->latest()
            ->paginate(12);

        return view('sellers.index', ['sellers' => $sellers]);
    }

    /**
     * Page publique d'un vendeur : ses infos + tous ses livres, paginés.
     */
    public function show(User $seller): View
    {
        abort_unless($seller->role === 'vendeur', 404);

        $books = $seller->books()->latest()->paginate(12);

        return view('sellers.show', ['seller' => $seller, 'books' => $books]);
    }
}