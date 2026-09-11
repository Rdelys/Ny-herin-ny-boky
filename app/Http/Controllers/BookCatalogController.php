<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookCatalogController extends Controller
{
    /**
     * Page "Livres" (/livres) : catalogue complet, avec recherche par mot-clé
     * (titre / auteur / catégorie) et filtre par catégorie exacte.
     * Alimente aussi la barre de recherche du header.
     */
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $categorie = $request->query('categorie', '');

        $books = Book::query()
            ->with(['seller.sellerProfile'])
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($inner) use ($query) {
                    $inner->where('titre', 'like', "%{$query}%")
                        ->orWhere('auteur', 'like', "%{$query}%")
                        ->orWhere('categorie', 'like', "%{$query}%");
                });
            })
            ->when($categorie !== '', fn ($q) => $q->where('categorie', $categorie))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('books.index', [
            'books' => $books,
            'query' => $query,
            'categorie' => $categorie,
            'categories' => BookController::CATEGORIES,
        ]);
    }
}