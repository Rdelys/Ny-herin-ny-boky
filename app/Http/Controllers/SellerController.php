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
}